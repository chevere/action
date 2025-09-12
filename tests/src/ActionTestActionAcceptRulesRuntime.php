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

final class ActionTestActionAcceptRulesRuntime extends Action implements Countable
{
    private int $counter;

    public function __invoke(int $counter): void
    {
        $this->counter = $counter;
    }

    public function count(): int
    {
        return $this->counter;
    }

    public function acceptRulesRuntime(): void
    {
        $this->counter++;
        if ($this->counter < 0) {
            throw new LogicException('Counter must be positive');
        }
    }
}
