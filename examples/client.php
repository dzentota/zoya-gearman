<?php
require __DIR__ . '/../vendor/autoload.php';

$client = new \Zoya\Gearman\Client(new Monolog\Logger('CLIENT'));
for ($i=0; $i<5;$i++) {
    $client->addTaskToWorker('test-valera', ['name'=>'valera', 'number'=>$i]);
}

$client->runTasks();
