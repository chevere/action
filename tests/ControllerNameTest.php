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

use Chevere\Action\ControllerName;
use Chevere\Tests\src\ControllerNameTestController;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class ControllerNameTest extends TestCase
{
    public function testWrongInterface(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new ControllerName(self::class);
    }

    public function testConstruct(): void
    {
        $className = ControllerNameTestController::class;
        $controllerName = new ControllerName($className);
        $this->assertSame($className, $controllerName->__toString());
    }
}
