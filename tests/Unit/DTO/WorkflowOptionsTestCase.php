<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Tests\Unit\DTO;

use Carbon\CarbonInterval;
use Temporal\Client\WorkflowOptions;
use Temporal\Common\IdReusePolicy;
use Temporal\Common\RetryOptions;
use Temporal\Common\Uuid;

class WorkflowOptionsTestCase extends DTOMarshallingTestCase
{
    /**
     * @throws \ReflectionException
     */
    public function testMarshalling(): void
    {
        $dto = new WorkflowOptions();

        $expected = [
            'WorkflowID'               => $dto->workflowId,
            'TaskQueue'                => 'default',
            'WorkflowExecutionTimeout' => 0,
            'WorkflowRunTimeout'       => 0,
            'WorkflowTaskTimeout'      => 0,
            'WorkflowIDReusePolicy'    => 2,
            'RetryPolicy'              => [
                'initial_interval'          => null,
                'backoff_coefficient'       => 2.0,
                'maximum_interval'          => null,
                'maximum_attempts'          => 1,
                'non_retryable_error_types' => [],
            ],
            'CronSchedule'             => null,
            'Memo'                     => null,
            'SearchAttributes'         => null,
        ];

        $this->assertSame($expected, $this->marshal($dto));
    }

    public function testWorkflowIdChangesNotMutateState(): void
    {
        $dto = new WorkflowOptions();

        $this->assertNotSame($dto, $dto->withWorkflowId(Uuid::v4()));
    }

    public function testTaskQueueChangesNotMutateState(): void
    {
        $dto = new WorkflowOptions();

        $this->assertNotSame($dto, $dto->withTaskQueue(Uuid::v4()));
    }

    public function testWorkflowExecutionTimeoutChangesNotMutateState(): void
    {
        $dto = new WorkflowOptions();

        $this->assertNotSame($dto, $dto->withWorkflowExecutionTimeout(
            CarbonInterval::days(42)
        ));
    }

    public function testWorkflowRunTimeoutChangesNotMutateState(): void
    {
        $dto = new WorkflowOptions();

        $this->assertNotSame($dto, $dto->withWorkflowRunTimeout(
            CarbonInterval::days(42)
        ));
    }

    public function testWorkflowTaskTimeoutChangesNotMutateState(): void
    {
        $dto = new WorkflowOptions();

        $this->assertNotSame($dto, $dto->withWorkflowTaskTimeout(
            CarbonInterval::seconds(10)
        ));
    }

    public function testWorkflowIdReusePolicyChangesNotMutateState(): void
    {
        $dto = new WorkflowOptions();

        $this->assertNotSame($dto, $dto->withWorkflowIdReusePolicy(
            IdReusePolicy::POLICY_ALLOW_DUPLICATE
        ));
    }

    public function testRetryOptionsChangesNotMutateState(): void
    {
        $dto = new WorkflowOptions();

        $this->assertNotSame($dto, $dto->withRetryOptions(
            RetryOptions::new()
        ));
    }

    public function testCronScheduleChangesNotMutateState(): void
    {
        $dto = new WorkflowOptions();

        $this->assertNotSame($dto, $dto->withCronSchedule('* * * * *'));
    }

    public function testMemoChangesNotMutateState(): void
    {
        $dto = new WorkflowOptions();

        $this->assertNotSame($dto, $dto->withMemo([1, 2, 3]));
    }

    public function testSearchAttributesChangesNotMutateState(): void
    {
        $dto = new WorkflowOptions();

        $this->assertNotSame($dto, $dto->withSearchAttributes([1, 2, 3]));
    }
}
