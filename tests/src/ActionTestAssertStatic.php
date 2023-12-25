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
use Chevere\Action\Interfaces\ReflectionActionInterface;

final class ActionTestAssertStatic extends Action
{
    protected static bool $isAsserted = false;

    public function __construct()
    {
        self::$isAsserted = false;
    }

    public function main(): void
    {
    }

    public static function isAsserted(): bool
    {
        return self::$isAsserted;
    }

    protected static function assertStatic(ReflectionActionInterface $reflection): void
    {
        self::$isAsserted = true;
    }
}
