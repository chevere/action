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

use Chevere\Action\Interfaces\ReflectionActionInterface;
use Chevere\Action\Traits\ReflectionActionTrait;
use Chevere\Parameter\Interfaces\ParameterInterface;
use Chevere\Parameter\Interfaces\ParametersInterface;
use ReflectionFunction;
use ReflectionMethod;
use function Chevere\Parameter\reflectionToParameters;
use function Chevere\Parameter\reflectionToReturn;

final class ReflectionAction implements ReflectionActionInterface
{
    use ReflectionActionTrait;

    public function reflectionToParameters(ReflectionFunction|ReflectionMethod $reflection): ParametersInterface
    {
        return reflectionToParameters($reflection);
    }

    public function reflectionToReturn(ReflectionFunction|ReflectionMethod $reflection): ParameterInterface
    {
        return reflectionToReturn($reflection);
    }
}
