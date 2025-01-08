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

use InvalidArgumentException;
use function Chevere\Message\message;

trait ControllerNameTrait
{
    public function __construct(
        private string $name
    ) {
        if ($this->isSubclassOf($this::interface())) {
            return;
        }

        throw new InvalidArgumentException(
            (string) message(
                "Controller `{{ name }}` doesn't implement `{{ interface }}`",
                name: $this->name,
                interface: $this->interface()
            )
        );
    }

    public function __toString(): string
    {
        /** @var class-string */
        return $this->name;
    }

    public function isSubclassOf(string $class): bool
    {
        return is_subclass_of($this->name, $class, true);
    }

    abstract public static function interface(): string;
}
