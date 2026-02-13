# Action

![Chevere](chevere.svg)

[![Build](https://img.shields.io/github/actions/workflow/status/chevere/action/test.yml?branch=2.1&style=flat-square)](https://github.com/chevere/action/actions)
![Code size](https://img.shields.io/github/languages/code-size/chevere/action?style=flat-square)
[![Apache-2.0](https://img.shields.io/github/license/chevere/action?style=flat-square)](LICENSE)
[![PHPStan](https://img.shields.io/badge/PHPStan-level%209-blueviolet?style=flat-square)](https://phpstan.org/)
[![Mutation testing badge](https://img.shields.io/endpoint?style=flat-square&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2Fchevere%2Faction%2F2.1)](https://dashboard.stryker-mutator.io/reports/github.com/chevere/action/2.1)

[![Quality Gate Status](https://sonarcloud.io/api/project_badges/measure?project=chevere_action&metric=alert_status)](https://sonarcloud.io/dashboard?id=chevere_action)
[![Maintainability Rating](https://sonarcloud.io/api/project_badges/measure?project=chevere_action&metric=sqale_rating)](https://sonarcloud.io/dashboard?id=chevere_action)
[![Reliability Rating](https://sonarcloud.io/api/project_badges/measure?project=chevere_action&metric=reliability_rating)](https://sonarcloud.io/dashboard?id=chevere_action)
[![Security Rating](https://sonarcloud.io/api/project_badges/measure?project=chevere_action&metric=security_rating)](https://sonarcloud.io/dashboard?id=chevere_action)
[![Coverage](https://sonarcloud.io/api/project_badges/measure?project=chevere_action&metric=coverage)](https://sonarcloud.io/dashboard?id=chevere_action)
[![Technical Debt](https://sonarcloud.io/api/project_badges/measure?project=chevere_action&metric=sqale_index)](https://sonarcloud.io/dashboard?id=chevere_action)
[![CodeFactor](https://www.codefactor.io/repository/github/chevere/action/badge)](https://www.codefactor.io/repository/github/chevere/action)

## Summary

**Chevere Action** is a PHP library that encapsulates application operations as reusable, self-validating objects. Built on the [Parameter](https://github.com/chevere/parameter) library, it implements the Action Design Pattern to enforce strict input/output validation at the class level, reducing runtime errors and improving code reliability with minimal boilerplate.

## Installing

Action is available through [Packagist](https://packagist.org/packages/chevere/action) and the repository source is at [chevere/action](https://github.com/chevere/action).

```sh
composer require chevere/action
```

## Quick Start

### Creating an Action

Create an Action by implementing the `ActionInterface` either by using `ActionTrait` or extending the `Action` class:

#### Using ActionTrait

```php
use Chevere\Action\Interfaces\ActionInterface;
use Chevere\Action\Traits\ActionTrait;

class GetUserAction implements ActionInterface
{
    use ActionTrait;

    public function __invoke(int $userId): array
    {
        return ['id' => $userId, 'name' => 'John'];
    }
}
```

#### Extending Action

```php
use Chevere\Action\Action;

class GetUserAction extends Action
{
    public function __invoke(int $userId): array
    {
        return ['id' => $userId, 'name' => 'John'];
    }
}
```

### Adding Validation

Enhance Actions with input and output validation using attributes from the Parameter library:

```php
use Chevere\Action\Action;
use Chevere\Parameter\Attributes\_arrayp;
use Chevere\Parameter\Attributes\_int;
use Chevere\Parameter\Attributes\_string;
use Chevere\Parameter\Attributes\_return;

class GetUserAction extends Action
{
    #[_return(
        new PArray(
            userId: new _int(min: 1),
            name: new _string(),
        )
    )]
    public function __invoke(
        #[_int(min: 1)]
        int $userId
    ): array {
        $this->assertArguments($userId);

        $result = ['id' => $userId, 'name' => 'John'];

        return $this->assertReturn($result);
    }
}
```

### Invoking an Action

Invoke an Action as you would a function:

```php
$action = new GetUserAction();
$result = $action(1);  // or $action->__invoke(1)
```

## Advanced Usage

### Parameter Validation Methods

Define validation rules using dedicated methods for more control and flexibility. This is especially useful when validation rules cannot be expressed as attribute attributes (literal values only).

#### Using `acceptParameters()`

Define input validation rules centrally:

```php
use Chevere\Action\Action;
use Chevere\Parameter\Interfaces\ParametersInterface;
use function Chevere\Parameter\parameters;
use function Chevere\Parameter\string;
use function Chevere\Parameter\int;

class CreatePostAction extends Action
{
    public function __invoke(
        string $title,
        string $content,
        int $authorId
    ): array {
        $this->assertArguments($title, $content, $authorId);
        return ['id' => 1];
    }

    public static function acceptParameters(): ParametersInterface
    {
        return parameters(
            title: string(minLength: 5, maxLength: 200),
            content: string(minLength: 10),
            authorId: int(min: 1),
        );
    }
}
```

#### Using `acceptReturn()`

Define output validation rules:

```php
use Chevere\Action\Action;
use Chevere\Parameter\Interfaces\ParameterInterface;
use function Chevere\Parameter\arrayOf;
use function Chevere\Parameter\int;

class GetUserAction extends Action
{
    public function __invoke(int $userId): array
    {
        $this->assertArguments($userId);
        $result = ['id' => $userId, 'name' => 'John'];
        return $this->assertReturn($result);
    }

    public static function acceptReturn(): ParameterInterface
    {
        return arrayOf(
            int(key: 'id', min: 1),
            string(key: 'name', minLength: 1),
        );
    }
}
```

### Assertion Methods

Control exactly when and how validations occur:

#### `assertArguments()`

Validate input against defined rules. Can be called with explicit arguments or automatically from the caller context:

```php
// Automatic extraction from caller context
public function __invoke($foo, $bar) {
    $this->assertArguments();
}

// Explicit arguments
public function __invoke($foo, $bar) {
    $this->assertArguments($foo, $bar);
}

// Using get_defined_vars()
public function __invoke($foo, $bar) {
    $this->assertArguments(...get_defined_vars());
}
```

#### `assertReturn()`

Validate the return value against defined rules:

```php
public function __invoke(int $id): array
{
    $result = $this->fetchUser($id);
    return $this->assertReturn($result);
}
```

#### `assert()`

Validate runtime rule coherence. Useful for checking internal state:

```php
public function __invoke(): void
{
    $this->setupDependencies();
    $this->assert();
}
```

### Advanced Rules

#### Static Rules with `acceptRulesStatic()`

Enforce design rules at class definition time. Called before any assertions:

```php
public static function acceptRulesStatic(): void
{
    $reflection = static::reflection();

    // Enforce that parameter $password is never used
    if ($reflection->parameters()->has('password')) {
        throw new LogicException('Password parameter not allowed');
    }
}
```

#### Runtime Rules with `acceptRulesRuntime()`

Enforce rules based on instance state. Called before assertions at invocation time:

```php
private array $config = [];

public function acceptRulesRuntime(): void
{
    if (empty($this->config)) {
        throw new RuntimeException('Configuration required before invocation');
    }
}
```

### Action Setup Method

Use `setUp()` to initialize an Action before invocation:

```php
class SendEmailAction extends Action
{
    private SmtpConfig $smtpConfig;

    public function setUp(SmtpConfig $config): void
    {
        $this->smtpConfig = $config;
    }

    public function __invoke(string $email): bool
    {
        $this->acceptRulesRuntime();
        return mail($email, 'Hello', 'World');
    }

    public function acceptRulesRuntime(): void
    {
        if (!isset($this->smtpConfig)) {
            throw new RuntimeException('SMTP configuration required');
        }
    }
}

// Usage
$action = new SendEmailAction();
$action->setUp($smtpConfig);
$result = $action('user@example.com');
```

### ActionName

Store and manage Action instances with their setup arguments using `ActionName`:

```php
use Chevere\Action\ActionName;

$actionName = new ActionName(
    SendEmailAction::class,
    $smtpConfig
);

// Later: reconstruct and invoke
$className = (string) $actionName;
$action = new $className();
$action->setUp(...$actionName->arguments());
$action('user@example.com');
```

Or use a factory method on your Action:

```php
class SendEmailAction extends Action
{
    public static function configure(SmtpConfig $config): ActionNameInterface
    {
        return new ActionName(static::class, $config);
    }
}

// Usage
$configured = SendEmailAction::configure($smtpConfig);
$action = new (string)$configured();
$action->setUp(...$configured->arguments());
```

### Reflection Access

Access Action metadata using `reflection()`:

```php
$action = new MyAction();

// Get parameter definitions
$parameters = $action::reflection()->parameters();

// Get return definition
$return = $action::reflection()->return();

// Check if parameter exists
if ($parameters->has('userId')) {
    // ...
}

// Iterate parameters
foreach ($parameters as $name => $parameter) {
    echo $name . ': ' . get_class($parameter);
}
```

The `Controller` is a specialized Action for handling command-like instructions. Unlike standard Actions that accept any type of parameters, Controllers restrict parameters to scalar types: `string`, `int`, and `float`.

Controllers are ideal for handling user input from APIs, CLI commands, or form submissions where all arguments arrive as scalar values.

### Creating a Controller

Extend the `Controller` class to create a compliant Controller:

```php
use Chevere\Action\Controller;
use Chevere\Parameter\Interfaces\ParametersInterface;
use function Chevere\Parameter\parameters;
use function Chevere\Parameter\string;
use function Chevere\Parameter\int;

class CreatePostController extends Controller
{
    public function __invoke(
        string $title,
        string $content,
        int $authorId
    ): array {
        $this->assertArguments($title, $content, $authorId);
        return [
            'id' => 1,
            'title' => $title,
            'author' => $authorId
        ];
    }

    public static function acceptParameters(): ParametersInterface
    {
        return parameters(
            title: string(minLength: 5, maxLength: 200),
            content: string(minLength: 10),
            authorId: int(min: 1),
        );
    }
}
```

### Usage Example

```php
$controller = new CreatePostController();
$result = $controller(
    title: 'My First Post',
    content: 'This is great content.',
    authorId: 42
);
// Returns: ['id' => 1, 'title' => 'My First Post', 'author' => 42]
```

**Note:** Controllers enforce type validation at the class level. Any parameter with a type other than `string|int|float` will trigger a validation error during class initialization.

## Documentation

Documentation is available at [chevere.org/packages/action](https://chevere.org/packages/action).

## License

Copyright [Rodolfo Berrios A.](https://rodolfoberrios.com/)

Chevere is licensed under the Apache License, Version 2.0. See [LICENSE](LICENSE) for the full license text.

Unless required by applicable law or agreed to in writing, software distributed under the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the License for the specific language governing permissions and limitations under the License.
