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

use Chevere\Action\ActionName;
use Chevere\Action\Interfaces\ActionInterface;
use Chevere\Tests\src\ControllerNameTestController;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class ActionNameTest extends TestCase
{
    public function testInterface(): void
    {
        $interface = ActionName::interface();
        $this->assertSame(ActionInterface::class, $interface);
    }

    public function testWrongInterface(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            <<<PLAIN
            Action `Chevere\Tests\ActionNameTest` doesn't implement `Chevere\Action\Interfaces\ActionInterface`
            PLAIN
        );
        new ActionName(self::class);
    }

    public function testConstruct(): void
    {
        $className = ControllerNameTestController::class;
        $actionName = new ActionName($className);
        $this->assertSame($className, $actionName->__toString());
        $this->assertSame('Action', $actionName->symbol());
    }
}
