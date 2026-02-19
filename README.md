# Coqui Composer Toolkit

Composer dependency management toolkit for [Coqui](https://github.com/AgentCoqui/coqui). Provides workspace-sandboxed `composer require`, `remove`, `update`, and audit tools that agents can use to manage dependencies at runtime.

## Requirements

- PHP 8.4+
- Composer 2.x available on `PATH`

## Installation

```bash
composer require coquibot/coqui-toolkit-composer
```

When installed alongside Coqui, the toolkit is **auto-discovered** via Composer's `extra.php-agents.toolkits` — no manual registration needed.

## Tools Provided

### `composer`

Manage Composer dependencies in the workspace.

| Parameter | Type   | Required | Description                                        |
|-----------|--------|----------|----------------------------------------------------|
| `action`  | enum   | Yes      | `require`, `remove`, `show`, `installed`, `update`, `validate`, `outdated`, `audit` |
| `package` | string | No       | Package name (vendor/package)                      |
| `version` | string | No       | Version constraint (e.g. `^2.0`)                   |
| `dev`     | bool   | No       | Use `--dev` flag (default: false)                  |

**Safety features:**
- All operations target the workspace project only — the host project is never modified
- Mutating operations create automatic backups before executing
- A denylist blocks full frameworks (Laravel, Laminas, Symfony framework-bundle, etc.)
- Security audit runs automatically after every install
- Newly installed toolkit packages are detected and registered via `PackageEventListenerInterface`

## Package Event Listener

The toolkit accepts a `PackageEventListenerInterface` (from `carmelosantana/php-agents`) in its constructor. When provided, the listener is notified after package installs and removals — enabling features like automatic toolkit discovery.

```php
use CoquiBot\Toolkits\Composer\ComposerToolkit;
use CarmeloSantana\PHPAgents\Contract\PackageEventListenerInterface;

$toolkit = new ComposerToolkit(
    workspacePath: '/path/to/.workspace',
    listener: $myDiscoveryListener,
);
```

## Standalone Usage

```php
<?php

declare(strict_types=1);

use CoquiBot\Toolkits\Composer\ComposerToolkit;

require __DIR__ . '/vendor/autoload.php';

$toolkit = ComposerToolkit::fromEnv();

foreach ($toolkit->tools() as $tool) {
    echo $tool->name() . ': ' . $tool->description() . PHP_EOL;
}

// Install a package
$result = $toolkit->tools()[0]->execute([
    'action' => 'require',
    'package' => 'monolog/monolog',
]);
echo $result->content;
```

## Development

```bash
git clone https://github.com/AgentCoqui/coqui-toolkit-composer.git
cd coqui-toolkit-composer
composer install
```

### Run tests

```bash
./vendor/bin/pest
```

### Static analysis

```bash
./vendor/bin/phpstan analyse
```

## License

MIT
