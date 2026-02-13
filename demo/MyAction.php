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
use Chevere\Parameter\Attributes\_int;
use Chevere\Parameter\Attributes\_return;
use Chevere\Parameter\Attributes\_string;

class MyAction implements ActionInterface
{
    use ActionTrait;

    #[_return(
        new _int(min: 0, max: 100)
    )]
    public function __invoke(
        #[_string('/^ok/')]
        string $value
    ): int {
        return mb_strlen($value) * 5;
    }
}
