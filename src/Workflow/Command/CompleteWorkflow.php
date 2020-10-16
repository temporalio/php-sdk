<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Client\Workflow\Command;

use Temporal\Client\Protocol\Command\Request;

class CompleteWorkflow extends Request
{
    /**
     * @var string
     */
    public const NAME = 'CompleteWorkflow';

    /**
     * @param mixed $result
     */
    public function __construct($result)
    {
        parent::__construct(self::NAME, [
            'result' => $result,
        ]);
    }
}