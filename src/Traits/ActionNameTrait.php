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

trait ActionNameTrait
{
    public function __construct(
        private string $name
    ) {
        $this->onConstruct();
    }

    public function __toString(): string
    {
        /** @var class-string */
        return $this->name;
    }

    public function arguments(): array
    {
        return [];
    }

    public function isSubclassOf(string $class): bool
    {
        return is_subclass_of($this->name, $class, true);
    }

    public static function symbol(): string
    {
        return 'Action';
    }

    abstract public static function interface(): string;

    private function onConstruct(): void
    {
        if ($this->isSubclassOf($this::interface())) {
            return;
        }

        throw new InvalidArgumentException(
            (string) message(
                "{{ action }} `{{ name }}` doesn't implement `{{ interface }}`",
                action: $this::symbol(),
                name: $this->name,
                interface: $this->interface()
            )
        );
    }
}
