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

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/MyAction.php';

$arguments = [
    'ok',
    'ok muy bueno',
    'ko',
    'ok malo pero por muy largo',
];
echo <<<PLAIN
Will return mb_strlen(\$argument) * 5
--

PLAIN;
$action = new MyAction();
foreach ($arguments as $argument) {
    try {
        $return = $action->__invoke($argument);
        echo <<<PLAIN
        [OK] argument: {$argument}
               return: {$return}

        PLAIN;
    } catch (Throwable $e) {
        echo <<<PLAIN
        [ER] argument: {{$argument}}
             error: {$e->getMessage()}

        PLAIN;
    }
}
