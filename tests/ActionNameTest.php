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
use Chevere\Tests\src\ActionNameTestAction;
use Chevere\Tests\src\ActionNameTestActionSetUp;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class ActionNameTest extends TestCase
{
    public function testInterface(): void
    {
        $interface = ActionName::interface();
        $this->assertSame(ActionInterface::class, $interface);
        $this->assertSame('Action', ActionName::symbol());
    }

    public function testWrongInterface(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            <<<PLAIN
            Action `` doesn't implement `Chevere\Action\Interfaces\ActionInterface`
            PLAIN
        );
        new ActionName('');
    }

    public function testConstruct(): void
    {
        $className = ActionNameTestAction::class;
        $actionName = new ActionName($className);
        $this->assertSame($className, $actionName->__toString());
        $this->assertSame([], $actionName->arguments());
    }

    public function testConstructArgumentsNoSetup(): void
    {
        $className = ActionNameTestAction::class;
        $arguments = ['arg1', 'arg2'];
        $actionName = new ActionName($className, ...$arguments);
        $this->assertSame([], $actionName->arguments());
    }

    /**
     * @dataProvider provideConstructArguments
     */
    public function testConstructArgumentsSetup(
        string $action,
        array $arguments,
        array $expectedArguments
    ): void {
        $actionName = new ActionName($action, ...$arguments);
        $this->assertSame($expectedArguments, $actionName->arguments());
    }

    public static function provideConstructArguments(): array
    {
        return [
            [
                ActionNameTestActionSetUp::class,
                [
                    'foo',
                    123,
                ],
                [
                    'foo',
                    123,
                ],
            ],
            [
                ActionNameTestActionSetUp::class,
                [
                    'test' => 'foo',
                    'code' => 123,
                ],
                [
                    'test' => 'foo',
                    'code' => 123,
                ],
            ],
        ];
    }
}
