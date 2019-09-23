<?php
/**
 * Kafka 生产者.
 *
 * @copyright  eventmosh
 * @author     jingwentian
 * @license    北京活动时文化传媒有限公司
 * @dateTime:  2018/12/25 12:10
 */

namespace App\Support\Queue\Kakfa;

use App\Exceptions\RuntimeException;
use function json_encode;
use RdKafka\Conf;
use RdKafka\Producer as RdKafkaProducer;
use RdKafka\TopicConf;

/**
 * Class Producer.
 *
 * @see https://arnaud-lb.github.io/php-rdkafka/phpdoc/index.html
 *
 * @package App\Support\MessageQueue\Kafka
 */
class Producer
{
    /**
     * @var \RdKafka\Producer
     */
    protected $_producer;

    /**
     * @var \RdKafka\ProducerTopic
     */
    protected $_producerTopic;

    /**
     * Produder constructor.
     *
     * @param string $publishKey
     */
    public function __construct(string $publishKey)
    {
        if (!\extension_loaded('rdkafka') || !\class_exists('RdKafka')) {
            throw new RuntimeException('PHP RdKafka extension was not installed');
        }

        $options = config('queue.connections.kafka', []);
        $publishOptions = $options['options']['publish'][$publishKey] ?? [];

        throw_if(!$options || !$publishOptions, new RuntimeException('unsupported kafka topic:'.$publishKey));

        $conf = new Conf();
        $conf->set('client.id', 'laravel-skeleton');
        $conf->set('api.version.request', 'true');
        $conf->set('message.send.max.retries', 5);
        $conf->set('socket.timeout.ms', 50);
        $conf->set('socket.blocking.max.ms', 1);

        if (\function_exists('pcntl_sigprocmask')) {
            pcntl_sigprocmask(SIG_BLOCK, [SIGIO]);
            $conf->set('internal.termination.signal', SIGIO);
        } else {
            $conf->set('queue.buffering.max.ms', 1);
        }

        $this->_producer = new RdKafkaProducer($conf);
        $this->_producer->setLogLevel(LOG_DEBUG);
        $this->_producer->addBrokers($options['server']);

        // -1 必须等所有brokers同步完成的确认 1当前服务器确认 0不确认，这里如果是0回调里的offset无返回，如果是1和-1会返回offset
        // 我们可以利用该机制做消息生产的确认，不过还不是100%，因为有可能会中途kafka服务器挂掉
        $topicConf = new TopicConf();
        $topicConf->set('request.required.acks', 0);

        $this->_producerTopic = $this->_producer->newTopic($publishOptions['topic'] ?? '', $topicConf);
        //echo 'Begin to connect kafka topic:' . $this->_producer->getName() . ' @' . date('Y-m-d H:i:s') . PHP_EOL;
    }

    /**
     * 发布消息.
     *
     * @param array       $message
     * @param string|null $messageKey
     */
    public function produce(array $message = null, string $messageKey = null): array
    {
        $payload = json_encode($message ?? []);
        // 兼容 enqueue/rdkafka 消息格式
        //$payload = \json_encode(['body' => \json_encode(['body' => $message ?? [], 'properties' => [], 'headers' => []]), 'properties' => [], 'headers' => []]);

        $this->_producerTopic->produce(RD_KAFKA_PARTITION_UA, 0, $payload, $messageKey);
        while ($this->_producer->getOutQLen() > 0) {
            $this->_producer->poll(5);
        }

        return [
            'message_id' => $messageKey,
        ];
    }
}
