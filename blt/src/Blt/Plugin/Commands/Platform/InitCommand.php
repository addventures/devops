<?php

namespace Add\Blt\Plugin\Commands\Platform;

use Add\Blt\Plugin\Commands\BaseCommand;

/**
 * Defines commands in the "stack:reset:*" namespace.
 */
class InitCommand extends BaseCommand {

  /**
   * Initializes a new platform or Drupal instance.
   *
   * @param array $options
   *   The command options.
   *
   * @command platform:init
   * @throws \Exception
   */
  public function init($options = [
    'ni' => FALSE,
  ]) {
    $this->notice("Creating a new platform.");

    $config_sys = $this->getConfigSys();

    $choice_template = [];
    foreach ($config_sys['template'] as $template_id => $template_config) {
      $choice_template[$template_id] = $template_config['label'];
    }

    $inputs = [
      'name' => [
        'label' => "Platform Friendly Name",
      ],
      'id' => [
        'label' => "Platform Machine ID",
      ],
      'template' => [
        'label' => "Template",
        'type' => 'choice',
        'choice' => $choice_template,
      ],
      'ace_app_id' => [
        'label' => "Acquia Cloud Application ID",
      ],
      'github_url' => [
        'label' => "Github Web URL",
      ],
    ];

    $defaults = [];

    $options = $this->buildInput($inputs, $options, $defaults);

    $template_config = $config_sys['template'][$options['template']];
    $template_git_url = $template_config['git']['url'];
    $template_git_branch = $template_config['git']['branch'];
    $path_platform = "{$this->pathSys}/platform/{$options['id']}";

    $this->taskExecStack()
      ->exec("git clone {$template_git_url} {$path_platform} --branch {$template_git_branch}")
      ->run();

    $this->taskExecStack()
      ->dir($path_platform)
      ->exec("rm -rf .git")
      ->exec("git init")
      ->exec("git remote add origin {$options['github_url']}")
      ->exec("git checkout -b develop")
      ->run();

    // Set Docksal hostname.

    // Copy Drush aliases from ACE API.

    // Copy template state.

    // Run platform:add.

    $this->success("New platform initialized at: {$path_platform}");
  }

}
