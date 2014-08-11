<?php
use Monolog\Formatter\HtmlFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

require __DIR__ . '/../vendor/autoload.php';


$stream = new StreamHandler('php://stderr', Logger::DEBUG);
$formatter = new HtmlFormatter();
$stream->setFormatter($formatter);
$logger = new \Monolog\Logger('CLIENT');
$logger->pushHandler($stream);
$client = new \Zoya\Gearman\Client($logger);
for ($i=0; $i<5;$i++) {
    $client->addTaskToWorker('test-valera', ['name'=>'valera', 'number'=>$i]);
}

$client->runTasks();
