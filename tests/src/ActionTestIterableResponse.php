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

namespace Chevere\Tests\src;

use Chevere\Action\Action;
use Chevere\Parameter\Interfaces\ParameterInterface;
use function Chevere\Parameter\int;
use function Chevere\Parameter\iterable;
use function Chevere\Parameter\string;

final class ActionTestIterableResponse extends Action
{
    public static function return(): ParameterInterface
    {
        return iterable(
            V: int(),
            K: string()
        );
    }

    protected function main(): iterable
    {
        return [
            'id' => 123,
            'id' => 124,
            'id' => 125,
            // ...
        ];
    }
}
