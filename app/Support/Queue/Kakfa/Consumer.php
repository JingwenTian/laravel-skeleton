<?php
/**
 * Kafka 消费者.
 *
 * @copyright  eventmosh
 * @author     jingwentian
 * @license    北京活动时文化传媒有限公司
 * @dateTime:  2018/12/25 10:55
 */

namespace App\Support\Queue\Kakfa;

use RdKafka\Conf;
use RdKafka\KafkaConsumer;
use RdKafka\TopicConf;
use function class_exists;
use function extension_loaded;
use function is_array;
use function json_decode;
use function json_last_error;

/**
 * Class Consumer.
 *
 * @package App\Support\Queue\Kakfa
 *
 * @docs https://github.com/arnaud-lb/php-rdkafka
 *       https://github.com/AliwareMQ/aliware-kafka-demos/tree/master/kafka-php-demo
 *       https://arnaud-lb.github.io/php-rdkafka/phpdoc/index.html
 *
 * @example
 *
 *       $consumer = new Consumer('org_order');
 *       while (true) {
 *          $message = $consumer->consume()->getMessage();
 *          if ($consumer->hasError()) {
 *              var_dump($consumer->getError());
 *          } else {
 *              var_dump($message, $consumer->getProperties());
 *          }
 *          sleep(1);
 *       }
 */
class Consumer
{
    /**
     * @var \RdKafka\KafkaConsumer
     */
    protected $_consumer;

    /**
     * @var array
     */
    protected $_message;

    /**
     * @var array
     */
    protected $_errors;

    /**
     * @var array
     */
    protected $_properties;

    /**
     * Consumer constructor.
     *
     * @param string $subscribeKey 订阅类型
     *
     * @throws \RdKafka\Exception
     */
    public function __construct(string $subscribeKey)
    {
        if (!extension_loaded('rdkafka') || !class_exists('RdKafka')) {
            echo "PHP RdKafka extension was not installed\n";
            exit;
        }
        $options = config('queue.connections.kafka', []);
        $subscribeOptions = $options['options']['subscribe'][$subscribeKey] ?? [];
        if (!$options || !$subscribeOptions) {
            echo "kafka config needed.\n";
            exit;
        }
        // Global config
        $conf = new Conf();

        $conf->set('sasl.mechanisms', 'PLAIN');
        $conf->set('api.version.request', 'true');
        $conf->set('ssl.ca.location', $options['ssl_ca_path']);
        $conf->set('message.send.max.retries', 5);
        $conf->set('client.id', 'order-client');
        $conf->set('group.id', $subscribeOptions['consumer'] ?? '');
        $conf->set('metadata.broker.list', $options['server']);

        if (\in_array(APP_ENV, ['development', 'test'], false)) {
            $conf->set('sasl.username', $options['username']);
            $conf->set('sasl.password', $options['password']);
            $conf->set('security.protocol', 'SASL_SSL');
        }
        // Topic config
        $topicConf = new TopicConf();

        $topicConf->set('auto.offset.reset', 'smallest');
        $conf->setDefaultTopicConf($topicConf);

        // High-level consumer
        $this->_consumer = new KafkaConsumer($conf);

        // Subscribe to topic 'CID-evente-*-*'
        $topic = $subscribeOptions['topic'] ?? '';
        $this->_consumer->subscribe(is_array($topic) ? $topic : [$topic]);

        echo "Waiting for partition assignment... (make take some time when\n";
        echo "quickly re-joining the group after leaving it.)\n";
        echo 'Begin to subscribe topics: '.implode(',', $this->_consumer->getSubscription())."\n";
    }

    /**
     * 消费消息.
     *
     * @throws \InvalidArgumentException
     * @throws \RdKafka\Exception
     *
     * @return $this
     */
    public function consume(): self
    {
        $this->setError();
        $this->setProperties();
        $this->setMessage();

        $message = $this->_consumer->consume(120 * 1000);

        switch ($message->err) {
            case RD_KAFKA_RESP_ERR_NO_ERROR: // 0
                $payload = json_decode($message->payload, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $this->setMessage($payload);
                }
                $this->setProperties([
                    'err'           => $message->err,
                    'topic_name'    => $message->topic_name,
                    'partition'     => $message->partition,
                    'key'           => $message->key,
                    'offset'        => $message->offset,
                ]);
                break;
            case RD_KAFKA_RESP_ERR__PARTITION_EOF: // -191
                $this->setError(['code' => $message->err, 'message' => 'No more messages; will wait for more']);
                break;
            case RD_KAFKA_RESP_ERR__TIMED_OUT: // -185
                $this->setError(['code' => $message->err, 'message' => 'Timed out']);
                break;
            default:
                $this->setError(['code' => $message->err, 'message' => $message->errstr()]);
                break;
        }

        return $this;
    }

    /**
     * 设置订阅的消息.
     *
     * @param array $message
     */
    public function setMessage(array $message = null): void
    {
        $this->_message = $message ?? [];
    }

    /**
     * 获取订阅的消息.
     *
     * @return array
     */
    public function getMessage(): array
    {
        $messageBody = $this->_message['body'] ?? '';
        $message = json_decode($messageBody, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return $message['body'] ?? [];
        }

        return [];
    }

    /**
     * 设置异常消息.
     *
     * @param array $errors
     */
    public function setError(array $errors = null): void
    {
        $this->_errors = $errors ?? [];
    }

    /**
     * 获取异常消息.
     *
     * @return array
     */
    public function getError(): array
    {
        return $this->_errors;
    }

    /**
     * 是否有异常.
     *
     * @return bool
     */
    public function hasError(): bool
    {
        return !empty($this->_errors);
    }

    /**
     * 设置消息扩展参数.
     *
     * @param array $properties
     */
    public function setProperties(array $properties = null): void
    {
        $this->_properties = $properties ?? [];
    }

    /**
     * 获取消息扩展参数.
     *
     * @return array
     */
    public function getProperties(): array
    {
        return $this->_properties;
    }
}
