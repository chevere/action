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

namespace Chevere\Tests\src;

use Chevere\Action\Action;

final class ActionTestOverride extends Action
{
    public static bool $assertTypes = false;

    public static bool $assertMethod = false;

    protected function main(): array
    {
        return [];
    }
}
