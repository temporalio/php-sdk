<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Tests\Workflow;

use Temporal\Activity\ActivityOptions;
use Temporal\Tests\Activity\SimpleActivity;
use Temporal\Workflow;
use Temporal\Workflow\SignalMethod;
use Temporal\Workflow\WorkflowMethod;

#[Workflow\WorkflowInterface]
class LoopWithSignalCoroutinesWorkflow
{
    private array $values = [];
    private array $result = [];
    private $simple;

    public function __construct()
    {
        $this->simple = Workflow::newActivityStub(
            SimpleActivity::class,
            ActivityOptions::new()->withStartToCloseTimeout(5)
        );
    }

    #[SignalMethod]
    public function addValue(
        string $value
    ) {
        $value = yield $this->simple->prefix('in signal ', $value);
        $value = yield $this->simple->prefix('in signal 2 ', $value);

        $this->values[] = $value;
    }

    #[WorkflowMethod(name: 'LoopWithSignalCoroutinesWorkflow')]
    public function run(
        int $count
    ) {
        while (true) {
            yield Workflow::await(fn() => $this->values !== []);
            $value = array_shift($this->values);

            // uppercases
            $this->result[] = yield $this->simple->echo($value);

            if (count($this->result) === $count) {
                break;
            }
        }

        return $this->result;
    }
}
