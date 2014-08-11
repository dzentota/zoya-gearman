<?php
require __DIR__ . '/../vendor/autoload.php';

$worker = new \Zoya\Gearman\Worker(new Monolog\Logger('WORKER'));

$worker->addJob(
    'test-valera',
    function ($work) {
        echo "WAIT", PHP_EOL;
        sleep(1);
        printf("My name is %s %s", $work->name, $work->number);
        return true;
    }
);

$worker->run();
