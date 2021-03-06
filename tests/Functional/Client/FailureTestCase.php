<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Tests\Functional\Client;

use Temporal\Exception\Client\WorkflowFailedException;
use Temporal\Exception\Failure\ActivityFailure;
use Temporal\Exception\Failure\ApplicationFailure;
use Temporal\Exception\Failure\ChildWorkflowFailure;

/**
 * @group client
 * @group functional
 */
class FailureTestCase extends ClientTestCase
{
    public function testSimpleFailurePropagation()
    {
        $client = $this->createClient();
        $ex = $client->newUntypedWorkflowStub('ExceptionalWorkflow');

        $e = $client->start($ex);
        $this->assertNotEmpty($e->getExecution()->getID());
        $this->assertNotEmpty($e->getExecution()->getRunID());

        try {
            $this->assertSame('OK', $ex->getResult());
            $this->fail('unreachable');
        } catch (WorkflowFailedException $e) {
            $this->assertInstanceOf(ApplicationFailure::class, $e->getPrevious());
            $this->assertStringContainsString('workflow error', $e->getPrevious()->getMessage());
        }
    }

    public function testActivityFailurePropagation()
    {
        $client = $this->createClient();
        $ex = $client->newUntypedWorkflowStub('ExceptionalActivityWorkflow');

        $e = $client->start($ex);
        $this->assertNotEmpty($e->getExecution()->getID());
        $this->assertNotEmpty($e->getExecution()->getRunID());

        $this->expectException(WorkflowFailedException::class);
        $ex->getResult();
    }

    public function testChildWorkflowFailurePropagation()
    {
        $client = $this->createClient();
        $ex = $client->newUntypedWorkflowStub('ComplexExceptionalWorkflow');

        $e = $client->start($ex);
        $this->assertNotEmpty($e->getExecution()->getID());
        $this->assertNotEmpty($e->getExecution()->getRunID());

        try {
            $ex->getResult();
            $this->fail('unreachable');
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
}
