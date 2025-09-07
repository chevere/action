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
use Chevere\Parameter\Interfaces\ParameterInterface;
use Chevere\Parameter\Interfaces\ParametersInterface;
use Throwable;
use function Chevere\Message\message;
use function Chevere\Parameter\mixed;

trait ActionTrait
{
    private ReflectionActionInterface $_reflection;

    public function assertArguments(mixed ...$argument): array
    {
        try {
            $this->_reflection ??= static::assert();
            $this->assertRuntime($this->_reflection);

            return $this->_reflection->parameters()
                ->__invoke(...$argument)
                ->toArray();
        } catch (Throwable $e) {
            throw new ActionException(
                ...$this::getExceptionArguments($e),
            );
        }
    }

    /**
     * @return mixed Same as $value
     * @throws ActionException
     */
    public function assertReturn(mixed $value = null): mixed
    {
        try {
            $this->_reflection ??= static::assert();
            $this->assertRuntime($this->_reflection);

            return $this->_reflection->return()->__invoke($value);
        } catch (Throwable $e) {
            throw new ActionException(
                ...$this::getExceptionArguments($e),
            );
        }
    }

    public static function return(): ParameterInterface
    {
        return mixed();
    }

    final public static function parameters(): ParametersInterface
    {
        try {
            $reflection = static::assert();

            return $reflection->parameters();
        } catch (Throwable $e) {
            throw new ActionException(
                // @phpstan-ignore-next-line
                ...self::getExceptionArguments($e),
            );
        }
    }

    final public static function assert(): ReflectionActionInterface
    {
        $reflection = new ReflectionAction(static::class);
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

    private static function getExceptionArguments(Throwable $e): array
    {
        $caller = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1];
        $message = (string) message(
            '`%actor%` %exception% → %message%',
            exception: $e::class,
            actor: static::class,
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
