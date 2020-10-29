<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Client\Protocol\Router;

use Temporal\Client\Workflow\Runtime\RunningWorkflows;

final class InvokeQueryMethod extends Route
{
    /**
     * @var string
     */
    private const ERROR_RID_NOT_DEFINED =
        'Invoking query of a workflow requires the id (rid argument) ' .
        'of the running workflow process';

    /**
     * @var string
     */
    private const ERROR_PROCESS_NOT_FOUND = 'Workflow with the specified run id %s not found';

    /**
     * @var string
     */
    private const ERROR_QUERY_NOT_FOUND = 'Workflow query handler "%s" not found';

    /**
     * @var RunningWorkflows
     */
    private RunningWorkflows $running;

    /**
     * @param RunningWorkflows $running
     */
    public function __construct(RunningWorkflows $running)
    {
        $this->running = $running;
    }

    /**
     * {@inheritDoc}
     */
    public function handle(array $payload, array $headers)
    {
        $this->assertArguments($payload);

        $workflowRunId = $payload['rid'] ?? $headers['rid'] ?? null;

        if ($workflowRunId === null) {
            throw new \InvalidArgumentException(self::ERROR_RID_NOT_DEFINED);
        }

        $workflow = $this->running->find($workflowRunId);

        if ($workflow === null) {
            throw new \LogicException(\sprintf(self::ERROR_PROCESS_NOT_FOUND, $workflowRunId));
        }

        $declaration = $workflow->getDeclaration();

        $handler = $declaration->findQueryHandler($payload['name']);

        if ($handler === null) {
            throw new \LogicException(\sprintf(self::ERROR_QUERY_NOT_FOUND, $payload['name']));
        }

        $handler(...($payload['args'] ?? []));
    }

    private function assertArguments(array $payload): void
    {
        // TODO
    }
}
