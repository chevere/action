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
 * @method mixed run() Defines the action run logic.
 */
interface ActionInterface
{
    /**
     * Retrieves `run` return validated against `return` method.
     */
    public function __invoke(mixed ...$argument): CastInterface;

    /**
     * Defines expected return parameter when executing `RUN_METHOD` method.
     */
    public static function return(): ParameterInterface;

    /**
     * Assert for static context.
     */
    public static function assert(): void;
}
