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
use Chevere\Action\Traits\ActionTrait;
use InvalidArgumentException;
use ReflectionNamedType;
use function Chevere\Message\message;

abstract class Controller implements ControllerInterface
{
    use ActionTrait;

    public static function acceptRulesStatic(): void
    {
        $invalid = [];
        foreach (static::reflection()->method()->getParameters() as $parameter) {
            $name = $parameter->getName();
            $type = $parameter->getType();
            if ($type === null) {
                $invalid[] = $name;

                continue;
            }
            $types = match (true) {
                $type instanceof ReflectionNamedType => [$type],
                default => $type->getTypes(), // @phpstan-ignore-line
            };
            foreach ($types as $item) {
                /** @var ReflectionNamedType $item */
                if (! in_array($item->getName(), ['string', 'float', 'int'], true)) {
                    $invalid[] = $name;

                    break;
                }
            }
        }
        if ($invalid === []) {
            return;
        }
        $names = implode('`, `', $invalid);
        $subjects = "`{$names}`";

        throw new InvalidArgumentException(
            (string) message(
                'Parameter(s) %parameters% must be compatible with type **%type%** at `%method%` method',
                parameters: $subjects,
                type: 'string|int|float',
                method: static::class . '->__invoke()',
            )
        );
    }
}
