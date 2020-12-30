<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Tests\Testing;

use JetBrains\PhpStorm\Immutable;
use React\Promise\PromiseInterface;
use Temporal\Internal\Queue\QueueInterface;
use Temporal\Internal\Transport\CapturedClient;
use Temporal\Internal\Transport\Client;
use Temporal\Worker\Command\ErrorResponse;
use Temporal\Worker\Command\RequestInterface;
use Temporal\Worker\Command\SuccessResponse;
use Temporal\Worker\LoopInterface;

class TestingClient extends CapturedClient
{
    /**
     * @var TestingQueue
     */
    #[Immutable]
    public TestingQueue $queue;

    /**
     * @param QueueInterface|null $queue
     */
    public function __construct(LoopInterface $loop, QueueInterface $queue = null)
    {
        $this->queue = $queue ?? new TestingQueue();

        parent::__construct(new Client($this->queue, $loop));
    }

    /**
     * @param RequestInterface $request
     * @param mixed|null $payload
     * @return TestingSuccessResponse
     */
    public function success(RequestInterface $request, $payload = null): TestingSuccessResponse
    {
        $response = new SuccessResponse($payload, $request->getId());

        $this->parent->dispatch($response);

        return new TestingSuccessResponse($response);
    }

    /**
     * @param RequestInterface $request
     * @param \Throwable $error
     * @return TestingErrorResponse
     */
    public function error(RequestInterface $request, \Throwable $error): TestingErrorResponse
    {
        $response = ErrorResponse::fromException($error, $request->getId());

        $this->parent->dispatch($response);

        return new TestingErrorResponse($response);
    }

    /**
     * {@inheritDoc}
     */
    public function request(RequestInterface $request): PromiseInterface
    {
        if (! $request instanceof TestingRequest) {
            $request = new TestingRequest($request);
        }

        return parent::request($request);
    }
}
