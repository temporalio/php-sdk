<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Tests\Unit\DTOMarshalling;

use Spiral\Attributes\AttributeReader;
use Temporal\Internal\Marshaller\Mapper\AttributeMapperFactory;
use Temporal\Internal\Marshaller\Marshaller;
use Temporal\Internal\Marshaller\MarshallerInterface;
use Temporal\Tests\Unit\UnitTestCase;

/**
 * @group unit
 * @group dto-marshalling
 */
abstract class DTOMarshallingTestCase extends UnitTestCase
{
    /**
     * @var MarshallerInterface
     */
    private MarshallerInterface $marshaller;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->marshaller = new Marshaller(
            new AttributeMapperFactory(
                new AttributeReader()
            )
        );
    }

    /**
     * @return void
     */
    abstract public function testMarshalling(): void;

    /**
     * @param object $object
     * @return array
     * @throws \ReflectionException
     */
    protected function marshal(object $object): array
    {
        return $this->marshaller->marshal($object);
    }
}
