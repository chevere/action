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
use Chevere\Parameter\Interfaces\ParameterInterface;
use Chevere\Parameter\Interfaces\StringParameterInterface;
use InvalidArgumentException;
use ReflectionMethod;
use function Chevere\Message\message;
use function Chevere\Parameter\reflectionToParameters;

abstract class Controller implements ControllerInterface
{
    use ActionTrait;

    // @infection-ignore-all
    protected static function assertStatic(
        ReflectionMethod $reflection,
        ParameterInterface $return
    ): void {
        $invalid = [];
        $parameters = reflectionToParameters($reflection);
        foreach ($parameters as $name => $parameter) {
            if (! ($parameter instanceof StringParameterInterface)) {
                $invalid[] = $name;
            }
        }
        if ($invalid === []) {
            return;
        }

        throw new InvalidArgumentException(
            (string) message(
                'Parameter `%parameters%` must be of type **%type%** for controller **%className%**',
                parameters: implode(', ', $invalid),
                type: 'string',
                className: static::class
            )
        );
    }
}
