# Action

![Chevere](chevere.svg)

[![Build](https://img.shields.io/github/actions/workflow/status/chevere/action/test.yml?branch=2.0&style=flat-square)](https://github.com/chevere/action/actions)
![Code size](https://img.shields.io/github/languages/code-size/chevere/action?style=flat-square)
[![Apache-2.0](https://img.shields.io/github/license/chevere/action?style=flat-square)](LICENSE)
[![PHPStan](https://img.shields.io/badge/PHPStan-level%209-blueviolet?style=flat-square)](https://phpstan.org/)
[![Mutation testing badge](https://img.shields.io/endpoint?style=flat-square&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2Fchevere%2Faction%2F2.0)](https://dashboard.stryker-mutator.io/reports/github.com/chevere/action/2.0)

[![Quality Gate Status](https://sonarcloud.io/api/project_badges/measure?project=chevere_action&metric=alert_status)](https://sonarcloud.io/dashboard?id=chevere_action)
[![Maintainability Rating](https://sonarcloud.io/api/project_badges/measure?project=chevere_action&metric=sqale_rating)](https://sonarcloud.io/dashboard?id=chevere_action)
[![Reliability Rating](https://sonarcloud.io/api/project_badges/measure?project=chevere_action&metric=reliability_rating)](https://sonarcloud.io/dashboard?id=chevere_action)
[![Security Rating](https://sonarcloud.io/api/project_badges/measure?project=chevere_action&metric=security_rating)](https://sonarcloud.io/dashboard?id=chevere_action)
[![Coverage](https://sonarcloud.io/api/project_badges/measure?project=chevere_action&metric=coverage)](https://sonarcloud.io/dashboard?id=chevere_action)
[![Technical Debt](https://sonarcloud.io/api/project_badges/measure?project=chevere_action&metric=sqale_index)](https://sonarcloud.io/dashboard?id=chevere_action)
[![CodeFactor](https://www.codefactor.io/repository/github/chevere/action/badge)](https://www.codefactor.io/repository/github/chevere/action)

## Summary

Action implements the Action pattern relying on the  [Parameter](https://github.com/chevere/parameter) library for creating a structured way to define and validate logic.

## Installing

Action is available through [Packagist](https://packagist.org/packages/chevere/action) and the repository source is at [chevere/action](https://github.com/chevere/action).

```sh
composer require chevere/action
```

## Quick start

Implement [ActionInterface](src/interfaces/ActionInterface.php) by using the [Action trait](#use-actiontrait) or by extending [Action abstract](#extend-action).

## Creating actions

### Use ActionTrait

Create an action by using [ActionTrait](src/Traits/ActionTrait.php).

```php
use Chevere\Action\Interfaces\ActionInterface;
use Chevere\Action\Traits\ActionTrait;

class MyAction implements ActionInterface
{
    use ActionTrait;
    // ...
}
```

### Extend Action

Create an Action by extending [Action](src/Action.php).

```php
use Chevere\Action\Action;

class MyAction extends Action
{
    // ...
}
```

### Invoke method

Use the `__invoke()` method to determine action main logic. Use **attributes** from [chevere/parameter](https://github.com/chevere/parameter) on arguments and method return to add validation rules.

* Before validation rules:

```php
class MyAction
{
    public function __invoke(
        string $value
    ): int
    {
        return mb_strlen($value) * 5;
    }
}
```

* After validation rules:

```php
use Chevere\Action\Action;
use Chevere\Parameter\Attributes\IntAttr;
use Chevere\Parameter\Attributes\ReturnAttr;
use Chevere\Parameter\Attributes\StringAttr;

use function Chevere\Parameter\valid;
use function Chevere\Parameter\returnAttr;

class MyAction extends Action
{
    #[ReturnAttr(
        new IntAttr(min: 0, max: 100)
    )]
    public function __invoke(
        #[StringAttr('/^ok/')]
        string $value
    ): int {
        $this->assertArguments($value);
        return $this->assertReturn(
            mb_strlen($value) * 5
        );
    }
}
```

## Using actions

Invoke action's main logic passing the arguments you would pass to `__invoke`. Action internal runtime will validate arguments and return against all defined rules.

💡 You can try by running `php demo/demo.php`

```php
$action = new MyAction();
$result = $action('ok muy bueno');
```

## Advanced use

### Return method

For validating `return` beyond the limitations of PHP's attributes you can define Action's `return()` method. In this context you can use and remix any [Parameter function](https://github.com/chevere/parameter#function-reference).

```php
use Chevere\Action\Interfaces\ParameterInterface;
use function Chevere\Parameter\string;

public static function return(): ParameterInterface
{
    return string();
}
```

You can also forward parameter resolution to a callable by using `CallableAttr`:

```php
use Chevere\Action\Attributes\CallableAttr;
use Chevere\Action\Attributes\ReturnAttr;
use function Chevere\Parameter\string;

function myCallable(): StringParameterInterface
{
    return string();
}

#[ReturnAttr(
    new CallableAttr('myCallable')
)]
public function __invoke(): string
{
    return $this->assertReturn('chevere');
}
```

## Controller

The Controller component is a special type of Action in charge of handling incoming instructions. Its `__invoke` method only takes parameters of type `string`.

### Defining a Controller

A Controller implements the `ControllerInterface`. You can extend `Controller` to quick create a compliant Controller:

```php
use Chevere\Controller\Controller;

class SomeController extends Controller
{
    // ...
}
```

### Invoke parameters

Parameters are defined in the `__invoke` method but it just takes strings.

```php
public function __invoke(
    string $pepito,
    string $paysTwice
): array
{
    // ...
}
```

## Parameter Attributes

Use `StringAttr` to validate a string:

```php
use Chevere\Attributes\StringAttr;

public function __invoke(
    #[StringAttr('/^[a-z]$/')]
    string $pepito,
    #[StringAttr('/^[a-zA-Z]+$/')]
    string $paysTwice
): array
{
    // ...
}
```

## Documentation

Documentation is available at [chevere.org](https://chevere.org/packages/action).

## License

Copyright [Rodolfo Berrios A.](https://rodolfoberrios.com/)

Chevere is licensed under the Apache License, Version 2.0. See [LICENSE](LICENSE) for the full license text.

Unless required by applicable law or agreed to in writing, software distributed under the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the License for the specific language governing permissions and limitations under the License.
