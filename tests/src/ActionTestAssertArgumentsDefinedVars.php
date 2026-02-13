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
use Chevere\Parameter\Attributes\_enum;
use Chevere\Parameter\Attributes\_int;

final class ActionTestAssertArgumentsDefinedVars extends Action
{
    public function __invoke(
        #[_enum('super', 'taldo')]
        string $foo,
        #[_int(min: 1)]
        int $bar
    ): void {
        $this->assertArguments(...get_defined_vars());
    }
}
