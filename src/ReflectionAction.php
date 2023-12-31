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
use ReflectionMethod;
use ReflectionNamedType;
use TypeError;
use function Chevere\Message\message;
use function Chevere\Parameter\reflectionToParameters;
use function Chevere\Parameter\reflectionToReturn;

final class ReflectionAction implements ReflectionActionInterface
{
    private ReflectionMethod $method;

    private ParametersInterface $parameters;

    private ParameterInterface $return;

    public function __construct(
        private string $action
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
         */
        if (! method_exists($action, $action::mainMethod())) {
            throw new LogicException(
                (string) message(
                    "Action doesn't define a `%main%` method",
                    main: $action::mainMethod(),
                )
            );
        }
        $this->method = new ReflectionMethod($action, $action::mainMethod());
        if ($this->method->isPrivate()) {
            throw new LogicException(
                (string) message(
                    "Action `%main%` method can't be private",
                    main: $action::mainMethod(),
                )
            );
        }
        $this->parameters = reflectionToParameters($this->method);
        $attributes = $this->method->getAttributes(ReturnAttr::class);
        $this->return = match (true) {
            $attributes === [] => $action::return(),
            default => reflectionToReturn($this->method),
        };
        if (! $this->method->hasReturnType()) {
            if ($this->return->type()->typeHinting() === 'null') {
                return;
            }

            throw new TypeError(
                (string) message(
                    'Action `%method%` method must declare `%type%` return type',
                    method: $action::mainMethod(),
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
        if (in_array('array', $expect, true)) {
            $expect[] = 'ArrayAccess';
        }
        if (in_array('iterable', $expect, true)) {
            $expect[] = 'array';
            $expect[] = 'Traversable';
        }
        /** @var ReflectionNamedType $type */
        $type = $this->method->getReturnType();
        $typeName = $type->getName();
        $return = match ($typeName) {
            'void' => 'null',
            default => $typeName,
        };
        if (! in_array($return, $expect, true)) {
            throw new TypeError(
                (string) message(
                    'Action `%main%` method must declare `%type%` return type',
                    main: $this->action::mainMethod(),
                    type: implode('|', $expect),
                )
            );
        }
    }
}
