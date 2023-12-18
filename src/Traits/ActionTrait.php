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
use function Chevere\Parameter\arguments;
use function Chevere\Parameter\mixed;
use function Chevere\Parameter\reflectionToReturnParameter;

/**
 * @method mixed run()
 */
trait ActionTrait
{
    public const RUN_METHOD = 'run';

    protected ?ParametersInterface $parameters = null;

    protected ?ParameterInterface $return = null;

    final public function __invoke(mixed ...$argument): CastInterface
    {
        static::assert();
        // @infection-ignore-all
        $this->assertRuntime();
        $arguments = arguments($this->parameters(), $argument)->toArray();
        $run = $this->run(...$arguments);

        try {
            static::return()(...)($run);
        } catch (Throwable $e) {
            $message = (string) message(
                '`%method%` → %message%',
                method: static::runMethodFQN(),
                message: $e->getMessage(),
            );

            throw new ($e::class)($message);
        }

        return new Cast($run);
    }

    public static function return(): ParameterInterface
    {
        return mixed();
    }

    final public static function assert(): void
    {
        [$method, $return] = static::assertMethod();
        /**
         * @var ?ReflectionNamedType $returnType
         * @phpstan-ignore-next-line
         */
        $returnType = $method->getReturnType();
        if ($returnType !== null) {
            // @phpstan-ignore-next-line
            static::assertTypes($returnType, $return);
        }
        static::assertStatic();
    }

    /**
     * @return array<object> [$method, $return]
     */
    final protected static function assertMethod(): array
    {
        if (! method_exists(static::class, static::RUN_METHOD)) {
            throw new LogicException(
                (string) message(
                    'Action `%action%` does not define a %run% method',
                    action: static::class,
                    run: static::RUN_METHOD,
                )
            );
        }
        $reflection = new ReflectionMethod(static::class, static::RUN_METHOD);
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
    protected static function assertStatic(): void
    {
        // enables extra static assertion
    }

    /**
     * Enables to define extra parameter assertion before the run method is called.
     * @codeCoverageIgnore
     */
    protected function assertRuntime(): void
    {
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
        return static::class . '::' . static::RUN_METHOD;
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
