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
use Chevere\Parameter\Attributes\BoolAttr;
use Chevere\Parameter\Attributes\ReturnAttr;
use Chevere\Parameter\Attributes\StringAttr;
use Chevere\Parameter\Interfaces\ParameterInterface;
use function Chevere\Parameter\int;

/**
 * Method `acceptReturn()` has higher priority than ReturnAttr, which is ignored.
 */
final class ActionTestAttributes extends Action
{
    #[ReturnAttr(
        new BoolAttr()
    )]
    public function __invoke(
        #[StringAttr('/^ab$/')]
        string $value
    ): int {
        $this->assertArguments($value);

        return $this->assertReturn(1);
    }

    public static function acceptReturn(): ParameterInterface
    {
        return int();
    }
}
