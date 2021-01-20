<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Tests\Unit\Declaration;

use Temporal\Internal\Declaration\Prototype\ActivityPrototype;
use Temporal\Internal\Declaration\Reader\ActivityReader;
use Temporal\Tests\Unit\Declaration\Fixture\ActivitiesWithPublicMethods;
use Temporal\Tests\Unit\Declaration\Fixture\ChildActivityMethods;

/**
 * @group unit
 * @group declaration
 */
class ActivityDeclarationTestCase extends DeclarationTestCase
{
    /**
     * @param array<ActivityPrototype> $prototypes
     * @return array<string>
     */
    private function arrayToActivityNames(array $prototypes): array
    {
        return array_map(static fn(ActivityPrototype $proto) => $proto->getID(), $prototypes);
    }

    /**
     * @testdox Reading activities (should return activity prototypes for all non-static public methods)
     * @dataProvider activityReaderDataProvider
     *
     * @param ActivityReader $reader
     * @throws \ReflectionException
     */
    public function testActivitiesFromPublicNonStaticMethods(ActivityReader $reader): void
    {
        $prototypes = $reader->fromClass(ActivitiesWithPublicMethods::class);

        $this->assertCount(3, $prototypes);

        $this->assertSame(['a', 'b', 'c'], $this->arrayToActivityNames($prototypes));
    }

    /**
     * @testdox
     * @dataProvider activityReaderDataProvider
     *
     * @param ActivityReader $reader
     * @throws \ReflectionException
     */
    public function testInheritedActivities(ActivityReader $reader): void
    {
        $prototypes = $reader->fromClass(ChildActivityMethods::class);

        $this->assertCount(3, $prototypes);

        $names = $this->arrayToActivityNames($prototypes);

        $this->assertSame(['activityMethod', 'AlternativeActivityName', 'activityMethodFromParentClass'], $names);
    }
}
