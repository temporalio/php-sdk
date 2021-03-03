<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Client\Proxy;

use JetBrains\PhpStorm\Pure;
use ProxyManager\Configuration;
use ProxyManager\Factory\LazyLoadingValueHolderFactory;
use ProxyManager\FileLocator\FileLocator;
use ProxyManager\GeneratorStrategy\EvaluatingGeneratorStrategy;
use ProxyManager\GeneratorStrategy\FileWriterGeneratorStrategy;
use ProxyManager\Inflector\ClassNameInflectorInterface;
use ProxyManager\Signature\ClassSignatureGeneratorInterface;
use ProxyManager\Signature\SignatureCheckerInterface;
use ProxyManager\Signature\SignatureGeneratorInterface;

class ProxyFactory implements ProxyFactoryInterface
{
    /**
     * @var Configuration
     */
    private Configuration $config;

    /**
     * @var LazyLoadingValueHolderFactory
     */
    private LazyLoadingValueHolderFactory $factory;

    /**
     * @param Configuration|null $config
     */
    #[Pure]
    public function __construct(Configuration $config = null)
    {
        $this->config = $config ?? new Configuration();
        $this->factory = new LazyLoadingValueHolderFactory($this->config);
    }

    /**
     * @param string $namespace
     * @return $this
     */
    public function withNamespace(string $namespace): self
    {
        $self = clone $this;
        $self->config->setProxiesNamespace($namespace);

        return $self;
    }

    /**
     * @param ClassNameInflectorInterface $inflector
     * @return $this
     */
    public function withClassNameInflector(ClassNameInflectorInterface $inflector): self
    {
        $self = clone $this;
        $self->config->setClassNameInflector($inflector);

        return $self;
    }

    /**
     * @param SignatureGeneratorInterface $generator
     * @return $this
     */
    public function withSignatureGenerator(SignatureGeneratorInterface $generator): self
    {
        $self = clone $this;
        $self->config->setSignatureGenerator($generator);

        return $self;
    }

    /**
     * @param SignatureCheckerInterface $checker
     * @return $this
     */
    public function withSignatureChecker(SignatureCheckerInterface $checker): self
    {
        $self = clone $this;
        $self->config->setSignatureChecker($checker);

        return $self;
    }

    /**
     * @param ClassSignatureGeneratorInterface $generator
     * @return $this
     */
    public function withClassSignatureGenerator(ClassSignatureGeneratorInterface $generator): self
    {
        $self = clone $this;
        $self->config->setClassSignatureGenerator($generator);

        return $self;
    }

    /**
     * @param string|null $directory
     * @return $this
     */
    public function withProxyCacheDirectory(?string $directory): self
    {
        $generator = $directory === null
            ? new EvaluatingGeneratorStrategy()
            : new FileWriterGeneratorStrategy(new FileLocator($directory))
        ;

        $self = clone $this;
        $self->config->setGeneratorStrategy($generator);

        return $self;
    }

    /**
     * @param callable(Configuration): void $then
     * @return $this
     */
    public function withConfig(callable $then): self
    {
        $self = clone $this;
        $then($self->config);

        return $self;
    }

    /**
     * {@inheritDoc}
     */
    public function create(string $class, object $delegate): object
    {
        return $this->factory->createProxy($class, $this->initializer($delegate));
    }

    /**
     * @param object $delegate
     * @return \Closure
     */
    private function initializer(object $delegate): \Closure
    {
        return static function (&$ctx, $realProxy, string $method, array $params, &$initializer) use ($delegate) {
            $ctx = $delegate;
            $initializer = null;
        };
    }

    /**
     * @return void
     */
    public function __clone()
    {
        $this->config = clone $this->config;
    }
}
