<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Client\Workflow\Runtime;

use React\Promise\PromiseInterface;
use Temporal\Client\Activity\ActivityOptions;

interface InteractWithQueueInterface
{
    /**
     * @param mixed $result
     * @return PromiseInterface
     */
    public function complete($result = null): PromiseInterface;

    /**
     * @param string $name
     * @param array $arguments
     * @param ActivityOptions|array|null $options
     * @return PromiseInterface
     */
    public function executeActivity(string $name, array $arguments = [], $options = null): PromiseInterface;
}