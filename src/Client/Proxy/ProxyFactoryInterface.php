<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Client\Proxy;

interface ProxyFactoryInterface
{
    /**
     * @template T of object
     *
     * @param class-string<T> $class
     * @param object $delegate
     * @return T
     */
    public function create(string $class, object $delegate): object;
}
