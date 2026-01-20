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
use Chevere\Tests\src\ControllerTestSupportedTypes;
use Chevere\Tests\src\ControllerTestUnsupportedTypes;
use PHPUnit\Framework\TestCase;
use stdClass;

final class ControllerTest extends TestCase
{
    public function testValidArguments(): void
    {
        $this->expectNotToPerformAssertions();
        $controller = new ControllerTestSupportedTypes();
        $controller->assertArguments(
            int: 1,
            var: 'var',
            float: 1.0
        );
    }

    public function testInvalidArguments(): void
    {
        $this->expectException(ActionException::class);
        $this->expectExceptionMessage(
            <<<PLAIN
            InvalidArgumentException → [int]: Argument must be of type int, true given
            [var]: Argument provided doesn't match union: Parameter `0` <Chevere\Parameter\StringParameter>: Argument must be of type Stringable|string, float given; Parameter `1` <Chevere\Parameter\IntParameter>: Argument must be of type int, float given
            [float]: Argument must be of type float, string given
            PLAIN
        );
        $controller = new ControllerTestSupportedTypes();
        $controller->assertArguments(
            int: true,
            var: 1.5,
            float: '1'
        );
    }

    public function testInvalidParameters(): void
    {
        $this->expectException(ActionException::class);
        $this->expectExceptionMessage(
            <<<PLAIN
            InvalidArgumentException → Parameter(s) `noType`, `stdClass`, `bool` must be compatible with type **string|int|float** at `Chevere\Tests\src\ControllerTestUnsupportedTypes->__invoke()` method
            PLAIN
        );
        $controller = new ControllerTestUnsupportedTypes();
        $controller->assertArguments(
            noType: 'any',
            stdClass: new stdClass(),
            bool: true
        );
    }
}
