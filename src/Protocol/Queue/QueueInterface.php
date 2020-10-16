<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Client\Protocol\Queue;

use Temporal\Client\Protocol\Command\CommandInterface;

/**
 * @implements \IteratorAggregate<array-key, CommandInterface>
 */
interface QueueInterface extends \IteratorAggregate, \Countable
{
    /**
     * @param CommandInterface $command
     */
    public function push(CommandInterface $command): void;
}