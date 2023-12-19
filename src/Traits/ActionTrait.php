<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevere.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Action\Traits;

use Chevere\Parameter\Attributes\ReturnAttr;
use Chevere\Parameter\Cast;
use Chevere\Parameter\Interfaces\CastInterface;
use Chevere\Parameter\Interfaces\ParameterInterface;
use Chevere\Parameter\Interfaces\ParametersInterface;
use Chevere\Parameter\Interfaces\UnionParameterInterface;
use LogicException;
use ReflectionMethod;
use ReflectionNamedType;
use Throwable;
use TypeError;
use function Chevere\Action\getParameters;
use function Chevere\Message\message;
use function Chevere\Parameter\mixed;
use function Chevere\Parameter\reflectionToReturnParameter;

/**
 * @method mixed run()
 */
trait ActionTrait
{
    protected ?ParametersInterface $parameters = null;

    protected ?ParameterInterface $return = null;

    final public function __invoke(mixed ...$argument): CastInterface
    {
        /**
         * @var ReflectionMethod $reflection
         * @var ParameterInterface $return
         */
        [$reflection, $return] = static::assert();
        // @infection-ignore-all
        $this->assertRuntime($reflection, $return);

        try {
            $arguments = $this->parameters()->__invoke(...$argument);
        } catch (Throwable $e) {
            throw new ($e::class)($this->getInvokeErrorMessage($e));
        }
        $run = $this->run(...$arguments->toArray());

        try {
            $return->__invoke($run);
        } catch (Throwable $e) {
            throw new ($e::class)($this->getInvokeErrorMessage($e));
        }

        return new Cast($run);
    }

    public static function return(): ParameterInterface
    {
        return mixed();
    }

    public static function runMethod(): string
    {
        return 'run';
    }

    /**
     * @return array<ReflectionMethod|ParameterInterface>
     */
    protected static function assert(): array
    {
        /**
         * @var ReflectionMethod $reflection
         * @var ParameterInterface $return
         */
        [$reflection, $return] = static::assertMethod();
        /**
         * @var ?ReflectionNamedType $returnType
         * @phpstan-ignore-next-line
         */
        $returnType = $reflection->getReturnType();
        if ($returnType !== null) {
            // @phpstan-ignore-next-line
            static::assertTypes($returnType, $return);
        }
        static::assertStatic($reflection, $return);

        return [$reflection, $return];
    }

    protected function getInvokeErrorMessage(Throwable $e): string
    {
        return (string) message(
            '`%method%` → %message%',
            method: static::runMethodFQN(),
            message: $e->getMessage(),
        );
    }

    /**
     * @return array<object> [$reflection, $return]
     */
    final protected static function assertMethod(): array
    {
        if (! method_exists(static::class, static::runMethod())) {
            throw new LogicException(
                (string) message(
                    'Action `%action%` does not define `%run%` method',
                    action: static::class,
                    run: static::runMethod(),
                )
            );
        }
        $reflection = new ReflectionMethod(static::class, static::runMethod());
        $attributes = $reflection->getAttributes(ReturnAttr::class);
        if ($attributes === []) {
            $return = static::return();
        } else {
            $return = reflectionToReturnParameter($reflection);
        }
        if (! $reflection->hasReturnType()) {
            if ($return->type()->typeHinting() === 'null') {
                return [$reflection, $return];
            }

            throw new TypeError(
                (string) message(
                    'Method `%method%` must declare `%type%` return type',
                    method: static::runMethodFQN(),
                    type: $return->type()->typeHinting(),
                )
            );
        }

        return [$reflection, $return];
    }

    /**
     * Enables to define extra parameter assertion before the run method is called.
     * @codeCoverageIgnore
     */
    protected static function assertStatic(
        ReflectionMethod $reflection,
        ParameterInterface $return
    ): void {
        // enables extra static assertion
    }

    /**
     * Enables to define extra parameter assertion before the run method is called.
     * @codeCoverageIgnore
     */
    protected function assertRuntime(
        ReflectionMethod $reflection,
        ParameterInterface $return
    ): void {
        // enables extra runtime assertion
    }

    final protected function parameters(): ParametersInterface
    {
        if ($this->parameters === null) {
            $this->parameters = getParameters(static::class);
        }

        return $this->parameters;
    }

    final protected static function runMethodFQN(): string
    {
        return static::class . '::' . static::runMethod();
    }

    final protected static function assertTypes(
        ReflectionNamedType $reflection,
        ParameterInterface $parameter
    ): void {
        $returnName = $reflection->getName();
        $expectName = $parameter->type()->typeHinting();
        $return = match ($returnName) {
            'void' => 'null',
            'ArrayAccess' => 'array',
            default => $returnName,
        };
        $expect = [];
        if ($parameter instanceof UnionParameterInterface) {
            foreach ($parameter->parameters() as $parameter) {
                $expect[] = $parameter->type()->typeHinting();
            }
        } else {
            $expect[] = match ($expectName) {
                'generic' => 'array',
                default => $expectName,
            };
        }
        if (in_array('mixed', $expect, true)) {
            return;
        }
        if (! in_array($return, $expect, true)) {
            throw new TypeError(
                (string) message(
                    'Method `%method%` must declare `%type%` return type',
                    method: static::runMethodFQN(),
                    type: implode('|', $expect),
                )
            );
        }
    }
}
