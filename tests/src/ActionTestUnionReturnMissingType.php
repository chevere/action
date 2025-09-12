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
use function Chevere\Parameter\string;
use function Chevere\Parameter\union;

final class ActionTestUnionReturnMissingType extends Action
{
    public function __invoke(): float
    {
        return $this->assertReturn(3.1);
    }

    public static function acceptReturn(): ParameterInterface
    {
        return union(
            string(),
            int(),
        );
    }
}
