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
use Chevere\Parameter\Interfaces\ParametersAccessInterface;
use Chevere\Parameter\Interfaces\ParametersInterface;

/**
 * Describes the component in charge of defining a single logic action.
 * Use __invoke() method to define the action main logic.
 *
 * @method mixed __invoke()
 */
interface ActionInterface
{
    /**
     * Defines parameter validation for `__invoke()` argument values.
     *
     * This definition is an alternative to using `__invoke()` parameter
     * attributes, intended for cases where attributes are limited
     * (literal values, constant expressions).
     */
    public static function acceptParameters(): ParametersInterface|ParametersAccessInterface;

    /**
     * Defines expected return validation for `__invoke()` method return value.
     */
    public static function acceptReturn(): ParameterInterface;

    /**
     * Provide access to the reflection instance.
     */
    public static function reflection(): ReflectionActionInterface;

    /**
     * Assert `__invoke()` arguments against defined rules.
     *
     * @param mixed ...$argument Arguments to assert. If none provided will take from caller backtrace.
     * @return array<int|string, mixed> Asserted arguments
     */
    public function assertArguments(mixed ...$argument): array;

    /**
     * Asserts return value against defined rules.
     *
     * @return mixed Asserted return value.
     */
    public function assertReturn(mixed $return): mixed;

    /**
     * Asserts rules coherence.
     */
    public function assert(): void;

    /**
     * Defines static extra validation rules.
     */
    public static function acceptRulesStatic(): void;

    /**
     * Defines runtime extra validation rules.
     */
    public function acceptRulesRuntime(): void;
}
