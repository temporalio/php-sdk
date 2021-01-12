<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Internal\Workflow;

use React\Promise\Deferred;
use React\Promise\PromiseInterface;
use Temporal\DataConverter\DataConverterInterface;
use Temporal\DataConverter\EncodedValues;
use Temporal\DataConverter\Payload;
use Temporal\DataConverter\ValuesInterface;
use Temporal\Internal\Marshaller\MarshallerInterface;
use Temporal\Internal\Transport\ClientInterface;
use Temporal\Internal\Transport\Request\ExecuteChildWorkflow;
use Temporal\Internal\Transport\Request\GetChildWorkflowExecution;
use Temporal\Internal\Transport\Request\SignalExternalWorkflow;
use Temporal\Worker\Transport\Command\RequestInterface;
use Temporal\Workflow;
use Temporal\Workflow\ChildWorkflowOptions;
use Temporal\Workflow\ChildWorkflowStubInterface;
use Temporal\Workflow\WorkflowExecution;

final class ChildWorkflowStub implements ChildWorkflowStubInterface
{
    private string $workflow;
    private Deferred $execution;
    private ChildWorkflowOptions $options;
    private MarshallerInterface $marshaller;
    private ?ExecuteChildWorkflow $request = null;

    /**
     * @param MarshallerInterface $marshaller
     * @param string $workflow
     * @param ChildWorkflowOptions $options
     */
    public function __construct(MarshallerInterface $marshaller, string $workflow, ChildWorkflowOptions $options)
    {
        $this->marshaller = $marshaller;
        $this->workflow = $workflow;
        $this->options = $options;
        $this->execution = new Deferred();
    }

    /**
     * @return string
     */
    public function getChildWorkflowType(): string
    {
        return $this->workflow;
    }

    /**
     * @return PromiseInterface
     */
    public function getExecution(): PromiseInterface
    {
        return $this->execution->promise();
    }

    /**
     * {@inheritDoc}
     */
    public function execute(array $args = [], \ReflectionType $returnType = null): PromiseInterface
    {
        if ($this->request !== null) {
            throw new \LogicException('Child workflow already has been executed');
        }

        $this->request = new ExecuteChildWorkflow(
            $this->workflow,
            EncodedValues::fromValues($args),
            $this->getOptionsArray()
        );

        $promise = $this->request($this->request);

        $this->request(new GetChildWorkflowExecution($this->request))
            ->then(
                function (ValuesInterface $values) {
                    $execution = $values->getValue(0, WorkflowExecution::class);
                    $this->execution->resolve($execution);

                    return $execution;
                }
            );

        return EncodedValues::decodePromise($promise, $returnType);
    }

    /**
     * @return array
     */
    private function getOptionsArray(): array
    {
        return $this->marshaller->marshal($this->getOptions());
    }

    /**
     * @return ChildWorkflowOptions
     */
    public function getOptions(): ChildWorkflowOptions
    {
        return $this->options;
    }

    /**
     * {@inheritDoc}
     */
    public function signal(string $name, array $args = []): PromiseInterface
    {
        $execution = $this->execution->promise();

        return $execution->then(
            function (WorkflowExecution $execution) use ($name, $args) {
                $request = new SignalExternalWorkflow(
                    $this->getOptions()->namespace,
                    $execution->id,
                    $execution->runId,
                    $name,
                    EncodedValues::fromValues($args)
                );

                return $this->request($request);
            }
        );
    }

    /**
     * @param RequestInterface $request
     * @return PromiseInterface
     */
    protected function request(RequestInterface $request): PromiseInterface
    {
        /** @var Workflow\WorkflowContextInterface $context */
        $context = Workflow::getCurrentContext();

        return $context->request($request);
    }
}
