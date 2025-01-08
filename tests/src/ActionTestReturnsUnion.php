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

namespace Chevere\Demo\Actions;

use Chevere\Action\Action;
use Chevere\Parameter\Interfaces\UnionParameterInterface;
use function Chevere\Parameter\object;
use function Chevere\Parameter\union;

class ActionTestReturnsUnion extends Action
{
    public static function return(): UnionParameterInterface
    {
        return union(
            object(MyClass1::class),
            object(MyClass2::class)
        );
    }

    protected function main(): MyClass1|MyClass2
    {
        return new MyClass1();
    }
}

class MyClass1
{
}

class MyClass2
{
}
