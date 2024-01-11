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

namespace Chevere\Action;

use Chevere\Action\Interfaces\ControllerInterface;
use Chevere\Action\Interfaces\ReflectionActionInterface;
use Chevere\Action\Traits\ActionTrait;
use InvalidArgumentException;
use ReflectionNamedType;
use function Chevere\Message\message;

abstract class Controller implements ControllerInterface
{
    use ActionTrait;

    // @infection-ignore-all
    protected static function assertStatic(
        ReflectionActionInterface $reflection
    ): void {
        $invalid = [];
        foreach ($reflection->method()->getParameters() as $parameter) {
            $name = $parameter->getName();
            $type = $parameter->getType();
            if ($type === null) {
                $invalid[] = $name;

                continue;
            }
            if ($type instanceof ReflectionNamedType) {
                $type = $type->getName();
                if ($type !== 'string') {
                    $invalid[] = $name;
                }

                continue;
            }
            $invalid[] = $name;
        }
        if ($invalid === []) {
            return;
        }
        $names = implode(', ', $invalid);

        throw new InvalidArgumentException(
            (string) message(
                'Parameter `%names%` must be of type **%type%** for controller `%className%`',
                names: $names,
                type: 'string',
                className: static::class
            )
        );
    }
}
