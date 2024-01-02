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

use ArrayAccess;
use ArrayObject;
use Chevere\Action\Action;
use Chevere\Parameter\Interfaces\ParameterInterface;
use function Chevere\Parameter\arrayp;
use function Chevere\Parameter\int;

final class ActionTestArrayAccessReturnType extends Action
{
    public static function return(): ParameterInterface
    {
        return arrayp(
            id: int(min: 1),
        );
    }

    protected function main(): ArrayAccess
    {
        return new ArrayObject([
            'id' => 1,
        ]);
    }
}
