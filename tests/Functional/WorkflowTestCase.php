<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Tests\Functional;

use Temporal\Tests\Fixtures\Splitter;
use Temporal\Tests\Fixtures\WorkerMock;

/**
 * @group workflow
 * @group functional
 */
class WorkflowTestCase extends FunctionalTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        // emulate connection to parent server
        $_SERVER['RR_RPC'] = 'tcp://127.0.0.1:6001';
    }

    public function testSplitter()
    {
        $splitter = Splitter::create('Test_ExecuteSimpleWorkflow_1.log');

        $this->assertNotEmpty($splitter->getQueue());
    }

    public function testSimpleWorkflow()
    {
        $worker = WorkerMock::createMock();

        $worker->run($this, Splitter::create('Test_ExecuteSimpleWorkflow_1.log')->getQueue());
    }

    public function testTimer()
    {
        $worker = WorkerMock::createMock();

        $worker->run($this, Splitter::create('Test_Timer.log')->getQueue());
    }

    public function testGetQuery()
    {
        $worker = WorkerMock::createMock();

        $worker->run($this, Splitter::create('Test_GetQuery.log')->getQueue());
    }

    public function testCancelledWithCompensationWorkflow()
    {
        $worker = WorkerMock::createMock();

        $worker->run($this, Splitter::create('Test_CancelledWithCompensationWorkflow.log')->getQueue());
    }

    public function testCancelledNestedWorkflow()
    {
        $worker = WorkerMock::createMock();

        $worker->run($this, Splitter::create('Test_CancelledNestedWorkflow.log')->getQueue());
    }

    public function testCancelledMidflightWorkflow()
    {
        $worker = WorkerMock::createMock();

        $worker->run($this, Splitter::create('Test_CancelledMidflightWorkflow.log')->getQueue());
    }

    public function testSendSignalBeforeCompletingWorkflow()
    {
        $worker = WorkerMock::createMock();

        $worker->run($this, Splitter::create('Test_SendSignalBeforeCompletingWorkflow.log')->getQueue());
    }

    public function testActivityStubWorkflow()
    {
        $worker = WorkerMock::createMock();

        $worker->run($this, Splitter::create('Test_ActivityStubWorkflow.log')->getQueue());
    }

    public function testBinaryPayload()
    {
        $worker = WorkerMock::createMock();

        $worker->run($this, Splitter::create('Test_BinaryPayload.log')->getQueue());
    }

    public function testContinueAsNew()
    {
        $worker = WorkerMock::createMock();

        $worker->run($this, Splitter::create('Test_ContinueAsNew.log')->getQueue());
    }

    public function testEmptyWorkflow()
    {
        $worker = WorkerMock::createMock();

        $worker->run($this, Splitter::create('Test_EmptyWorkflow.log')->getQueue());
    }

    public function testSideEffectWorkflow()
    {
        $worker = WorkerMock::createMock();

        $worker->run($this, Splitter::create('Test_SideEffect.log')->getQueue());
    }

    public function testExecuteWorkflowWithParallelScopes()
    {
        $worker = WorkerMock::createMock();

        $worker->run($this, Splitter::create('Test_ExecuteWorkflowWithParallelScopes.log')->getQueue());
    }

    public function testActivity()
    {
        $worker = WorkerMock::createMock();

        $worker->run($this, Splitter::create('Test_Activity.log')->getQueue());
    }

    public function testExecuteProtoWorkflow()
    {
        $worker = WorkerMock::createMock();

        $worker->run($this, Splitter::create('Test_ExecuteProtoWorkflow.log')->getQueue());
    }

    public function testExecuteSimpleDTOWorkflow()
    {
        $worker = WorkerMock::createMock();

        $worker->run($this, Splitter::create('Test_ExecuteSimpleDTOWorkflow.log')->getQueue());
    }

    public function testExecuteSimpleWorkflowWithSequenceInBatch()
    {
        $worker = WorkerMock::createMock();

        $worker->run($this, Splitter::create('Test_ExecuteSimpleWorkflowWithSequenceInBatch.log')->getQueue());
    }

    public function testPromiseChaining()
    {
        $worker = WorkerMock::createMock();

        $worker->run($this, Splitter::create('Test_PromiseChaining.log')->getQueue());
    }

    public function testMultipleWorkflowsInSingleWorker()
    {
        $worker = WorkerMock::createMock();

        $worker->run($this, Splitter::create('Test_MultipleWorkflowsInSingleWorker.log')->getQueue());
    }

    public function testSignalChildViaStubWorkflow()
    {
        $worker = WorkerMock::createMock();

        $worker->run($this, Splitter::create('Test_SignalChildViaStubWorkflow.log')->getQueue());
    }

    public function testExecuteChildStubWorkflow()
    {
        $worker = WorkerMock::createMock();

        $worker->run($this, Splitter::create('Test_ExecuteChildStubWorkflow.log')->getQueue());
    }

    public function testExecuteChildStubWorkflow_02()
    {
        $worker = WorkerMock::createMock();

        $worker->run($this, Splitter::create('Test_ExecuteChildStubWorkflow_02.log')->getQueue());
    }

    public function testExecuteChildWorkflow()
    {
        $worker = WorkerMock::createMock();

        $worker->run($this, Splitter::create('Test_ExecuteChildWorkflow.log')->getQueue());
    }

    public function testRuntimeSignal()
    {
        $worker = WorkerMock::createMock();

        $worker->run($this, Splitter::create('Test_RuntimeSignal.log')->getQueue());
    }

    public function testSignalStepsAndRuntimeQuery()
    {
        $worker = WorkerMock::createMock();

        $worker->run($this, Splitter::create('Test_SignalSteps.log')->getQueue());
    }

    public function testBatchedSignal_WithPauses()
    {
        $worker = WorkerMock::createMock();

        $worker->run($this, Splitter::create('Test_BatchedSignal.log')->getQueue());
    }

    public function testBatchedSignal_Combined()
    {
        $worker = WorkerMock::createMock();

        $worker->run($this, Splitter::create('Test_BatchedSignal_01.log')->getQueue());
    }
}
