<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Workflow;

use React\Promise\PromiseInterface;
use Temporal\Activity\ActivityOptions;
use Temporal\DataConverter\DataConverterInterface;
use Temporal\DataConverter\ValuesInterface;
use Temporal\Internal\Support\DateInterval;
use Temporal\Worker\Transport\Command\RequestInterface;
use Temporal\Worker\Environment\EnvironmentInterface;

/**
 * @psalm-import-type DateIntervalFormat from DateInterval
 */
interface WorkflowContextInterface extends EnvironmentInterface
{
    /**
     * @return WorkflowInfo
     */
    public function getInfo(): WorkflowInfo;

    /**
     * @return ValuesInterface
     */
    public function getInput(): ValuesInterface;

    /**
     * @return DataConverterInterface
     */
    public function getDataConverter(): DataConverterInterface;

    /**
     * @param string $queryType
     * @param callable $handler
     * @return $this
     */
    public function registerQuery(string $queryType, callable $handler): self;

    /**
     * @param string $queryType
     * @param callable $handler
     * @return $this
     */
    public function registerSignal(string $queryType, callable $handler): self;

    /**
     * @param string $changeId
     * @param int $minSupported
     * @param int $maxSupported
     * @return PromiseInterface
     */
    public function getVersion(string $changeId, int $minSupported, int $maxSupported): PromiseInterface;

    /**
     * @psalm-type SideEffectCallback = callable(): mixed
     * @psalm-param SideEffectCallback $context
     *
     * @param callable $context
     * @return PromiseInterface
     */
    public function sideEffect(callable $context): PromiseInterface;

    /**
     * @param array|null $result
     * @param \Throwable|null $failure
     * @return PromiseInterface
     */
    public function complete(array $result = null, \Throwable $failure = null): PromiseInterface;

    /**
     * @param DateIntervalFormat|int $interval
     * @return PromiseInterface
     * @see DateInterval
     */
    public function timer($interval): PromiseInterface;

    /**
     * @return array
     */
    public function getTrace(): array;

    /**
     * @param class-string|string $type
     * @param array $args
     * @param ChildWorkflowOptions|null $options
     * @param \ReflectionType|null $returnType
     * @return PromiseInterface
     */
    public function executeChildWorkflow(
        string $type,
        array $args = [],
        ChildWorkflowOptions $options = null,
        \ReflectionType $returnType = null
    ): PromiseInterface;

    /**
     * @psalm-template T of object
     * @psalm-param class-string<T> $class
     * @psalm-return object<T>|T
     *
     * @param string $class
     * @param ChildWorkflowOptions|null $options
     * @return object
     */
    public function newChildWorkflowStub(string $class, ChildWorkflowOptions $options = null): object;

    /**
     * @param string $name
     * @param ChildWorkflowOptions|null $options
     * @return ChildWorkflowStubInterface
     */
    public function newUntypedChildWorkflowStub(
        string $name,
        ChildWorkflowOptions $options = null
    ): ChildWorkflowStubInterface;

    /**
     * @param string $type
     * @param array $args
     * @param ActivityOptions|null $options
     * @param \ReflectionType|null $returnType
     * @return PromiseInterface
     */
    public function executeActivity(
        string $type,
        array $args = [],
        ActivityOptions $options = null,
        \ReflectionType $returnType = null
    ): PromiseInterface;

    /**
     * @psalm-template TActivity
     * @psalm-param class-string<TActivity> $class
     * @psalm-return TActivity
     *
     * @param string $class
     * @param ActivityOptions|null $options
     * @return object
     */
    public function newActivityStub(string $class, ActivityOptions $options = null): object;

    /**
     * @param ActivityOptions|null $options
     * @return ActivityStubInterface
     */
    public function newUntypedActivityStub(ActivityOptions $options = null): ActivityStubInterface;

    /**
     * {@inheritDoc}
     */
    public function request(RequestInterface $request): PromiseInterface;
}
