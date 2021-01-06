<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Internal\Transport;

use React\Promise\PromiseInterface;
use Temporal\Worker\Command\CommandInterface;
use Temporal\Worker\Command\RequestInterface;

interface ClientInterface
{
    /**
     * @param RequestInterface $request
     * @return PromiseInterface
     */
    public function request(RequestInterface $request): PromiseInterface;

    /**
     * @param CommandInterface $command
     * @return bool
     */
    public function isQueued(CommandInterface $command): bool;

    /**
     * @param CommandInterface $command
     */
    public function cancel(CommandInterface $command): void;
}
