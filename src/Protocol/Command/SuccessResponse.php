<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Client\Protocol\Command;

class SuccessResponse extends Response implements SuccessResponseInterface
{
    /**
     * @var mixed
     */
    protected $result;

    /**
     * @param mixed $result
     * @param int|null $id
     */
    public function __construct($result, int $id = null)
    {
        $this->result = $result;

        parent::__construct($id);
    }

    /**
     * {@inheritDoc}
     */
    public function getResult()
    {
        return $this->result;
    }
}