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

use Chevere\Tests\src\ActionTestVariadic;
use PHPUnit\Framework\TestCase;

final class ActionVariadicTest extends TestCase
{
    public function testNamed(): void
    {
        $result = (new ActionTestVariadic())
            (id: 1, foo: 'super', bar: 'taldo');
        $this->assertSame(
            [
                'id' => 1,
                'name' => [
                    'foo' => 'super',
                    'bar' => 'taldo',
                ],
            ],
            $result
        );
    }

    public function testNamedSome(): void
    {
        $result = (new ActionTestVariadic())
            (1, 'super', bar: 'taldo');
        $this->assertSame(
            [
                'id' => 1,
                'name' => [
                    0 => 'super',
                    'bar' => 'taldo',
                ],
            ],
            $result
        );
    }

    public function testPositional(): void
    {
        $result = (new ActionTestVariadic())
            (1);
        $this->assertSame(
            [
                'id' => 1,
                'name' => [],
            ],
            $result
        );
    }

    public function testPositionalSome(): void
    {
        $result = (new ActionTestVariadic())
            (1, 'super', 'taldo');
        $this->assertSame(
            [
                'id' => 1,
                'name' => [
                    'super',
                    'taldo',
                ],
            ],
            $result
        );
    }
}
