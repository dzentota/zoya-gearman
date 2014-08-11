<?php

namespace Zoya\Gearman;

use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;

/**
 * Class Client
 * @package Zoya\Gearman
 */
class Client
{
    use LoggerAwareTrait;

    /**
     * @var string
     */
    protected $host;
    /**
     * @var int
     */
    protected $port;
    /**
     * @var GearmanClient
     */
    private $client;

    /**
     * @param \Psr\Log\LoggerInterface $logger
     * @param string $host
     * @param int $port
     */
    public function __construct(LoggerInterface $logger, $host = 'localhost', $port = 4730)
    {
        $this->host = $host;
        $this->port = $port;
        $this->logger = $logger;

        $this->client = new \GearmanClient();
        $this->client->addServer($this->host, $this->port);
        $this->logger->debug("Starting client at " . date("d.m.Y H:i s", time()));
    }

    /**
     * Add new task to worker
     * @param $task
     * @param $data
     */
    public function addTaskToWorker($task, $data, $context=null, $unique=null)
    {
        $work = json_encode($data);
        $this->logger->debug("Added new task <$task>.");
        $this->client->addTaskBackground($task, $work, $context, $unique);

    }

    /**
     * Run all added tasks
     */
    public function runTasks()
    {
        $this->client->runTasks();
        $this->logger->debug('All tasks added');
    }
}
