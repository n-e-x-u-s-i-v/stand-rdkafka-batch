<?php

$config = [
    'bootstrap.servers' => 'broker:19092',
];

$topic = 'test-2';

$conf = new RdKafka\Conf();
$conf->set('metadata.broker.list', $config['bootstrap.servers']);

$producer = new RdKafka\Producer($conf);
$topic = $producer->newTopic("test-2");


$data = [
    'bid',//
    'bid',//
    'object_realty',
    'object_realty',
    'bid',//
    'lawyer',//
    'order-development',//
    'deal',
    'deal',
    'bid',//
    'object_realty',
    'test2',
    'test2',
    'deal',
    'bid',//
    'object-realty',
    'lawyer',//
    'order-development',//
    'test2',
    'test2',
    'deal',
    'bid',//
];

foreach ($data as $key => $value) {
    $topic->produce(RD_KAFKA_PARTITION_UA, 0, "Message $key", $value);
    $producer->poll(0);
}

$result = $producer->flush(10000);
if (RD_KAFKA_RESP_ERR_NO_ERROR !== $result) {
    throw new \RuntimeException('Was unable to flush, messages might be lost!');
}
