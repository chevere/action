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

use Chevere\Action\Interfaces\ActionInterface;
use Chevere\Action\ReflectionAction;
use Chevere\Tests\src\ActionTestMissingRun;
use Chevere\Tests\src\ActionTestPrivateScope;
use LogicException;
use PHPUnit\Framework\TestCase;

final class ReflectionActionTest extends TestCase
{
    public function testActionNotExists(): void
    {
        $action = 'wea';
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage(
            "Action doesn't exists"
        );
        new ReflectionAction($action);
    }

    public function testActionNotImplements(): void
    {
        $action = __CLASS__;
        $interface = ActionInterface::class;
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage(
            "Action doesn't implement `{$interface}`"
        );
        new ReflectionAction($action);
    }

    public function testActionNoMainMethod(): void
    {
        $action = ActionTestMissingRun::class;
        $main = ActionTestMissingRun::mainMethod();
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage(
            "Action doesn't define a `{$main}` method"
        );
        new ReflectionAction($action);
    }

    public function testActionPrivateScope(): void
    {
        $action = ActionTestPrivateScope::class;
        $main = ActionTestPrivateScope::mainMethod();
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage(
            "Action `{$main}` method can't be private"
        );
        new ReflectionAction($action);
    }
}
