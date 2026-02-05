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
use Chevere\Parameter\Interfaces\ParametersInterface;
use function Chevere\Parameter\parameters;
use function Chevere\Parameter\string;

/**
 * Method `acceptParameters` has higher priority than `__invoke()` parameter attributes.
 */
final class ActionTestAcceptParameters extends Action
{
    #[ReturnAttr(
        new IntAttr(min: 1)
    )
    ]
    public function __invoke(
        #[StringAttr('/^ko$/')]
        string $foo
    ): int {
        $this->assertArguments($foo);

        return $this->assertReturn(1);
    }

    /**
     * This rule has higher priority than the one defined in `__invoke()` parameter attributes.
     */
    public static function acceptParameters(): ParametersInterface
    {
        return parameters(
            foo: string('/^ok$/')
        );
    }
}
