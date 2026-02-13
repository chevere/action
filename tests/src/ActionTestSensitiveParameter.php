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
use SensitiveParameter;

final class ActionTestSensitiveParameter extends Action
{
    public function __invoke(
        #[SensitiveParameter()]
        #[_enum('super', 'taldo')]
        string $sensitive,
        #[SensitiveParameter()]
        #[_int(min: 1)]
        int $secret
    ): void {
        $this->assertArguments();
    }
}
