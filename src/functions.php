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
use Chevere\Parameter\Attributes\ReturnAttr;
use Chevere\Parameter\Interfaces\ParameterInterface;
use Chevere\Parameter\Interfaces\ParametersInterface;
use ReflectionMethod;
use function Chevere\Parameter\reflectionToReturn;

/**
 * @param class-string<ActionInterface> $action
 */
function getParameters(string $action): ParametersInterface
{
    return (new ReflectionAction($action))->parameters();
}

/**
 * @param class-string<ActionInterface> $action
 */
function getReturnParameter(string $action): ParameterInterface
{
    $return = $action::return();
    $reflection = new ReflectionMethod($action, 'main');
    $attributes = $reflection->getAttributes(ReturnAttr::class);
    if ($attributes !== []) {
        return reflectionToReturn($reflection);
    }

    return $return;
}
