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

use Chevere\Action\Exceptions\ActionException;
use Chevere\Tests\src\ControllerNameTestController;
use Chevere\Tests\src\ControllerTestController;
use Chevere\Tests\src\ControllerTestInvalidController;
use PHPUnit\Framework\TestCase;

final class ControllerTest extends TestCase
{
    public function testInvalid(): void
    {
        $this->expectException(ActionException::class);
        $this->expectExceptionMessage(
            <<<PLAIN
            `Chevere\Tests\src\ControllerTestInvalidController` InvalidArgumentException → Parameter `mixed, int, var` must be of type **string** for controller `Chevere\Tests\src\ControllerTestInvalidController`
            PLAIN
        );
        $controller = new ControllerTestInvalidController();
        $controller->__invoke();
    }

    public function testConstruct(): void
    {
        $this->expectNotToPerformAssertions();
        (new ControllerTestController())->__invoke('');
        (new ControllerNameTestController())->__invoke();
    }
}
