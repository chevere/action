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
use Chevere\Parameter\Attributes\_bool;
use Chevere\Parameter\Attributes\_return;
use Chevere\Parameter\Attributes\_string;
use Chevere\Parameter\Interfaces\ParameterInterface;
use function Chevere\Parameter\int;

/**
 * Method `acceptReturn()` has higher priority than _return, which is ignored.
 */
final class ActionTestAttributes extends Action
{
    #[_return(
        new _bool()
    )]
    public function __invoke(
        #[_string('/^ab$/')]
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
