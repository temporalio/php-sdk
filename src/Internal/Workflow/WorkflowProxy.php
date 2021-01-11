<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Internal\Workflow;

use Temporal\Client\WorkflowOptions;
use Temporal\Client\WorkflowStubInterface;
use Temporal\Internal\Declaration\Prototype\Prototype;
use Temporal\Internal\Declaration\Prototype\WorkflowPrototype;
use Temporal\Internal\Marshaller\MarshallerInterface;
use Temporal\Worker\Transport\RpcConnectionInterface;
use Temporal\Workflow\WorkflowRun;

/**
 * @template-covariant T of object
 */
final class WorkflowProxy extends Proxy
{
    /**
     * @var string
     */
    private const ERROR_UNDEFINED_WORKFLOW_METHOD =
        'The given stub class "%s" does not contain a workflow method named "%s"';

    /**
     * @var string
     */
    private const ERROR_UNDEFINED_METHOD =
        'The given stub class "%s" does not contain a workflow, query or signal method named "%s"';

    /**
     * @var WorkflowStubInterface|null
     */
    private ?WorkflowStubInterface $stub;

    /**
     * @var WorkflowPrototype|null
     */
    private ?WorkflowPrototype $prototype;

    /**
     * @var string
     */
    private string $class;

    /**
     * @param WorkflowStubInterface $stub
     * @param WorkflowPrototype $prototype
     * @param string $class
     */
    public function __construct(WorkflowStubInterface $stub, WorkflowPrototype $prototype, string $class)
    {
        $this->stub = $stub;
        $this->prototype = $prototype;
        $this->class = $class;
    }

    /**
     * @param string $method
     * @param array $args
     * @return mixed|void
     */
    public function __call(string $method, array $args)
    {
        if ($method === $this->prototype->getHandler()->getName()) {
            // If the proxy does not contain information about the running workflow,
            // then we try to create a new stub from the workflow method and start
            // the workflow.
            $this->stub->start(...$args);

            return new WorkflowRun($this->stub, $this->prototype->getHandler()->getReturnType());
        }

        // Otherwise, we try to find a suitable workflow "query" method.
        foreach ($this->prototype->getQueryHandlers() as $name => $query) {
            if ($query->getName() === $method) {
                return $this->stub->query($name, $args);
            }
        }

        // Otherwise, we try to find a suitable workflow "signal" method.
        foreach ($this->prototype->getSignalHandlers() as $name => $signal) {
            if ($signal->getName() === $method) {
                $this->stub->signal($name, $args);

                return;
            }
        }

        throw new \BadMethodCallException(
            \sprintf(self::ERROR_UNDEFINED_METHOD, $this->class, $method)
        );
    }
}
