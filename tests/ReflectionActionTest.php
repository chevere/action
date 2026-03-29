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
use Chevere\Tests\src\ActionTestMissingInvoke;
use Chevere\Tests\src\ActionTestNullReturnType;
use Chevere\Tests\src\ActionTestUnionReturnMismatch;
use Chevere\Tests\src\ActionTestUnionReturnType;
use LogicException;
use PHPUnit\Framework\TestCase;
use TypeError;

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

    public function testActionNoInvokeMethod(): void
    {
        $action = ActionTestMissingInvoke::class;
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage(
            "Action doesn't define a `__invoke` method"
        );
        new ReflectionAction($action);
    }

    public function testVoidReturnType(): void
    {
        $action = ActionTestNullReturnType::class;
        $reflection = new ReflectionAction($action);
        $this->assertSame('null', $reflection->return()->type()->typeHinting());
    }

    public function testUnionReturnTypeMismatch(): void
    {
        $action = ActionTestUnionReturnMismatch::class;
        $this->expectException(TypeError::class);
        $this->expectExceptionMessage(
            'Action `__invoke` method must declare `int` return type'
        );
        new ReflectionAction($action);
    }

    public function testUnionReturnTypeMatch(): void
    {
        $action = ActionTestUnionReturnType::class;
        $reflection = new ReflectionAction($action);
        $this->assertInstanceOf(ReflectionAction::class, $reflection);
    }

    /**
     * @dataProvider dataProviderViolations
     */
    public function testViolations(array $violations, string $action): void
    {
        $reflection = new ReflectionAction($action, failFast: false);
        $this->assertSame(
            $violations,
            $reflection->violations()->toArray()
        );
    }

    public static function dataProviderViolations(): array
    {
        $interface = ActionInterface::class;

        return [
            'action not exists' => [
                [
                    [
                        'concern' => 'class',
                        'message' => "Action doesn't exists",
                    ],
                ],
                'wea',
            ],
            'action not implements interface' => [
                [
                    [
                        'concern' => 'interface',
                        'message' => "Action doesn't implement `{$interface}`",
                    ],
                ],
                __CLASS__,
            ],
            'no invoke method' => [
                [
                    [
                        'concern' => '__invoke',
                        'message' => "Action doesn't define a `__invoke` method",
                    ],
                ],
                ActionTestMissingInvoke::class,
            ],
            'union return type mismatch' => [
                [
                    [
                        'concern' => '__invoke',
                        'message' => 'Action `__invoke` method must declare `int` return type',
                    ],
                ],
                ActionTestUnionReturnMismatch::class,
            ],
        ];
    }
}
