<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Tests\Testing;

use Illuminate\Support\Arr;
use PHPUnit\Framework\Assert;
use Temporal\DataConverter\Payload;
use Temporal\Internal\DataConverter\DataConverter;
use Temporal\Internal\DataConverter\NullConverter;
use Temporal\Internal\DataConverter\ScalarJsonConverter;
use Temporal\Worker\Command\RequestInterface;

/**
 * @template-extends TestingCommand<RequestInterface>
 */
class TestingRequest extends TestingCommand implements RequestInterface
{
    /**
     * @param RequestInterface $request
     */
    public function __construct(RequestInterface $request)
    {
        parent::__construct($request);
    }

    /**
     * @param string $expected
     * @param string $message
     * @return $this
     */
    public function assertName(string $expected, string $message = ''): self
    {
        Assert::assertSame($expected, $this->getName(), $message);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return $this->command->getName();
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function getParam(string $key)
    {
        return Arr::get($this->getParams(), $key);
    }

    /**
     * {@inheritDoc}
     */
    public function getParams(): array
    {
        return $this->command->getParams();
    }

    /**
     * @param string $expected
     * @param string $message
     * @return $this
     */
    public function assertParamsSame(array $expected, string $message = ''): self
    {
        Assert::assertSame($expected, $this->getParams(), $message);

        return $this;
    }

    /**
     * @param string $key
     * @param mixed $expected
     * @param string $message
     * @return $this
     */
    public function assertParamsKeySame(string $key, $expected, string $message = ''): self
    {
        if ($expected === null) {
            $this->assertParamsHasKey($key, $message);
        }

        Assert::assertEquals($expected, Arr::get($this->getParams(), $key), $message);

        return $this;
    }

    /**
     * @param string $expected
     * @param string $message
     * @return $this
     */
    public function assertParamsHasKey(string $key, string $message = ''): self
    {
        Assert::assertTrue(Arr::has($this->getParams(), $key), $message);

        return $this;
    }

    /**
     * @param string $key
     * @param mixed $expected
     * @param string $message
     * @return $this
     */
    public function assertParamsKeySamePayload(string $key, $expected, string $message = ''): self
    {
        if ($expected === null) {
            $this->assertParamsHasKey($key, $message);
        }

        if (is_array($expected)) {
            $expected = array_map([$this, 'convertValue'], $expected);
        } else {
            $expected = $this->convertValue($expected);
        }

        Assert::assertEquals($expected, Arr::get($this->getParams(), $key), $message);

        return $this;
    }

    private function convertValue($value): Payload
    {
        $dc = new DataConverter(new NullConverter(), new ScalarJsonConverter());

        return $dc->toPayloads([$value])[0];
    }

    /**
     * @param string $key
     * @param class-string $expected
     * @param string $message
     * @return $this
     */
    public function assertParamsKeyInstanceOf(string $key, string $expected, string $message = ''): self
    {
        Assert::assertInstanceOf($expected, Arr::get($this->getParams(), $key), $message);

        return $this;
    }

    /**
     * @param string $key
     * @param mixed $expected
     * @param string $message
     * @return $this
     */
    public function assertParamsKeyNotSame(string $key, $expected, string $message = ''): self
    {
        $this->assertParamsHasKey($key, $message);

        Assert::assertNotSame($expected, Arr::get($this->getParams(), $key), $message);

        return $this;
    }
}
