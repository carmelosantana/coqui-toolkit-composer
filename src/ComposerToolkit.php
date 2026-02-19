<?php

declare(strict_types=1);

namespace CoquiBot\Toolkits\Composer;

use CarmeloSantana\PHPAgents\Contract\PackageEventListenerInterface;
use CarmeloSantana\PHPAgents\Contract\ToolkitInterface;

/**
 * Toolkit providing Composer dependency management for Coqui workspaces.
 *
 * Auto-discovered by Coqui's ToolkitDiscovery when installed via Composer.
 * All operations target the workspace's own composer.json — the host project
 * is never modified.
 *
 * Accepts an optional PackageEventListenerInterface so that the host application
 * can react to package installs/removals (e.g. scanning for new toolkits).
 */
final class ComposerToolkit implements ToolkitInterface
{
    public function __construct(
        private readonly string $workspacePath = '',
        private readonly ?PackageEventListenerInterface $listener = null,
    ) {}

    /**
     * Factory method for ToolkitDiscovery — reads workspace path from environment.
     */
    public static function fromEnv(): self
    {
        $workspacePath = getenv('COQUI_WORKSPACE_PATH');

        return new self(
            workspacePath: is_string($workspacePath) && $workspacePath !== '' ? $workspacePath : '',
        );
    }

    public function tools(): array
    {
        return [
            new ComposerTool(
                workspacePath: $this->resolveWorkspacePath(),
                listener: $this->listener,
            ),
        ];
    }

    public function guidelines(): string
    {
        return <<<'GUIDELINES'
            <COMPOSER-TOOLKIT-GUIDELINES>
            Use the `composer` tool to manage workspace dependencies.

            Workflow:
            1. Use `packagist` to search for and evaluate packages first.
            2. Use `composer` with action `require` to install.
            3. Use `package_info` to learn a package's API after installing.

            Key behaviors:
            - All operations target the workspace composer project only.
            - Mutating operations (require, remove, update) create automatic backups.
            - A denylist blocks full frameworks (Laravel, Laminas, etc.) to prevent conflicts.
            - After installing, newly discovered toolkits are registered automatically.
            - Security audits run after every install.

            Available actions: require, remove, show, installed, update, validate, outdated, audit
            </COMPOSER-TOOLKIT-GUIDELINES>
            GUIDELINES;
    }

    /**
     * Resolve the workspace path — checks constructor value, then environment.
     */
    private function resolveWorkspacePath(): string
    {
        if ($this->workspacePath !== '') {
            return $this->workspacePath;
        }

        $env = getenv('COQUI_WORKSPACE_PATH');

        return is_string($env) && $env !== '' ? $env : '';
    }
}
