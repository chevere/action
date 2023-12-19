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

use ArgumentCountError;
use Chevere\Tests\src\ActionTestAction;
use Chevere\Tests\src\ActionTestArrayAccessReturnType;
use Chevere\Tests\src\ActionTestAttributes;
use Chevere\Tests\src\ActionTestController;
use Chevere\Tests\src\ActionTestGenericResponse;
use Chevere\Tests\src\ActionTestGenericResponseError;
use Chevere\Tests\src\ActionTestMethodParameterMissingType;
use Chevere\Tests\src\ActionTestMissingRun;
use Chevere\Tests\src\ActionTestNoReturnTypeError;
use Chevere\Tests\src\ActionTestNullParameterNoReturn;
use Chevere\Tests\src\ActionTestNullReturnType;
use Chevere\Tests\src\ActionTestPrivateScope;
use Chevere\Tests\src\ActionTestReturnExtraArguments;
use Chevere\Tests\src\ActionTestUnionReturnMissingType;
use Chevere\Tests\src\ActionTestUnionReturnType;
use Error;
use InvalidArgumentException;
use LogicException;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use TypeError;

final class ActionTest extends TestCase
{
    public function testMissingRunMethod(): void
    {
        $action = new ActionTestMissingRun();
        $this->expectException(LogicException::class);
        $action->__invoke();
    }

    public function testWithArguments(): void
    {
        $expected = 'PeoplesHernandez';
        $action = new ActionTestController();
        $string = $action->__invoke(name: $expected)->string();
        $this->assertSame($expected, $string);
    }

    public function testInvalidRunParameter(): void
    {
        $action = new ActionTestMethodParameterMissingType();
        $this->expectException(TypeError::class);
        $this->expectExceptionMessage('$mixed');
        $action->__invoke();
    }

    public function testReturnExtraArguments(): void
    {
        $action = new ActionTestReturnExtraArguments();
        $this->expectException(ArgumentCountError::class);
        $action->__invoke();
    }

    public function testGenericResponse(): void
    {
        $action = new ActionTestGenericResponse();
        $this->expectNotToPerformAssertions();
        $action->__invoke();
    }

    public function testGenericResponseError(): void
    {
        $action = new ActionTestGenericResponseError();
        $this->expectException(InvalidArgumentException::class);
        $action->__invoke();
    }

    public function testUnionResponse(): void
    {
        $action = new ActionTestUnionReturnType();
        $this->expectNotToPerformAssertions();
        $action->__invoke();
    }

    public function testUnionResponseError(): void
    {
        $action = new ActionTestUnionReturnMissingType();
        $class = $action::class;
        $this->expectException(TypeError::class);
        $this->expectExceptionMessage(
            <<<PLAIN
            Method `{$class}::run` must declare `string|int` return type
            PLAIN
        );
        $action->__invoke();
    }

    public function testArrayAccessResponse(): void
    {
        $action = new ActionTestArrayAccessReturnType();
        $this->expectNotToPerformAssertions();
        $action->__invoke();
    }

    public function testPrivateScope(): void
    {
        $action = new ActionTestPrivateScope();
        $this->expectException(Error::class);
        $action->__invoke();
    }

    public function testNullReturnType(): void
    {
        $action = new ActionTestNullReturnType();
        $this->expectNotToPerformAssertions();
        $action->__invoke();
    }

    public function testNoReturnTypeError(): void
    {
        $action = new ActionTestNoReturnTypeError();
        $class = $action::class;
        $this->expectException(TypeError::class);
        $this->expectExceptionMessage(
            <<<PLAIN
            Method `{$class}::run` must declare `array` return type
            PLAIN
        );
        $action->__invoke();
    }

    public function testNullParameterNoReturn(): void
    {
        $action = new ActionTestNullParameterNoReturn();
        $this->expectNotToPerformAssertions();
        $action->__invoke();
    }

    public function testParametersNullAssign(): void
    {
        $action = new ActionTestAction();
        $reflection = new ReflectionProperty($action, 'parameters');
        $this->assertTrue($reflection->isInitialized($action));
        $this->assertNull($reflection->getValue($action));
        $action->__invoke();
        $reflection->getValue($action);
    }

    public function testAttributeValidation(): void
    {
        $action = new ActionTestAttributes();
        $this->assertSame(1, $action->__invoke(value: 'ab')->int());
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            <<<PLAIN
            `Chevere\Tests\src\ActionTestAttributes::run` → [value]: Argument value provided `ac` doesn't match the regex `/^ab$/`
            PLAIN
        );
        $action->__invoke(value: 'ac');
    }
}
