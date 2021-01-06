<?php

namespace Add\Blt\Plugin\Commands\Platform;

use Add\Blt\Plugin\Commands\BaseCommand;
use Symfony\Component\Console\Input\InputOption;

/**
 * Defines the "platform:delete" command.
 */
class DeleteCommand extends BaseCommand {

  /**
   * Delete an existing platform.
   *
   * @param array $options
   *   The command options.
   *
   * @command platform:delete
   */
  public function exec($options = [
    'platform' => InputOption::VALUE_OPTIONAL,
  ]) {

    $inputs = [
      'platform' => [
        'label' => "Platform",
        'type' => 'choice',
        'choice' => $this->getOptionEnvPlatform(),
      ],
    ];

    $defaults = [];

    $options = $this->buildInput($inputs, $options, $defaults);

    $platform_id_env = "{$options['platform']}";

    $path_platform = $this->getPath("platform.{$platform_id_env}");

    $this->taskExecStack()
      ->dir($path_platform)
      ->exec("fin p rm -f")
      ->run();

    $this->taskExecStack()
      ->exec("rm -rf {$path_platform}")
      ->run();

    $config_env = $this->getConfigEnv()->export();
    unset($config_env['platform'][$platform_id_env]);
    $this->setConfigEnv($config_env);

  }

}
