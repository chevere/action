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

Action implements the Action Design Pattern (a variant of the Command Pattern) that encapsulates operations as reusable, self-validating objects. Built on the [Parameter](https://github.com/chevere/parameter) library, it provides a robust framework for defining business logic with strict input/output validation, promoting type safety and reducing boilerplate code across your application.

## Installing

Action is available through [Packagist](https://packagist.org/packages/chevere/action) and the repository source is at [chevere/action](https://github.com/chevere/action).

```sh
composer require chevere/action
```

## Quick start

To create an Action class implement the [ActionInterface](src/interfaces/ActionInterface.php) either with [use ActionTrait](#use-actiontrait) or by [extends Action](#extends-action).

## Use ActionTrait

To create an action by using [ActionTrait](src/Traits/ActionTrait.php):

```php
use Chevere\Action\Interfaces\ActionInterface;
use Chevere\Action\Traits\ActionTrait;

class MyAction implements ActionInterface
{
    use ActionTrait;
    // ...
}
```

## Extends Action

To create an Action by extending [Action](src/Action.php):

```php
use Chevere\Action\Action;

class MyAction extends Action
{
    // ...
}
```

### Invoke method

Use the `__invoke()` method to determine action main logic. Use **attributes** from [chevere/parameter](https://github.com/chevere/parameter) on both parameters and return to add assertion rules.

* Before assertion rules:

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

* After assertion rules:

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

The code above demonstrates how to create an Action class with input validation and output assertion. The `$value` argument must match the regular expression `/^ok/` and the return value must be an integer between 0 and 100. See Advanced use for alternative approaches.

## Using actions

Invoke action's `__invoke()` method, same as a function. Action internal runtime will assert arguments and return against your expectations.

💡 You can try by running `php demo/demo.php`

```php
$action = new MyAction();
$result = $action->__invoke('ok muy bueno');
$result = $action('ok muy bueno'); // same thing
```

## Advanced use

This library offers flexible validation strategies to match your application's architecture. While embedding assertions within the `__invoke()` method provides maximum portability, you can also implement centralized validation logic or delegate validation responsibilities to callers. The following methods enable fine-grained control over where and how validations are performed across.

### Return method

Use method `return()` to define return value assertion rules. In this context you can use and remix any [Parameter function](https://github.com/chevere/parameter#function-reference).

**Note:** `#[ReturnAttr]` has greater precedence than `return()`.

```php
use Chevere\Action\Interfaces\ParameterInterface;
use function Chevere\Parameter\string;

public static function return(): ParameterInterface
{
    return string();
}
```

### Assert arguments method

Use method `assertArguments()` to assert Action's `__invoke()` arguments against your expectations.

```php
$action->assertArguments(...$args);
```

### Assert return method

Use method `assertReturn()` to assert Action's `__invoke()` return value against your expectations.

```php
$action->assertReturn($result);
```

### Assert method

Use method `assert()` to assert runtime rules coherence.

```php
$action->assert();
```

### Reflection method

Use method `reflection()` to access ReflectionAction instance. It enables to read Action's parameters and return assertion rules.

```php
$action::reflection()->parameters();
$action::reflection()->return();
```

### Define static rules method

Use method `defineStaticRules()` to define extra static assertion rules to constrain your custom Action design. You can see this on the [Controller class](src/Controller.php) where this method is used to constrain `__invoke()` parameters to type string.

```php
public static function defineStaticRules(): void {
    if(static::reflection()->parameters()->has('lucho')) {
        throw new LogicException('Parameter $lucho is forbidden');
    }
}
```

### Define runtime rules method

Use method `defineRuntimeRules()` to define runtime assertion rules. This method is hooked and called before `assertArguments()` and `assertReturn()`.

```php
public function defineRuntimeRules(
    ReflectionActionInterface $reflection
): void {
    if(!$this->flag) {
        throw new LogicException('Runtime rules not satisfied');
    }
}
```

## Controller

The Controller is a special type of Action in charge of handling incoming instructions. Its `__invoke()` method only takes parameters of type `string`.

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

Parameters are defined in the `__invoke()` method but it just takes strings.

```php
public function __invoke(
    string $pepito,
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
