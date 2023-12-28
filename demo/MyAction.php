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
use Chevere\Parameter\Attributes\RegexAttr;
use Chevere\Parameter\Attributes\ReturnAttr;

class MyAction implements ActionInterface
{
    use ActionTrait;

    #[ReturnAttr(
        new IntAttr(min: 0, max: 100)
    )]
    protected function main(
        #[RegexAttr('/^ok/')]
        string $value
    ): int {
        return mb_strlen($value) * 5;
    }
}
