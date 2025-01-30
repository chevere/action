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
use SensitiveParameter;

final class ActionTestSensitiveParameter extends Action
{
    protected function main(
        #[SensitiveParameter()]
        #[EnumAttr('super', 'taldo')]
        string $sensitive,
        #[SensitiveParameter()]
        #[IntAttr(min: 1)]
        int $secret
    ): void {
    }
}
