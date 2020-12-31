<?php

namespace Add\Blt\Plugin\Commands\Platform;

use Add\Blt\Plugin\Commands\BaseCommand;
use Symfony\Component\Console\Input\InputOption;

/**
 * Defines the "platform:init" command.
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
  public function exec($options = [
    'label' => InputOption::VALUE_OPTIONAL,
    'id' => InputOption::VALUE_OPTIONAL,
    'template' => InputOption::VALUE_OPTIONAL,
    'aceuuid' => InputOption::VALUE_OPTIONAL,
    'githuburl' => InputOption::VALUE_OPTIONAL,
  ]) {

    $config_sys = $this->getConfigSys();

    $choice_template = [];
    foreach ($config_sys->get('template') as $template_id => $template_config) {
      $choice_template[$template_id] = $template_config['label'];
    }

    $inputs = [
      'label' => [
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
      'aceuuid' => [
        'label' => "Acquia Cloud Application UUID",
      ],
      'githuburl' => [
        'label' => "Github Web URL",
      ],
    ];

    $defaults = [];

    $options = $this->buildInput($inputs, $options, $defaults);

    $ace_app = $this->getAceApiClientApplications()->get($options['aceuuid']);
    $ace_app_id_split = $ace_app->hosting->id;
    list($ace_env_id, $ace_app_id) = explode(':', $ace_app_id_split);

    $template_config = $config_sys->get("template.{$options['template']}");
    $template_git_url = $template_config['git']['url'];
    $template_git_branch = $template_config['git']['branch'];
    $path_platform = "{$this->pathSys}/platform/{$options['id']}";

    $hostname = "{$options['id']}.local";

    // Add to sys.yml.
    $config_env = $this->getConfigSys()->export();
    $config_env['platform'][$options['id']] = [
      'id' => $options['id'],
      'platform' => $options['id'],
      'aceuuid' => $options['aceuuid'],
      'template' => $options['template'],
      'githuburl' => $options['githuburl'],
      'created' => $this->getDateString(),
    ];
    $this->setConfigSys($config_env);

    $this->taskExecStack()
      ->exec("git clone {$template_git_url} {$path_platform} --branch {$template_git_branch}")
      ->run();

    $this->taskExecStack()
      ->dir($path_platform)
      ->exec("rm -rf .git")
      ->exec("git init")
      ->exec("git remote add origin {$options['githuburl']}")
      ->exec("git checkout -b develop")
      ->run();

    // Configure docksal.
    $this->taskExecStack()
      ->dir($path_platform)
      ->exec("fin config set VIRTUAL_HOST={$hostname}")
      ->exec("fin p start")
      ->exec("fin exec 'composer install'")
      ->run();

    // Copy Drush aliases from ACE API.

    $drush_alias_id = $template_config['drush_alias'];
    list($template_ace_app_id, $template_ace_app_env_id) = explode('.', $drush_alias_id);
    $drush_alias_path_target = "{$path_platform}/drush/sites/";
    $this->copyDrushAliasesToPath([$ace_app_id, $template_ace_app_id], $drush_alias_path_target);
//
//    // Copy source DB.
//    $cmd = "drush sql-drop -y && drush sql-sync @{$drush_alias_id} @self --create-db -y -v && drush rsync @{$drush_alias_id}:%files/ @self:sites/default/files/ -y -v && drush cr";
//    $this->taskExecStack()
//      ->dir($path_platform)
//      ->exec("fin exec '{$cmd}'")
//      ->run();

    // Run platform:add.
    $cmd_platform_add = "add platform:add --platform={$options['id']}";
    $this->invokeCommand('platform:add', [
      '--platform' => $options['id'],
    ]);

    $this->success("New platform initialized at: {$path_platform}");
  }

}
