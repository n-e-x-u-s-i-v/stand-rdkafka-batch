<?php

use RdKafka\Conf;
use RdKafka\Consumer;
use RdKafka\TopicConf;

$config = [
    'group.id' => 'batch-group-test',
    'bootstrap.servers' => 'broker:19092',
    'auto.offset.reset' => 'earliest',
    'enable.partition.eof' => 'false',
    'security.protocol' => 'PLAINTEXT',
];
$topic = 'test-2';
$partition = 0;

$conf = new Conf();
$conf->set('group.id', $config['group.id']);
$conf->set('bootstrap.servers', $config['bootstrap.servers']);
$conf->set('enable.partition.eof', $config['enable.partition.eof']);
$conf->set('security.protocol', $config['security.protocol']);
$consumer = new Consumer($conf);

$topicConf = new TopicConf();
$topicConf->set('auto.commit.interval.ms', 1000000);
$topicConf->set('auto.commit.enable', 'false');
$topicConf->set('auto.offset.reset', $config['auto.offset.reset']);

$topic = $consumer->newTopic($topic, $topicConf);

$topic->consumeStart($partition, RD_KAFKA_OFFSET_STORED);


while (true) {
    //80s - timeout
    $messages = $topic->consumeBatch($partition, 80e3, 20);
    $str = '';
    foreach ($messages as $message) {
        if ($message->err === RD_KAFKA_RESP_ERR_NO_ERROR) {
            $str .= $message->payload . PHP_EOL;
        }
    }
    var_dump('packet - ' . $str);
}
