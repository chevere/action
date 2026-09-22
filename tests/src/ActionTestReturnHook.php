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
use Chevere\Parameter\Interfaces\ArrayParameterInterface;
use function Chevere\Parameter\arrayp;
use function Chevere\Parameter\string;

final class ActionTestReturnHook extends Action
{
    public function __construct(
        private bool $useHook
    ) {
    }

    public function __invoke(
    ): array {
        return $this->assertReturn(
            [
                'foo' => 'test',
                'bar' => 'test',
            ]
        );
    }

    public function acceptReturnHook(ArrayParameterInterface $return): ArrayParameterInterface
    {
        if (! $this->useHook) {
            return $return->without('bar');
        }

        return $return->withMakeRequired('bar');
    }

    public static function acceptReturn(): ArrayParameterInterface
    {
        return arrayp(
            foo: string()
        )->withOptional(
            bar: string()
        );
    }
}
