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
use Chevere\Action\Interfaces\ControllerNameInterface;
use Chevere\Action\Traits\ActionNameTrait;

final class ControllerName implements ControllerNameInterface
{
    use ActionNameTrait;

    public static function symbol(): string
    {
        return 'Controller';
    }

    public static function interface(): string
    {
        return ControllerInterface::class;
    }
}
