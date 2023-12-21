# Action

> 🔔 Subscribe to the [newsletter](https://chv.to/chevere-newsletter) to don't miss any update regarding Chevere.

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

Action provides an object-oriented convention for working with [Parameter](https://github.com/chevere/parameter). The convention is the implementation of `ActionInterface` and the usage of `main` method to define class main logic.

## Quick start

Install with [Composer](https://packagist.org/packages/chevere/action)

```sh
composer require chevere/action
```

## Cookbook

* Create an action by using `ActionTrait`:

```php
use Chevere\Action\Interfaces\ActionInterface;
use Chevere\Action\Traits\ActionTrait;

class MyAction implements ActionInterface
{
    use ActionTrait;
    // ...
}
```

* (Alternative) Create an action by extending `Action`:

```php
use Chevere\Action\Action;

class MyAction extends Action
{
    // ...
}
```

* Use the `main` method to validate your action's main logic using **attributes** on parameters and method return:

```php
use Chevere\Parameter\Attributes\IntAttr;
use Chevere\Parameter\Attributes\ReturnAttr;

#[ReturnAttr(
    new IntAttr(min: 0, max: 100)
)]
protected function main(
    #[StringAttr('/^ok/')]
    string $value
): int
{
    return 10;
}
```

* **Invoke** the action passing the `main` arguments to validate arguments and return:

```php
$action = new MyAction();
$result = $action('ok muy bueno');
```

## Advanced usage

For validating `return` beyond the limitations of PHP's attributes you can define the `return()` function. In this context you can use and remix any [Parameter function](https://github.com/chevere/parameter#function-reference).

```php
use Chevere\Action\Interfaces\ParameterInterface;
use function Chevere\Parameter\string;

public static function return(): ParameterInterface
{
    return string();
}
```

You can also forward parameter resolution to a callable using `CallableAttr`:

```php
use Chevere\Action\Attributes\CallableAttr;
use Chevere\Action\Attributes\ReturnAttr;

#[ReturnAttr(
    new CallableAttr('myCallable')
)]
protected function main(): string
{
    return 'chevere';
}
```

## Documentation

Documentation is available at [chevere.org](https://chevere.org/).

## License

Copyright 2023 [Rodolfo Berrios A.](https://rodolfoberrios.com/)

Chevere is licensed under the Apache License, Version 2.0. See [LICENSE](LICENSE) for the full license text.

Unless required by applicable law or agreed to in writing, software distributed under the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the License for the specific language governing permissions and limitations under the License.
