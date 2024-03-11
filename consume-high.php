<?php

use RdKafka\Conf;
use RdKafka\KafkaConsumer;

$config = [
    'group.id' => 'batch-group-test',
    'bootstrap.servers' => 'broker:19092',
    'auto.offset.reset' => 'earliest',
    'enable.partition.eof' => 'false',
    'security.protocol' => 'PLAINTEXT',
    'enable.auto.commit' => 'false',
];
$topic = 'test-2';
$counter_batch = 100;
$timeout = 30;
pcntl_async_signals(true);
pcntl_signal(SIGTERM, function () {
    echo 'SIGTERM' . PHP_EOL;
    exit(1);
});

pcntl_signal(SIGINT, function () {
    echo 'SIGINT' . PHP_EOL;
    exit(1);
});

pcntl_signal(SIGUSR2, function () {
    echo 'SIGUSR2' . PHP_EOL;
    exit(1);
});

pcntl_signal(SIGCONT, function () {
    echo 'SIGCONT' . PHP_EOL;
    exit(1);
});

pcntl_signal(SIGALRM, static function () use ($timeout) {
    echo 'SIGALRM' . PHP_EOL;
    pcntl_alarm($timeout);
});

pcntl_signal(SIGHUP, function () {
    echo 'SIGHUP' . PHP_EOL;
    exit(1);
});

pcntl_alarm($timeout);


$conf = new Conf();
$conf->set('group.id', $config['group.id']);
$conf->set('bootstrap.servers', $config['bootstrap.servers']);
$conf->set('enable.partition.eof', $config['enable.partition.eof']);
$conf->set('security.protocol', $config['security.protocol']);
$conf->set('enable.auto.commit', $config['enable.auto.commit']);
$consumer = new KafkaConsumer($conf);

$consumer->subscribe([$topic]);


while (true) {
    //80s - timeout
    $message = $consumer->consume(20e3);
    switch ($message->err) {
        case RD_KAFKA_RESP_ERR_NO_ERROR:
            echo $message->payload . PHP_EOL;
            $consumer->commit($message);
            break;
        case RD_KAFKA_RESP_ERR__TIMED_OUT:
            echo "Timed out\n";
            break;
        default:
            throw new \Exception($message->errstr(), $message->err);
    }
}
