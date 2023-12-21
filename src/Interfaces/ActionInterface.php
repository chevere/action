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

use Chevere\Parameter\Interfaces\CastInterface;
use Chevere\Parameter\Interfaces\ParameterInterface;

/**
 * Describes the component in charge of defining a single logic action.
 * @method mixed main() Defines the action main logic.
 */
interface ActionInterface
{
    /**
     * Retrieves `main` return validated against all rules.
     */
    public function __invoke(mixed ...$argument): CastInterface;

    /**
     * Defines expected return parameter when executing target main method.
     */
    public static function return(): ParameterInterface;

    /**
     * Determines main method to use.
     */
    public static function mainMethod(): string;
}
