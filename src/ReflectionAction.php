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
use LogicException;
use ReflectionMethod;
use TypeError;
use function Chevere\Message\message;
use function Chevere\Parameter\reflectionToParameters;
use function Chevere\Parameter\reflectionToReturnParameter;

final class ReflectionAction implements ReflectionActionInterface
{
    private ReflectionMethod $method;

    private ParametersInterface $parameters;

    private ParameterInterface $return;

    public function __construct(string $action)
    {
        if (! class_exists($action)) {
            throw new LogicException(
                (string) message(
                    'Action does not exist',
                )
            );
        }
        $interfaces = class_implements($action) ?: [];
        if (! in_array(ActionInterface::class, $interfaces, true)) {
            throw new LogicException(
                (string) message(
                    'Action does not implement `%interface%`',
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
                    'Action does not define a `%main%` method',
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
            default => reflectionToReturnParameter($this->method),
        };
        if (! $this->method->hasReturnType()) {
            if ($this->return->type()->typeHinting() === 'null') {
                return;
            }

            throw new TypeError(
                (string) message(
                    'Method `%method%` must declare `%type%` return type',
                    method: $action::mainMethod(),
                    type: $this->return->type()->typeHinting(),
                )
            );
        }
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
}
