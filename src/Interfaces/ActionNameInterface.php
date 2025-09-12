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

use Stringable;

/**
 * Describes the component in charge of handling Action participants.
 * Will contain the Action name plus its setUp arguments.
 */
interface ActionNameInterface extends Stringable
{
    /**
     * @return class-string Action class name
     */
    public function __toString(): string;

    /**
     * @return array<string|int, mixed> Arguments to be passed to the named class `setUp` method.
     */
    public function arguments(): array;

    /**
     * Returns a boolean if the object has this class as one of its parents or implements it.
     */
    public function isSubclassOf(string $class): bool;

    /**
     * Returns the symbol representing the named class type.
     * e.g. "Action", "Controller"
     */
    public static function symbol(): string;

    /**
     * Returns the interface that the named class must implement.
     *
     * @return class-string
     */
    public static function interface(): string;
}
