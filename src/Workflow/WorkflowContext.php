<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Client\Workflow;

use Carbon\CarbonInterface;
use Carbon\CarbonTimeZone;
use React\Promise\PromiseInterface;
use Temporal\Client\Activity\ActivityOptions;
use Temporal\Client\Internal\ServiceContainer;
use Temporal\Client\Internal\Support\DateInterval;
use Temporal\Client\Internal\Transport\CapturedClient;
use Temporal\Client\Internal\Transport\CapturedClientInterface;
use Temporal\Client\Internal\Transport\ClientInterface;
use Temporal\Client\Internal\Transport\Request\CompleteWorkflow;
use Temporal\Client\Internal\Transport\Request\ExecuteActivity;
use Temporal\Client\Internal\Transport\Request\GetVersion;
use Temporal\Client\Internal\Transport\Request\NewTimer;
use Temporal\Client\Internal\Transport\Request\SideEffect;
use Temporal\Client\Internal\Workflow\ActivityProxy;
use Temporal\Client\Internal\Workflow\Input;
use Temporal\Client\Internal\Workflow\Process\CancellationScope;
use Temporal\Client\Internal\Workflow\Process\Process;
use Temporal\Client\Worker\Command\RequestInterface;

use function React\Promise\reject;

class WorkflowContext implements WorkflowContextInterface, ClientInterface
{
    /**
     * @var ServiceContainer
     */
    protected ServiceContainer $services;

    /**
     * @var CapturedClientInterface
     */
    protected CapturedClientInterface $client;

    /**
     * @var Input
     */
    private Input $input;

    /**
     * @var Process
     */
    private Process $process;

    /**
     * @var array
     */
    private array $trace = [];

    /**
     * @param Process $process
     * @param ServiceContainer $services
     * @param Input $input
     */
    public function __construct(Process $process, ServiceContainer $services, Input $input)
    {
        $this->process = $process;
        $this->input = $input;
        $this->services = $services;

        $this->client = new CapturedClient($services->client);
    }

    /**
     * {@inheritDoc}
     */
    public function getTimeZone(): CarbonTimeZone
    {
        $this->recordTrace();

        return $this->services->env->getTimeZone();
    }

    /**
     * Record last stack trace of the call.
     *
     * @return void
     */
    private function recordTrace(): void
    {
        $this->trace = \debug_backtrace(
            \DEBUG_BACKTRACE_IGNORE_ARGS
        );
    }

    /**
     * {@inheritDoc}
     */
    public function now(): CarbonInterface
    {
        $this->recordTrace();

        return $this->services->env->now();
    }

    /**
     * @return string
     */
    public function getRunId(): string
    {
        $this->recordTrace();

        return $this->input->info->execution->runId;
    }

    /**
     * {@inheritDoc}
     */
    public function getInfo(): WorkflowInfo
    {
        $this->recordTrace();

        return $this->input->info;
    }

    /**
     * {@inheritDoc}
     */
    public function getArguments(): array
    {
        $this->recordTrace();

        return $this->input->args;
    }

    /**
     * @return CapturedClientInterface
     */
    public function getClient(): CapturedClientInterface
    {
        return $this->client;
    }

    /**
     * {@inheritDoc}
     */
    public function registerQuery(string $queryType, callable $handler): WorkflowContextInterface
    {
        $this->recordTrace();

        $instance = $this->process->getWorkflowInstance();
        $instance->addQueryHandler($queryType, $handler);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function registerSignal(string $queryType, callable $handler): WorkflowContextInterface
    {
        $this->recordTrace();

        $instance = $this->process->getWorkflowInstance();
        $instance->addSignalHandler($queryType, $handler);

        return $this;
    }

    /**
     * @param callable $handler
     * @return PromiseInterface
     */
    public function newCancellationScope(callable $handler): CancellationScope
    {
        $this->recordTrace();

        $self = clone $this;
        $self->client = new CapturedClient($this->client);

        return new CancellationScope($self, $this->services, \Closure::fromCallable($handler));
    }

    /**
     * {@inheritDoc}
     */
    public function getVersion(string $changeId, int $minSupported, int $maxSupported): PromiseInterface
    {
        $this->recordTrace();

        return $this->request(
            new GetVersion($changeId, $minSupported, $maxSupported)
        );
    }

    /**
     * {@inheritDoc}
     */
    public function request(RequestInterface $request): PromiseInterface
    {
        $this->recordTrace();

        return $this->client->request($request);
    }

    /**
     * {@inheritDoc}
     */
    public function sideEffect(callable $context): PromiseInterface
    {
        $this->recordTrace();

        try {
            $value = $this->isReplaying() ? null : $context();
        } catch (\Throwable $e) {
            return reject($e);
        }

        return $this->request(new SideEffect($value));
    }

    /**
     * {@inheritDoc}
     */
    public function isReplaying(): bool
    {
        $this->recordTrace();

        return $this->services->env->isReplaying();
    }

    /**
     * {@inheritDoc}
     */
    public function complete($result = null): PromiseInterface
    {
        $this->recordTrace();

        $this->process->cancel();

        return $this->request(new CompleteWorkflow($result));
    }

    /**
     * {@inheritDoc}
     */
    public function executeActivity(string $name, array $args = [], ActivityOptions $options = null): PromiseInterface
    {
        $this->recordTrace();

        $options ??= new ActivityOptions();

        return $this->request(
            new ExecuteActivity($name, $args, $this->services->marshaller->marshal($options))
        );
    }

    /**
     * {@inheritDoc}
     */
    public function newActivityStub(string $name, ActivityOptions $options = null): object
    {
        $this->recordTrace();

        $options ??= new ActivityOptions();

        return new ActivityProxy($name, $options, $this, $this->services->activities);
    }

    /**
     * {@inheritDoc}
     */
    public function timer($interval): PromiseInterface
    {
        $this->recordTrace();

        return $this->request(
            new NewTimer(DateInterval::parse($interval, DateInterval::FORMAT_SECONDS))
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getTrace(): array
    {
        return $this->trace;
    }
}
