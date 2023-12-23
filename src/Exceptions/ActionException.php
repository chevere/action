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

namespace Chevere\Action\Exceptions;

use Exception;

/**
 * Exception thrown at action __invoke on I/O.
 */
final class ActionException extends Exception
{
    public function __construct(string $message, string $file, int $line)
    {
        parent::__construct(message: $message);
        $this->file = $file;
        $this->line = $line;
    }
}
