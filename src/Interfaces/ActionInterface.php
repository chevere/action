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

namespace Chevere\Action\Interfaces;

use Chevere\Parameter\Interfaces\ParameterInterface;
use Chevere\Parameter\Interfaces\ParametersInterface;

/**
 * Describes the component in charge of defining a single logic action.
 * Use mixed __invoke() to define the action main logic.
 *
 * @method mixed __invoke()
 */
interface ActionInterface
{
    /**
     * Provides access to parameters defined at the `__invoke` method.
     */
    public static function parameters(): ParametersInterface;

    /**
     * Defines expected return parameter validation for `__invoke` method.
     */
    public static function return(): ParameterInterface;

    /**
     * Returns asserted reflection against static rules.
     */
    public static function reflection(): ReflectionActionInterface;

    /**
     * Asserts action `__invoke` arguments against defined rules.
     *
     * @param mixed ...$argument Arguments to assert. If none provided will
     * take from caller backtrace.
     * @return array<int|string, mixed>
     */
    public function assertArguments(mixed ...$argument): array;

    /**
     * Asserts action return value against defined rules.
     */
    public function assertReturn(mixed $return): mixed;
}
