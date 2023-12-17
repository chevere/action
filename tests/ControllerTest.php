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

use Chevere\Tests\src\ControllerTestController;
use Chevere\Tests\src\ControllerTestInvalidController;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class ControllerTest extends TestCase
{
    public function testInvalid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        (new ControllerTestInvalidController())->__invoke();
    }

    public function testConstruct(): void
    {
        $this->expectNotToPerformAssertions();
        (new ControllerTestController())->__invoke(string: '');
    }
}
