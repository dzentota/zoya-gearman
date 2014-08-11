<?php
namespace Zoya\Gearman;

use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;

/**
 * Class Worker
 * @package Zoya\Gearman
 */
class Worker
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
     * @var \GearmanWorker
     */
    private $worker;

    private $handler;

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

        $this->worker = new \GearmanWorker();
        $this->worker->addServer($this->host, $this->port);

        $this->logger->debug('[' . getmypid() . "] Waiting for job...");

    }

    /**
     * Add new job for worker
     * @param $job
     * @param callable $handler
     */
    public function addJob($job, callable $handler)
    {
        $this->handler = $handler;
        $this->logger->debug('added JOB');
        $this->worker->addFunction($job, [$this, 'jobHandler']);
    }

    /**
     * Proxy function to handle possible errors
     * @param null $job
     * @return bool
     */
    public function jobHandler($job = null)
    {
        if (empty($job)) {
            $this->logger->warning('No jobs');
            $job->sendFail();
            return false;
        }
        $this->logger->debug("Received job: " . $job->handle());

        $work = json_decode($job->workload());

        if (!empty($work)) {
            $handler = $this->handler;
            try {
                if (true === $handler($work)) {
                    $this->logger->debug('SUCCESSFULLY COMPLETE');
                    $job->sendComplete('OK');
                } else {
                    $this->logger->error('JOB FAILED');
                    $job->sendFail();
                }
            } catch (\Exception $e) {
                $this->logger->error('CAUGHT EXCEPTION');
                $job->sendException($e->getMessage());
                $job->sendFail();
            }

        } else {
            $this->logger->error('Empty or corrupted job');
            $job->sendFail();
            return false;
        }
    }

    /**
     * Run worker
     */
    public function run()
    {
        while ($this->worker->work()) {
            if ($this->worker->returnCode() != GEARMAN_SUCCESS) {
                $this->logger->error("return_code: " . $this->worker->returnCode());
                break;
            }
        }

    }
}
