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

namespace Chevere\Tests;

use Chevere\Tests\src\ActionTestAction;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use function Chevere\Action\getParameters;
use function Chevere\Parameter\reflectionToParameters;

final class FunctionsTest extends TestCase
{
    public function testGetParameters(): void
    {
        $action = ActionTestAction::class;
        $reflection = new ReflectionMethod($action, $action::mainMethod());
        $toParameters = reflectionToParameters($reflection);
        $parameters = getParameters($action);
        $this->assertEquals(
            $toParameters,
            $parameters
        );
    }
}
