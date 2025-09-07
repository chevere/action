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
 *
 * Use mixed __invoke() to define the action main logic.
 */
interface ActionInterface
{
    /**
     * Provides access to the parameters defined at the main method.
     */
    public static function parameters(): ParametersInterface;

    /**
     * Defines expected return parameter validation for __invoke method.
     */
    public static function return(): ParameterInterface;

    /**
     * Asserts action static rules coherence.
     */
    public static function assert(): ReflectionActionInterface;

    /**
     * Asserts action arguments coherence.
     *
     * @return array<int|string, mixed>
     */
    public function assertArguments(mixed ...$argument): array;

    public function assertReturn(mixed $return): mixed;
}
