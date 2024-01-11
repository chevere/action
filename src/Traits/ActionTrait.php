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

/**
 * @method mixed main()
 */
trait ActionTrait
{
    protected ?ParametersInterface $parameters = null;

    protected ?ParameterInterface $return = null;

    final public function __invoke(mixed ...$argument): mixed
    {
        try {
            $reflection = static::assert();
            // @infection-ignore-all
            $this->assertRuntime($reflection);
            $arguments = $reflection->parameters()->__invoke(...$argument);
        } catch (Throwable $e) {
            // @infection-ignore-all
            throw new ActionException(
                ...$this->getExceptionArguments($e),
            );
        }
        $result = $this->main(...$arguments->toArray());

        try {
            $reflection->return()->__invoke($run);
        } catch (Throwable $e) {
            // @infection-ignore-all
            throw new ActionException(
                ...$this->getExceptionArguments($e),
            );
        }

        return $result;
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

    // @phpstan-ignore-next-line
    private function getExceptionArguments(Throwable $e): array
    {
        // @infection-ignore-all
        $caller = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1];
        $message = (string) message(
            '`%actor%` %exception% → %message%',
            exception: $e::class,
            actor: static::class,
            message: $e->getMessage(),
        );

        // @infection-ignore-all
        return [
            $message,
            $e,
            $caller['file'] ?? 'na',
            $caller['line'] ?? 0,
        ];
    }
}
