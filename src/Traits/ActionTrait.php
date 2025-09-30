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
use InvalidArgumentException;
use ReflectionException;
use Throwable;
use function Chevere\Message\message;
use function Chevere\Parameter\mixed;

trait ActionTrait
{
    public function assertArguments(mixed ...$argument): array
    {
        static $cache = [];

        if ($argument === []) {
            $argument = $this->takeArguments(1);
        }

        try {
            $reflection = static::reflection();
            if (! isset($cache[static::class])) {
                static::acceptRulesStatic();
                $cache[static::class] = true;
            }
            $this->acceptRulesRuntime();

            return $reflection->parameters()
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
        static $cache = [];

        try {
            $reflection = static::reflection();
            if (! isset($cache[static::class])) {
                static::acceptRulesStatic();
                $cache[static::class] = true;
            }
            $this->acceptRulesRuntime();

            return $reflection->return()->__invoke($value);
        } catch (Throwable $e) {
            throw new ActionException(
                ...$this::getExceptionArguments($e),
            );
        }
    }

    public static function acceptReturn(): ParameterInterface
    {
        return mixed();
    }

    final public function assert(): void
    {
        static $cache = [];

        try {
            if (! isset($cache[static::class])) {
                static::reflection();
                static::acceptRulesStatic();
                $cache[static::class] = true;
            }
            $this->acceptRulesRuntime();
        } catch (Throwable $e) {
            throw new ActionException(
                // @phpstan-ignore-next-line
                ...self::getExceptionArguments($e),
            );
        }
    }

    final public static function reflection(): ReflectionActionInterface
    {
        static $cache = [];
        if (! isset($cache[static::class])) {
            $cache[static::class] = new ReflectionAction(static::class);
        }

        return $cache[static::class];
    }

    /**
     * @codeCoverageIgnore
     */
    public static function acceptRulesStatic(): void
    {
        // enables extra static assertion
    }

    /**
     * @codeCoverageIgnore
     */
    public function acceptRulesRuntime(): void
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
        $parameters = static::reflection()->parameters();
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
            'message' => $message,
            'previous' => $e,
            'file' => $caller['file'] ?? 'na',
            'line' => $caller['line'] ?? 0,
        ];
    }
}
