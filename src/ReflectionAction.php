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

use Chevere\Action\Interfaces\ActionInterface;
use Chevere\Action\Interfaces\ReflectionActionInterface;
use Chevere\Parameter\Attributes\ReturnAttr;
use Chevere\Parameter\Interfaces\ParameterInterface;
use Chevere\Parameter\Interfaces\ParametersInterface;
use Chevere\Parameter\Interfaces\UnionParameterInterface;
use LogicException;
use ReflectionIntersectionType;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionUnionType;
use TypeError;
use function Chevere\Message\message;
use function Chevere\Parameter\reflectionToParameters;
use function Chevere\Parameter\reflectionToReturn;

final class ReflectionAction implements ReflectionActionInterface
{
    private ReflectionMethod $method;

    private ParametersInterface $parameters;

    private ParameterInterface $return;

    /**
     * @param class-string<ActionInterface> $action
     */
    public function __construct(
        string $action
    ) {
        if (! class_exists($action)) {
            throw new LogicException(
                (string) message("Action doesn't exists")
            );
        }
        $interfaces = class_implements($action) ?: [];
        if (! in_array(ActionInterface::class, $interfaces, true)) {
            throw new LogicException(
                (string) message(
                    "Action doesn't implement `%interface%`",
                    interface: ActionInterface::class,
                )
            );
        }
        /**
         * @var class-string<ActionInterface> $action
         * @phpstan-ignore-next-line
         */
        if (! method_exists($action, '__invoke')) {
            throw new LogicException(
                (string) message(
                    "Action doesn't define a `__invoke` method",
                )
            );
        }
        $this->method = new ReflectionMethod($action, '__invoke');
        $this->parameters = reflectionToParameters($this->method);
        $attributes = $this->method->getAttributes(ReturnAttr::class);
        $this->return = match (true) {
            $attributes === [] => $action::acceptReturn(),
            default => reflectionToReturn($this->method),
        };
        if (! $this->method->hasReturnType()) {
            if ($this->return->type()->typeHinting() === 'null') {
                return;
            }

            throw new TypeError(
                (string) message(
                    'Action `__invoke` method must declare `%type%` return type',
                    type: $this->return->type()->typeHinting(),
                )
            );
        }
        $this->assertReturn();
    }

    public function method(): ReflectionMethod
    {
        return $this->method;
    }

    public function parameters(): ParametersInterface
    {
        return $this->parameters;
    }

    public function return(): ParameterInterface
    {
        return $this->return;
    }

    private function assertReturn(): void
    {
        $expect = [];
        if ($this->return instanceof UnionParameterInterface) {
            foreach ($this->return->parameters() as $parameter) {
                $expect[] = $parameter->type()->typeHinting();
            }
        } else {
            $expect[] = $this->return->type()->typeHinting();
        }
        if (in_array('mixed', $expect, true)) {
            return;
        }
        if (in_array('iterable', $expect, true)) {
            $expect[] = 'array';
            $expect[] = 'Traversable';
        }
        /** @var ReflectionNamedType|ReflectionUnionType $type */
        $type = $this->method->getReturnType();
        if ($type instanceof ReflectionUnionType) {
            $typeName = [];
            foreach ($type->getTypes() as $unionType) {
                if ($unionType instanceof ReflectionIntersectionType) {
                    continue;
                }
                $typeName[] = $unionType->getName();
            }
        } else {
            $typeName = $type->getName();
        }
        $return = match ($typeName) {
            'void' => 'null',
            default => $typeName,
        };
        if (is_array($return) && $expect === $return) {
            return;
        }
        if (! in_array($return, $expect, true)) {
            throw new TypeError(
                (string) message(
                    'Action `__invoke` method must declare `%type%` return type',
                    type: implode('|', $expect),
                )
            );
        }
    }
}
