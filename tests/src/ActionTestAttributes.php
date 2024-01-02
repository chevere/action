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
use Chevere\Parameter\Attributes\IntAttr;
use Chevere\Parameter\Attributes\ReturnAttr;
use Chevere\Parameter\Attributes\StringAttr;
use Chevere\Parameter\Interfaces\ParameterInterface;
use function Chevere\Parameter\string;

final class ActionTestAttributes extends Action
{
    public static function return(): ParameterInterface
    {
        return string();
    }

    #[ReturnAttr(new IntAttr(min: 1))]
    protected function main(
        #[StringAttr('/^ab$/')]
        string $value
    ): int {
        return 1;
    }
}
