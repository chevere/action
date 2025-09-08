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
use Chevere\Parameter\Attributes\EnumAttr;
use Chevere\Parameter\Attributes\IntAttr;

final class ActionTestAssertArgumentsDefinedVars extends Action
{
    public function __invoke(
        #[EnumAttr('super', 'taldo')]
        string $foo,
        #[IntAttr(min: 1)]
        int $bar
    ): void {
        $this->assertArguments(...get_defined_vars());
    }
}
