<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\DataConverter;

use Temporal\Exception\DataConverterException;

interface DataConverterInterface
{
    /**
     * @param array<Payload> $payloads
     * @param array<\ReflectionType> $types
     * @return array
     *
     * @throws DataConverterException
     */
    public function fromPayloads(array $payloads, array $types): array;

    /**
     * @param array $values
     * @return array<Payload>
     *
     * @throws DataConverterException
     */
    public function toPayloads(array $values): array;
}
