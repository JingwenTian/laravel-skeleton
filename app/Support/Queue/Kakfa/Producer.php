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

use RdKafka\Conf;
use RdKafka\Producer as RdKafkaProducer;

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
            echo "PHP RdKafka extension was not installed\n";
            exit;
        }
        $options = config('queue.connections.kafka', []);
        $publishOptions = $options['options']['publish'][$publishKey] ?? [];
        if (!$options || !$publishOptions) {
            echo "kafka config '{$publishKey}' needed.\n";
            exit;
        }

        $conf = new Conf();
        //$conf->set('group.id', $publishOptions['producer'] ?? '');
        $conf->set('client.id', 'checkout-client');
        $conf->set('sasl.mechanisms', 'PLAIN');
        $conf->set('api.version.request', 'true');
        $conf->set('ssl.ca.location', $options['ssl_ca_path']);
        $conf->set('message.send.max.retries', 5);
        $conf->set('socket.timeout.ms', 50);
        $conf->set('socket.blocking.max.ms', 1);

        if (\in_array(APP_ENV, ['development', 'test'], false)) {
            $conf->set('sasl.username', $options['username']);
            $conf->set('sasl.password', $options['password']);
            $conf->set('security.protocol', 'SASL_SSL');
        }

        if (\function_exists('pcntl_sigprocmask')) {
            pcntl_sigprocmask(SIG_BLOCK, [SIGIO]);
            $conf->set('internal.termination.signal', SIGIO);
        } else {
            $conf->set('queue.buffering.max.ms', 1);
        }

        $this->_producer = new RdKafkaProducer($conf);
        $this->_producer->setLogLevel(LOG_DEBUG);
        $this->_producer->addBrokers($options['server']);
        $this->_producerTopic = $this->_producer->newTopic($publishOptions['topic'] ?? '');
        //echo 'Begin to connect kafka topic:' . $this->_producer->getName() . ' @' . date('Y-m-d H:i:s') . PHP_EOL;
    }

    /**
     * 发布消息.
     *
     * @param array       $message
     * @param string|null $messageKey
     */
    public function produce(array $message = null, string $messageKey = null): void
    {
        //$payload = \json_encode($message ?? []);
        $payload = \json_encode(['body' => \json_encode(['body' => $message ?? []])]); // 兼容原通知格式
        $this->_producerTopic->produce(RD_KAFKA_PARTITION_UA, 0, $payload, $messageKey);
        while ($this->_producer->getOutQLen() > 0) {
            $this->_producer->poll(5);
        }
    }
}
