<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Temporal\Tests\Client;

use Temporal\Client\GRPC\ServiceClient;
use Temporal\Client\WorkflowClient;
use Temporal\Exception\Client\WorkflowFailedException;
use Temporal\Exception\Failure\ActivityFailure;
use Temporal\Exception\Failure\ApplicationFailure;
use Temporal\Exception\Failure\ChildWorkflowFailure;
use Temporal\Tests\TestCase;

class FailureTestCase extends TestCase
{
    public function testSimpleFailurePropagation()
    {
        $w = $this->createClient();
        $ex = $w->newUntypedWorkflowStub('ExceptionalWorkflow');

        $e = $ex->start();
        $this->assertNotEmpty($e->id);
        $this->assertNotEmpty($e->runId);

        try {
            $this->assertSame('OK', $ex->getResult(0));
        } catch (WorkflowFailedException $e) {
            $this->assertInstanceOf(ApplicationFailure::class, $e->getPrevious());
            $this->assertStringContainsString('workflow error', $e->getPrevious()->getMessage());
        }
    }

    public function testActivityFailurePropagation()
    {
        $w = $this->createClient();
        $ex = $w->newUntypedWorkflowStub('ExceptionalActivityWorkflow');

        $e = $ex->start();
        $this->assertNotEmpty($e->id);
        $this->assertNotEmpty($e->runId);

        $this->expectException(WorkflowFailedException::class);
        $ex->getResult(0);
    }

    public function testChildWorkflowFailurePropagation()
    {
        $w = $this->createClient();
        $ex = $w->newUntypedWorkflowStub('ComplexExceptionalWorkflow');

        $e = $ex->start();
        $this->assertNotEmpty($e->id);
        $this->assertNotEmpty($e->runId);

        try {
            $ex->getResult(0);
        } catch (WorkflowFailedException $e) {
            $this->assertInstanceOf(ChildWorkflowFailure::class, $e->getPrevious());
            $this->assertStringContainsString('ComplexExceptionalWorkflow', $e->getPrevious()->getMessage());

            $e = $e->getPrevious();

            $this->assertInstanceOf(ActivityFailure::class, $e->getPrevious());
            $this->assertStringContainsString('ExceptionalActivityWorkflow', $e->getPrevious()->getMessage());

            $e = $e->getPrevious();

            $this->assertInstanceOf(ApplicationFailure::class, $e->getPrevious());
            $this->assertStringContainsString('SimpleActivity->fail', $e->getPrevious()->getMessage());
        }
    }

    /**
     * @return WorkflowClient
     */
    private function createClient(): WorkflowClient
    {
        $sc = ServiceClient::createInsecure('localhost:7233');

        return new WorkflowClient($sc);
    }
}
