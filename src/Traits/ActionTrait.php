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

use Chevere\Action\Exceptions\ActionException;
use Chevere\Action\Interfaces\ReflectionActionInterface;
use Chevere\Action\ReflectionAction;
use Chevere\Parameter\Cast;
use Chevere\Parameter\Interfaces\CastInterface;
use Chevere\Parameter\Interfaces\ParameterInterface;
use Chevere\Parameter\Interfaces\ParametersInterface;
use Chevere\Parameter\Interfaces\UnionParameterInterface;
use ReflectionNamedType;
use Throwable;
use TypeError;
use function Chevere\Message\message;
use function Chevere\Parameter\mixed;

/**
 * @method mixed main()
 */
trait ActionTrait
{
    protected ?ParametersInterface $parameters = null;

    protected ?ParameterInterface $return = null;

    final public function __invoke(mixed ...$argument): CastInterface
    {
        try {
            $reflection = static::assert();
            // @infection-ignore-all
            $this->assertRuntime($reflection);
            $arguments = $reflection->parameters()
                ->__invoke(...$argument);
        } catch (Throwable $e) {
            // @infection-ignore-all
            throw new ActionException(
                ...$this->getExceptionArguments($e),
            );
        }
        $run = $this->main(...$arguments->toArray());

        try {
            $reflection->return()
                ->__invoke($run);
        } catch (Throwable $e) {
            // @infection-ignore-all
            throw new ActionException(
                ...$this->getExceptionArguments($e),
            );
        }

        return new Cast($run);
    }

    public static function return(): ParameterInterface
    {
        return mixed();
    }

    public static function mainMethod(): string
    {
        return 'main';
    }

    final public static function assert(): ReflectionActionInterface
    {
        $reflection = new ReflectionAction(static::class);
        /**
         * @var ?ReflectionNamedType $returnType
         * @phpstan-ignore-next-line
         */
        $returnType = $reflection->method()->getReturnType();
        if ($returnType !== null) {
            // @phpstan-ignore-next-line
            static::assertTypes($returnType, $reflection->return());
        }
        static::assertStatic($reflection);

        return $reflection;
    }

    /**
     * Enables to define extra parameter assertion before the run method is called.
     * @codeCoverageIgnore
     */
    protected static function assertStatic(ReflectionActionInterface $reflection): void
    {
        // enables extra static assertion
    }

    /**
     * Enables to define extra parameter assertion before the run method is called.
     * @codeCoverageIgnore
     */
    protected function assertRuntime(ReflectionActionInterface $reflection): void
    {
        // enables extra runtime assertion
    }

    final protected static function mainMethodFQN(): string
    {
        return static::class . '::' . static::mainMethod();
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
                    method: static::mainMethodFQN(),
                    type: implode('|', $expect),
                )
            );
        }
    }

    // @phpstan-ignore-next-line
    private function getExceptionArguments(Throwable $e): array
    {
        // @infection-ignore-all
        $caller = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1];

        $message = (string) message(
            '`%action%` %exception% → %message%',
            exception: $e::class,
            action: static::class,
            method: static::mainMethod(),
            message: $e->getMessage(),
        );

        return [
            $message,
            $e,
            $caller['file'] ?? 'na',
            $caller['line'] ?? 0,
        ];
    }
}
