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
use Countable;
use LogicException;

final class ActionTestActionAssertRulesStatic extends Action implements Countable
{
    private static int $counter;

    public function __invoke(int $counter): void
    {
        static::$counter = $counter;
    }

    public function count(): int
    {
        return static::$counter;
    }

    public function assertRulesRuntime(): void
    {
    }

    public static function assertRulesStatic(): void
    {
        static::$counter++;
        if (static::reflection()->parameters()->has('lucho')) {
            throw new LogicException('Parameter $lucho is forbidden');
        }
    }
}
