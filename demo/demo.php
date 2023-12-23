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
$action = new MyAction();
foreach ($arguments as $argument) {
    try {
        $return = $action($argument);
        $return = $return->int();
        echo <<<PLAIN
        [OK] argument:{$argument} \n     return:{$return}

        PLAIN;
    } catch (Throwable $e) {
        echo <<<PLAIN
        [ER] argument:{$argument} \n     error:{$e->getMessage()}

        PLAIN;
    }
}
