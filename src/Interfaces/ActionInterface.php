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

/**
 * Describes the component in charge of defining a single logic action.
 * @method mixed main() Defines the action main logic.
 */
interface ActionInterface
{
    /**
     * Run `main` for `...$argument` validating all I/O rules.
     */
    public function __invoke(mixed ...$argument): mixed;

    /**
     * Defines expected return parameter validation for main method.
     */
    public static function return(): ParameterInterface;

    /**
     * Defines main method to use.
     */
    public static function mainMethod(): string;

    /**
     * Asserts action rules coherence.
     */
    public static function assert(): ReflectionActionInterface;
}
