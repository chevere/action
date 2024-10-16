# Action

![Chevere](chevere.svg)

[![Build](https://img.shields.io/github/actions/workflow/status/chevere/action/test.yml?branch=1.0&style=flat-square)](https://github.com/chevere/action/actions)
![Code size](https://img.shields.io/github/languages/code-size/chevere/action?style=flat-square)
[![Apache-2.0](https://img.shields.io/github/license/chevere/action?style=flat-square)](LICENSE)
[![PHPStan](https://img.shields.io/badge/PHPStan-level%209-blueviolet?style=flat-square)](https://phpstan.org/)
[![Mutation testing badge](https://img.shields.io/endpoint?style=flat-square&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2Fchevere%2Faction%2F1.0)](https://dashboard.stryker-mutator.io/reports/github.com/chevere/action/1.0)

[![Quality Gate Status](https://sonarcloud.io/api/project_badges/measure?project=chevere_action&metric=alert_status)](https://sonarcloud.io/dashboard?id=chevere_action)
[![Maintainability Rating](https://sonarcloud.io/api/project_badges/measure?project=chevere_action&metric=sqale_rating)](https://sonarcloud.io/dashboard?id=chevere_action)
[![Reliability Rating](https://sonarcloud.io/api/project_badges/measure?project=chevere_action&metric=reliability_rating)](https://sonarcloud.io/dashboard?id=chevere_action)
[![Security Rating](https://sonarcloud.io/api/project_badges/measure?project=chevere_action&metric=security_rating)](https://sonarcloud.io/dashboard?id=chevere_action)
[![Coverage](https://sonarcloud.io/api/project_badges/measure?project=chevere_action&metric=coverage)](https://sonarcloud.io/dashboard?id=chevere_action)
[![Technical Debt](https://sonarcloud.io/api/project_badges/measure?project=chevere_action&metric=sqale_index)](https://sonarcloud.io/dashboard?id=chevere_action)
[![CodeFactor](https://www.codefactor.io/repository/github/chevere/action/badge)](https://www.codefactor.io/repository/github/chevere/action)

## Summary

Action provides an object oriented convention for working with [Parameter](https://github.com/chevere/parameter).

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

### Main method

Use the `main` method to determine your action's main logic. Use **attributes** from [chevere/parameter](https://github.com/chevere/parameter) on arguments and method return to add validation rules.

* Before validation rules:

```php
class MyAction
{
    protected function main(
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

class MyAction extends Action
{
    #[ReturnAttr(
        new IntAttr(min: 0, max: 100)
    )]
    protected function main(
        #[StringAttr('/^ok/')]
        string $value
    ): int {
        return mb_strlen($value) * 5;
    }
}
```

## Using actions

Invoke action's main logic passing the arguments you would pass to `main`. Action internal runtime will validate arguments and return against all defined rules.

💡 You can toy with this by running `php demo/demo.php`

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
protected function main(): string
{
    return 'chevere';
}
```

### Custom main method

Override Action's `mainMethod` to define a custom `main` method to use.

```php
public static function mainMethod(): string
{
    return 'altMain';
}
```

## Controller

The Controller component is a special type of Action in charge of handling incoming instructions. Its `main` method only takes parameters of type `string`.

Controller is intended to use them wired to:

* Web Servers
* CLI applications
* Application runners

### Defining a Controller

A Controller implements the `ControllerInterface`. You can extend `Controller` to quick create a compliant Controller:

```php
use Chevere\Controller\Controller;

class SomeController extends Controller
{
    // ...
}
```

### Main Parameters

Parameters are defined in the `main` method but it just takes strings.

```php
public function main(
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

public function main(
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
