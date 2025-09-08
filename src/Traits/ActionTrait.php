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
use InvalidArgumentException;
use ReflectionException;
use Throwable;
use function Chevere\Message\message;
use function Chevere\Parameter\mixed;

trait ActionTrait
{
    private ReflectionActionInterface $_reflection;

    public function assertArguments(mixed ...$argument): array
    {
        if ($argument === []) {
            $argument = $this->takeArguments(1);
        }

        try {
            $this->assertRuntime($this->reflection());

            return $this->reflection()->parameters()
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
            $this->assertRuntime($this->reflection());

            return $this->reflection()->return()->__invoke($value);
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
            $reflection = static::newReflection();

            return $reflection->parameters();
        } catch (Throwable $e) {
            throw new ActionException(
                // @phpstan-ignore-next-line
                ...self::getExceptionArguments($e),
            );
        }
    }

    final public function assert(): void
    {
        try {
            $this->assertRuntime($this->reflection());
        } catch (Throwable $e) {
            throw new ActionException(
                // @phpstan-ignore-next-line
                ...self::getExceptionArguments($e),
            );
        }
    }

    final public function reflection(): ReflectionActionInterface
    {
        return $this->_reflection ??= static::newReflection();
    }

    final public static function newReflection(): ReflectionActionInterface
    {
        $reflection = new ReflectionAction(static::class);
        static::assertStatic($reflection);

        return $reflection;
    }

    /**
     * Enables to define extra parameter assertion before the run method is called.
     *
     * @codeCoverageIgnore
     */
    protected static function assertStatic(ReflectionActionInterface $reflection): void
    {
        // enables extra static assertion
    }

    /**
     * Enables to define runtime assertions that will run on `assert()`.
     *
     * @codeCoverageIgnore
     */
    protected function assertRuntime(ReflectionActionInterface $reflection): void
    {
        // enables extra runtime assertion
    }

    /**
     * Return an array with the passed function arguments from the backtrace position.
     * This is negligible (~0-2.5%) slower than using direct argument access.
     *
     * @return array<string, mixed>
     *
     * @throws ReflectionException
     * @throws InvalidArgumentException
     */
    private function takeArguments(int $pos): array
    {
        $tracePos = 2 + $pos;
        $trace = debug_backtrace(0, $tracePos);
        $caller = $trace[$tracePos - 1];
        $args = $caller['args'] ?? [];
        $parameters = $this->reflection()->parameters();
        $pos = -1;
        $arguments = [];
        foreach ($parameters->keys() as $named) {
            $pos++;
            if (! isset($args[$pos])) {
                continue;
            }
            $arguments[$named] = $args[$pos];
        }

        return $arguments;
    }

    /**
     * @infection-ignore-all
     */
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
