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

namespace Chevere\Tests;

use Chevere\Tests\src\ActionTestAction;
use PHPUnit\Framework\TestCase;
use function Chevere\Action\getParameters;
use function Chevere\Action\getReturnParameter;

final class FunctionsTest extends TestCase
{
    public function testGetParameters(): void
    {
        $action = ActionTestAction::class;
        $this->assertEquals(
            $action::reflection()->parameters(),
            getParameters($action)
        );
    }

    public function testGetReturnParameter(): void
    {
        $action = ActionTestAction::class;
        $this->assertEquals(
            $action::reflection()->return(),
            getReturnParameter($action)
        );
    }
}
