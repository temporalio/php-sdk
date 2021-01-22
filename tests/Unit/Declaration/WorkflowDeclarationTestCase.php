<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Tests\Unit\Declaration;

use Carbon\CarbonInterval;
use Temporal\Common\CronSchedule;
use Temporal\Internal\Declaration\Reader\WorkflowReader;
use Temporal\Tests\Unit\Declaration\Fixture\SimpleWorkflow;
use Temporal\Tests\Unit\Declaration\Fixture\WorkflowWithCron;
use Temporal\Tests\Unit\Declaration\Fixture\WorkflowWithCronAndRetry;
use Temporal\Tests\Unit\Declaration\Fixture\WorkflowWithCustomName;
use Temporal\Tests\Unit\Declaration\Fixture\WorkflowWithInterface;
use Temporal\Tests\Unit\Declaration\Fixture\WorkflowWithQueries;
use Temporal\Tests\Unit\Declaration\Fixture\WorkflowWithRetry;
use Temporal\Tests\Unit\Declaration\Fixture\WorkflowWithSignals;

/**
 * @group unit
 * @group declaration
 */
class WorkflowDeclarationTestCase extends DeclarationTestCase
{
    /**
     * @testdox Reading workflow without cron attribute (cron prototype value should be null)
     * @dataProvider workflowReaderDataProvider
     *
     * @param WorkflowReader $reader
     * @throws \ReflectionException
     */
    public function testWorkflowWithoutCron(WorkflowReader $reader): void
    {
        $prototype = $reader->fromClass(SimpleWorkflow::class);

        $this->assertNull($prototype->getCronSchedule());
    }

    /**
     * @testdox Reading workflow with cron attribute (cron prototype value should not be null)
     * @dataProvider workflowReaderDataProvider
     *
     * @param WorkflowReader $reader
     * @throws \ReflectionException
     */
    public function testWorkflowWithCron(WorkflowReader $reader): void
    {
        $prototype = $reader->fromClass(WorkflowWithCron::class);

        $this->assertNotNull($prototype->getCronSchedule());
        $this->assertEquals(new CronSchedule('@daily'), $prototype->getCronSchedule());
    }

    /**
     * @testdox Reading workflow without method retry attribute (method retry prototype value should be null)
     * @dataProvider workflowReaderDataProvider
     *
     * @param WorkflowReader $reader
     * @throws \ReflectionException
     */
    public function testWorkflowWithoutRetry(WorkflowReader $reader): void
    {
        $prototype = $reader->fromClass(SimpleWorkflow::class);

        $this->assertNull($prototype->getMethodRetry());
    }

    /**
     * @testdox Reading workflow with method retry attribute (method retry prototype value should not be null)
     * @dataProvider workflowReaderDataProvider
     *
     * @param WorkflowReader $reader
     * @throws \ReflectionException
     */
    public function testWorkflowWithRetry(WorkflowReader $reader): void
    {
        $prototype = $reader->fromClass(WorkflowWithRetry::class);

        $this->assertNotNull($prototype->getMethodRetry());
        $this->assertEquals(CarbonInterval::microsecond(42),
            $prototype->getMethodRetry()->initialInterval
        );
    }

    /**
     * @testdox Reading workflow with method retry and cron attributes (prototypes value should not be null)
     * @dataProvider workflowReaderDataProvider
     *
     * @param WorkflowReader $reader
     * @throws \ReflectionException
     */
    public function testWorkflowWithCronAndRetry(WorkflowReader $reader): void
    {
        $prototype = $reader->fromClass(WorkflowWithCronAndRetry::class);

        $this->assertNotNull($prototype->getCronSchedule());
        $this->assertNotNull($prototype->getMethodRetry());

        $this->assertEquals(new CronSchedule('@monthly'), $prototype->getCronSchedule());
        $this->assertEquals(CarbonInterval::microsecond(42),
            $prototype->getMethodRetry()->initialInterval
        );
    }

    /**
     * @testdox Reading workflow without query methods (query methods count equals 0)
     * @dataProvider workflowReaderDataProvider
     *
     * @param WorkflowReader $reader
     * @throws \ReflectionException
     */
    public function testWorkflowWithoutQueries(WorkflowReader $reader): void
    {
        $prototype = $reader->fromClass(SimpleWorkflow::class);

        $this->assertCount(0, $prototype->getQueryHandlers());
    }

    /**
     * @testdox Reading workflow with query methods (query methods count not equals 0)
     * @dataProvider workflowReaderDataProvider
     *
     * @param WorkflowReader $reader
     * @throws \ReflectionException
     */
    public function testWorkflowWithQueries(WorkflowReader $reader): void
    {
        $prototype = $reader->fromClass(WorkflowWithQueries::class);

        $queries = \array_keys($prototype->getQueryHandlers());
        $this->assertSame(['a', 'b'], $queries);
    }

    /**
     * @testdox Reading workflow without signal methods (signal methods count equals 0)
     * @dataProvider workflowReaderDataProvider
     *
     * @param WorkflowReader $reader
     * @throws \ReflectionException
     */
    public function testWorkflowWithoutSignals(WorkflowReader $reader): void
    {
        $prototype = $reader->fromClass(SimpleWorkflow::class);

        $this->assertCount(0, $prototype->getSignalHandlers());
    }

    /**
     * @testdox Reading workflow with signal methods (signal methods count not equals 0)
     * @dataProvider workflowReaderDataProvider
     *
     * @param WorkflowReader $reader
     * @throws \ReflectionException
     */
    public function testWorkflowWithSignals(WorkflowReader $reader): void
    {
        $prototype = $reader->fromClass(WorkflowWithSignals::class);

        $signals = \array_keys($prototype->getSignalHandlers());

        $this->assertSame(['a', 'b'], $signals);
    }

    /**
     * @testdox Workflow should be named same as method name
     * @dataProvider workflowReaderDataProvider
     *
     * @param WorkflowReader $reader
     * @throws \ReflectionException
     */
    public function testWorkflowHandlerDefaultNaming(WorkflowReader $reader): void
    {
        $withoutName = $reader->fromClass(SimpleWorkflow::class);

        $this->assertSame('handler', $withoutName->getID());
    }

    /**
     * @testdox Workflow should be named same as the name specified in the workflow method attribute
     * @dataProvider workflowReaderDataProvider
     *
     * @param WorkflowReader $reader
     * @throws \ReflectionException
     */
    public function testWorkflowHandlerWithName(WorkflowReader $reader): void
    {
        $prototype = $reader->fromClass(WorkflowWithCustomName::class);

        $this->assertSame('ExampleWorkflowName', $prototype->getID());
    }
}
