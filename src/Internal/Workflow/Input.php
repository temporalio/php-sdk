<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Internal\Workflow;

use JetBrains\PhpStorm\Immutable;
use Temporal\DataConverter\Payload;
use Temporal\Internal\Marshaller\Meta\Marshal;
use Temporal\Internal\Marshaller\Meta\MarshalArray;
use Temporal\Workflow\WorkflowInfo;

#[Immutable]
final class Input
{
    /**
     * @var WorkflowInfo
     */
    #[Marshal(name: 'info')]
    #[Immutable]
    public WorkflowInfo $info;

    /**
     * @var array
     */
    #[MarshalArray(name: 'args', of: Payload::class)]
    #[Immutable]
    public array $args;

    /**
     * @param WorkflowInfo $info
     * @param array<Payload> $args
     */
    public function __construct(WorkflowInfo $info = null, array $args = [])
    {
        $this->info = $info ?? new WorkflowInfo();
        $this->args = $args;
    }
}
