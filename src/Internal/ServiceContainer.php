<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Internal;

use JetBrains\PhpStorm\Immutable;
use Spiral\Attributes\ReaderInterface;
use Temporal\DataConverter\DataConverterInterface;
use Temporal\Internal\Declaration\Prototype\ActivityPrototype;
use Temporal\Internal\Declaration\Prototype\ActivityCollection;
use Temporal\Internal\Declaration\Prototype\WorkflowCollection;
use Temporal\Internal\Declaration\Prototype\WorkflowPrototype;
use Temporal\Internal\Declaration\Reader\ActivityReader;
use Temporal\Internal\Declaration\Reader\WorkflowReader;
use Temporal\Internal\Marshaller\Mapper\AttributeMapperFactory;
use Temporal\Internal\Marshaller\Marshaller;
use Temporal\Internal\Marshaller\MarshallerInterface;
use Temporal\Internal\Queue\QueueInterface;
use Temporal\Internal\Repository\RepositoryInterface;
use Temporal\Internal\Transport\ClientInterface;
use Temporal\Internal\Workflow\ProcessCollection;
use Temporal\WorkerFactory;
use Temporal\Worker\Environment\Environment;
use Temporal\Worker\Environment\EnvironmentInterface;
use Temporal\Worker\LoopInterface;

#[Immutable]
final class ServiceContainer
{
    /**
     * @var LoopInterface
     */
    #[Immutable]
    public LoopInterface $loop;

    /**
     * @var ClientInterface
     */
    #[Immutable]
    public ClientInterface $client;

    /**
     * @var ReaderInterface
     */
    #[Immutable]
    public ReaderInterface $reader;

    /**
     * @var EnvironmentInterface
     */
    #[Immutable]
    public EnvironmentInterface $env;

    /**
     * @var MarshallerInterface
     */
    #[Immutable]
    public MarshallerInterface $marshaller;

    /**
     * @var RepositoryInterface<WorkflowPrototype>
     */
    #[Immutable]
    public RepositoryInterface $workflows;

    /**
     * @var ProcessCollection
     */
    #[Immutable]
    public ProcessCollection $running;

    /**
     * @var RepositoryInterface<ActivityPrototype>
     */
    #[Immutable]
    public RepositoryInterface $activities;

    /**
     * @var QueueInterface
     */
    #[Immutable]
    public QueueInterface $queue;

    /**
     * @var DataConverterInterface
     */
    #[Immutable]
    public DataConverterInterface $dataConverter;

    /**
     * @var WorkflowReader
     */
    #[Immutable]
    public WorkflowReader $workflowsReader;

    /**
     * @var ActivityReader
     */
    #[Immutable]
    public ActivityReader $activitiesReader;

    /**
     * @param LoopInterface $loop
     * @param ClientInterface $client
     * @param ReaderInterface $reader
     * @param QueueInterface $queue
     * @param DataConverterInterface $dataConverter
     */
    public function __construct(
        LoopInterface $loop,
        ClientInterface $client,
        ReaderInterface $reader,
        QueueInterface $queue,
        DataConverterInterface $dataConverter
    ) {
        $this->loop = $loop;
        $this->client = $client;
        $this->reader = $reader;
        $this->queue = $queue;

        $this->env = new Environment();
        $this->workflows = new WorkflowCollection();
        $this->activities = new ActivityCollection();
        $this->running = new ProcessCollection($client);

        $this->dataConverter = $dataConverter;
        $this->marshaller = new Marshaller(new AttributeMapperFactory($this->reader));

        $this->workflowsReader = new WorkflowReader($this->reader);
        $this->activitiesReader = new ActivityReader($this->reader);
    }

    /**
     * @param WorkerFactory $worker
     * @return static
     */
    public static function fromWorker(WorkerFactory $worker): self
    {
        return new self(
            $worker,
            $worker->getClient(),
            $worker->getReader(),
            $worker->getQueue(),
            $worker->getDataConverter()
        );
    }
}
