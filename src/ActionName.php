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
use Chevere\Action\Interfaces\ActionNameInterface;
use Chevere\Action\Traits\ActionNameTrait;

final class ActionName implements ActionNameInterface
{
    use ActionNameTrait;

    public static function interface(): string
    {
        return ActionInterface::class;
    }
}
