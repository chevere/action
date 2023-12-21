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

use Chevere\Action\Interfaces\ActionInterface;
use Chevere\Action\Traits\ActionTrait;
use Chevere\Parameter\Attributes\IntAttr;
use Chevere\Parameter\Attributes\ReturnAttr;
use Chevere\Parameter\Attributes\StringAttr;

class MyAction implements ActionInterface
{
    use ActionTrait;

    #[ReturnAttr(
        new IntAttr(min: 0, max: 100)
    )]
    protected function main(
        #[StringAttr('/^ok/')]
        string $value
    ): int {
        return 100;
    }
}
