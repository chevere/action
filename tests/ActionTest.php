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
use Chevere\Tests\src\ActionTestAction;
use Chevere\Tests\src\ActionTestArrayAccessReturnType;
use Chevere\Tests\src\ActionTestAssertRuntimeAction;
use Chevere\Tests\src\ActionTestAssertStatic;
use Chevere\Tests\src\ActionTestAttributes;
use Chevere\Tests\src\ActionTestController;
use Chevere\Tests\src\ActionTestIterableResponse;
use Chevere\Tests\src\ActionTestIterableResponseError;
use Chevere\Tests\src\ActionTestMethodParameterMissingType;
use Chevere\Tests\src\ActionTestMissingRun;
use Chevere\Tests\src\ActionTestNoReturnTypeError;
use Chevere\Tests\src\ActionTestNullParameterNoReturn;
use Chevere\Tests\src\ActionTestNullReturnType;
use Chevere\Tests\src\ActionTestPrivateScope;
use Chevere\Tests\src\ActionTestReturnExtraArguments;
use Chevere\Tests\src\ActionTestUnionReturnMissingType;
use Chevere\Tests\src\ActionTestUnionReturnType;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

final class ActionTest extends TestCase
{
    public function testMissingMainMethod(): void
    {
        $action = new ActionTestMissingRun();
        $this->expectException(ActionException::class);
        $this->expectExceptionMessage(
            <<<PLAIN
            `Chevere\Tests\src\ActionTestMissingRun` LogicException → Action doesn't define a `main` method
            PLAIN
        );
        $action->__invoke();
    }

    public function testWithArguments(): void
    {
        $expected = 'PeoplesHernandez';
        $action = new ActionTestController();
        $string = $action->__invoke(name: $expected);
        $this->assertSame($expected, $string);
    }

    public function testNoTypeParameter(): void
    {
        $action = new ActionTestMethodParameterMissingType();
        $this->expectNotToPerformAssertions();
        $action->__invoke('mixed');
    }

    public function testReturnExtraArguments(): void
    {
        $action = new ActionTestReturnExtraArguments();
        $this->expectException(ActionException::class);
        $this->expectExceptionMessage(
            <<<PLAIN
            `Chevere\Tests\src\ActionTestReturnExtraArguments` ArgumentCountError → Invalid argument(s) provided: `id, extra`
            PLAIN
        );
        $action->__invoke();
    }

    public function testIterableResponse(): void
    {
        $action = new ActionTestIterableResponse();
        $this->expectNotToPerformAssertions();
        $action->__invoke();
    }

    public function testIterableResponseError(): void
    {
        $action = new ActionTestIterableResponseError();
        $this->expectException(ActionException::class);
        $this->expectExceptionMessage(
            <<<PLAIN
            `Chevere\Tests\src\ActionTestIterableResponseError` InvalidArgumentException → [V *iterable]: Argument #1 (\$value) must be of type int, string given
            PLAIN
        );
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
        $this->expectException(ActionException::class);
        $this->expectExceptionMessage(
            <<<PLAIN
            `Chevere\Tests\src\ActionTestUnionReturnMissingType` TypeError → Action `main` method must declare `string|int` return type
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
        $this->expectException(ActionException::class);
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
        $this->expectException(ActionException::class);
        $this->expectExceptionMessage(
            <<<PLAIN
            `Chevere\Tests\src\ActionTestNoReturnTypeError` TypeError → Action `main` method must declare `array` return type
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
        $this->assertSame(1, $action->__invoke(value: 'ab'));
        $this->expectException(ActionException::class);
        $this->expectExceptionMessage(
            <<<PLAIN
            `Chevere\Tests\src\ActionTestAttributes` InvalidArgumentException → [value]: Argument value provided `ac` doesn't match the regex `/^ab$/`
            PLAIN
        );
        $action->__invoke(value: 'ac');
    }

    public function testAssert(): void
    {
        $action = new ActionTestAssertStatic();
        $this->assertFalse($action::isAsserted());
        $action::assert();
        $this->assertTrue($action::isAsserted());
    }

    public function testAssertInvoke(): void
    {
        $action = new ActionTestAssertStatic();
        $this->assertFalse($action::isAsserted());
        $action->__invoke();
        $this->assertTrue($action::isAsserted());
    }

    public function testAssertRuntime(): void
    {
        $action = new ActionTestAssertRuntimeAction();
        $this->assertFalse($action->flag());
        $action->__invoke();
        $this->assertTrue($action->flag());
    }
}
