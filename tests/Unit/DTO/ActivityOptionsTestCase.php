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
use Temporal\Activity\ActivityCancellationType;
use Temporal\Activity\ActivityOptions;
use Temporal\Common\RetryOptions;
use Temporal\Common\Uuid;

class ActivityOptionsTestCase extends DTOMarshallingTestCase
{
    /**
     * @throws \ReflectionException
     */
    public function testMarshalling(): void
    {
        $dto = new ActivityOptions();

        $expected = [
            'TaskQueueName'          => 'default',
            'ScheduleToCloseTimeout' => 0,
            'ScheduleToStartTimeout' => 0,
            'StartToCloseTimeout'    => 0,
            'HeartbeatTimeout'       => 0,
            'WaitForCancellation'    => false,
            'ActivityID'             => '',
            'RetryPolicy'            => [
                'initial_interval'          => null,
                'backoff_coefficient'       => 2.0,
                'maximum_interval'          => null,
                'maximum_attempts'          => 1,
                'non_retryable_error_types' => [],
            ],
        ];

        $this->assertSame($expected, $this->marshal($dto));
    }

    public function testTaskQueueChangesNotMutateState(): void
    {
        $dto = new ActivityOptions();

        $this->assertNotSame($dto, $dto->withTaskQueue(Uuid::v4()));
    }

    public function testScheduleToCloseTimeoutChangesNotMutateState(): void
    {
        $dto = new ActivityOptions();

        $this->assertNotSame($dto, $dto->withScheduleToCloseTimeout(
            CarbonInterval::days(42)
        ));
    }

    public function testScheduleToStartTimeoutChangesNotMutateState(): void
    {
        $dto = new ActivityOptions();

        $this->assertNotSame($dto, $dto->withScheduleToStartTimeout(
            CarbonInterval::days(42)
        ));
    }

    public function testStartToCloseTimeoutChangesNotMutateState(): void
    {
        $dto = new ActivityOptions();

        $this->assertNotSame($dto, $dto->withStartToCloseTimeout(
            CarbonInterval::days(42)
        ));
    }

    public function testHeartbeatTimeoutChangesNotMutateState(): void
    {
        $dto = new ActivityOptions();

        $this->assertNotSame($dto, $dto->withHeartbeatTimeout(
            CarbonInterval::days(42)
        ));
    }

    public function testCancellationTypeChangesNotMutateState(): void
    {
        $dto = new ActivityOptions();

        $this->assertNotSame($dto, $dto->withCancellationType(
            ActivityCancellationType::ABANDON
        ));
    }

    public function testActivityIdChangesNotMutateState(): void
    {
        $dto = new ActivityOptions();

        $this->assertNotSame($dto, $dto->withActivityId(
            Uuid::v4()
        ));
    }

    public function testRetryOptionsChangesNotMutateState(): void
    {
        $dto = new ActivityOptions();

        $this->assertNotSame($dto, $dto->withRetryOptions(
            RetryOptions::new()
        ));
    }
}
