<?php

declare(strict_types=1);

use CoquiBot\Toolkits\Composer\ComposerToolkit;

test('toolkit implements ToolkitInterface', function () {
    $toolkit = new ComposerToolkit(workspacePath: sys_get_temp_dir());

    expect($toolkit)->toBeInstanceOf(\CarmeloSantana\PHPAgents\Contract\ToolkitInterface::class);
});

test('tools returns composer tool', function () {
    $toolkit = new ComposerToolkit(workspacePath: sys_get_temp_dir());
    $tools = $toolkit->tools();

    expect($tools)->toHaveCount(1);
    expect($tools[0]->name())->toBe('composer');
});

test('guidelines returns non-empty string', function () {
    $toolkit = new ComposerToolkit(workspacePath: sys_get_temp_dir());

    expect($toolkit->guidelines())->toBeString()->not->toBeEmpty();
});

test('fromEnv creates instance', function () {
    $toolkit = ComposerToolkit::fromEnv();

    expect($toolkit)->toBeInstanceOf(ComposerToolkit::class);
});
