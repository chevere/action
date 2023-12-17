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

use Chevere\Parameter\Interfaces\ParametersInterface;
use ReflectionMethod;
use function Chevere\Parameter\reflectionToParameters;

function getParameters(string $action): ParametersInterface
{
    $reflection = new ReflectionMethod($action, Action::RUN_METHOD);

    return reflectionToParameters($reflection);
}
