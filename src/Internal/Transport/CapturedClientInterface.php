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

interface CapturedClientInterface extends ClientInterface, \Countable, \IteratorAggregate
{
    /**
     * @return array<positive-int, PromiseInterface>
     */
    public function fetchUnresolvedRequests(): array;
}
