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
use Chevere\Action\Interfaces\ActionInterface;
use Chevere\Parameter\Interfaces\StringParameterInterface;
use Chevere\Tests\src\ActionTestAssertArgumentsDefinedVars;
use Chevere\Tests\src\ActionTestAssertArgumentsExplicit;
use Chevere\Tests\src\ActionTestAssertArgumentsImplicit;
use Chevere\Tests\src\ActionTestAssertRuntimeAction;
use Chevere\Tests\src\ActionTestAssertStatic;
use Chevere\Tests\src\ActionTestAttributes;
use Chevere\Tests\src\ActionTestController;
use Chevere\Tests\src\ActionTestIterableResponse;
use Chevere\Tests\src\ActionTestIterableReturnError;
use Chevere\Tests\src\ActionTestMethodParameterMissingType;
use Chevere\Tests\src\ActionTestNoReturnTypeError;
use Chevere\Tests\src\ActionTestNullParameterNoReturn;
use Chevere\Tests\src\ActionTestNullReturnType;
use Chevere\Tests\src\ActionTestReturnExtraArguments;
use Chevere\Tests\src\ActionTestSensitiveParameter;
use Chevere\Tests\src\ActionTestUnionReturnMissingType;
use Chevere\Tests\src\ActionTestUnionReturnType;
use Closure;
use PHPUnit\Framework\TestCase;

final class ActionTest extends TestCase
{
    public static function getClosuresAssertProvider(ActionInterface $action): array
    {
        return [
            [
                function () use ($action) {
                    $action->__invoke('error', -111);
                },
            ],
            [
                function () use ($action) {
                    $closure = function (string $foo, int $bar) use ($action) {
                        $action->assertArguments($foo, $bar);
                    };
                    $closure('error', -111);
                },
            ],
        ];
    }

    public function testParameters(): void
    {
        $parameters = ActionTestController::parameters();
        $parameter = $parameters->get('name');
        $this->assertInstanceOf(StringParameterInterface::class, $parameter);
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
        $this->expectNotToPerformAssertions();
        $action = new ActionTestReturnExtraArguments();
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
        $action = new ActionTestIterableReturnError();
        $this->expectException(ActionException::class);
        $this->expectExceptionMessage(
            <<<PLAIN
            `Chevere\Tests\src\ActionTestIterableReturnError` InvalidArgumentException → [V *iterable]: Argument must be of type int, string given
            PLAIN
        );
        $action->__invoke();
    }

    public function testUnionResponse(): void
    {
        $action = new ActionTestUnionReturnType('test');
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
            `Chevere\Tests\src\ActionTestUnionReturnMissingType` TypeError → Action `__invoke` method must declare `string|int` return type
            PLAIN
        );
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
            `Chevere\Tests\src\ActionTestNoReturnTypeError` TypeError → Action `__invoke` method must declare `array` return type
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

    public function testAttributeParameters(): void
    {
        $parameters = ActionTestAttributes::parameters();
        $parameter = $parameters->required('value')->string();
        $this->assertSame(
            '/^ab$/',
            $parameter->regex()->__toString()
        );
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
        $action->assert();
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

    public function testReturnsUnion(): void
    {
        $this->expectNotToPerformAssertions();
        $action = new ActionTestUnionReturnType(123);
        $action->__invoke();
    }

    public function testSensitiveParameter(): void
    {
        $action = new ActionTestSensitiveParameter();
        $this->expectException(ActionException::class);
        $this->expectExceptionMessage(
            <<<PLAIN
            [sensitive]: Argument value provided doesn't match the regex `#^super|taldo$#`
            [secret]: Argument value provided is less than `1`
            PLAIN
        );
        $action->__invoke('sensitive', -333);
    }

    /**
     * @dataProvider assertArgumentsExplicitProvider
     */
    public function testAssertArgumentsExplicit(Closure $closure): void
    {
        $this->expectException(ActionException::class);
        $this->expectExceptionMessage(
            <<<PLAIN
            [foo]: Argument value provided `error` doesn't match the regex `#^super|taldo$#`
            [bar]: Argument value provided `-111` is less than `1`
            PLAIN
        );
        $closure();
    }

    public static function assertArgumentsExplicitProvider(): array
    {
        return static::getClosuresAssertProvider(
            new ActionTestAssertArgumentsExplicit()
        );
    }

    /**
     * @dataProvider assertArgumentsImplicitProvider
     */
    public function testAssertArgumentsImplicit(Closure $closure): void
    {
        $this->expectException(ActionException::class);
        $this->expectExceptionMessage(
            <<<PLAIN
            [foo]: Argument value provided `error` doesn't match the regex `#^super|taldo$#`
            [bar]: Argument value provided `-111` is less than `1`
            PLAIN
        );
        $closure();
    }

    public static function assertArgumentsImplicitProvider(): array
    {
        return static::getClosuresAssertProvider(
            new ActionTestAssertArgumentsImplicit()
        );
    }

    /**
     * @dataProvider assertArgumentsDefinedVarsProvider
     */
    public function testAssertArgumentsDefinedVars(Closure $closure): void
    {
        $this->expectException(ActionException::class);
        $this->expectExceptionMessage(
            <<<PLAIN
            [foo]: Argument value provided `error` doesn't match the regex `#^super|taldo$#`
            [bar]: Argument value provided `-111` is less than `1`
            PLAIN
        );
        $closure();
    }

    public static function assertArgumentsDefinedVarsProvider(): array
    {
        return static::getClosuresAssertProvider(
            new ActionTestAssertArgumentsDefinedVars()
        );
    }

    public function testReflectionCache(): void
    {
        $reflection1 = ActionTestAssertStatic::reflection();
        $reflection2 = ActionTestAssertStatic::reflection();
        $this->assertSame($reflection1, $reflection2);
    }
}
