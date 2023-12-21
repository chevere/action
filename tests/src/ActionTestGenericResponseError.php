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
use function Chevere\Parameter\generic;
use function Chevere\Parameter\int;
use function Chevere\Parameter\string;

final class ActionTestGenericResponseError extends Action
{
    public static function return(): ParameterInterface
    {
        return generic(
            V: int(),
            K: string()
        );
    }

    protected function main(): array
    {
        return [
            'a' => 123,
            'b' => '124',
            'c' => 125,
            // ...
        ];
    }
}
