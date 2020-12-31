<?php

namespace Add\Blt\Plugin\Commands\Platform;

use Add\Blt\Plugin\Commands\BaseCommand;
use Symfony\Component\Console\Input\InputOption;

/**
 * Defines the "platform:add" command.
 */
class AddCommand extends BaseCommand {

  /**
   * Add a new platform.
   *
   * @param array $options
   *   The command options.
   *
   * @command platform:add
   *
   * @throws \Exception
   */
  public function exec($options = [
    'platform' => InputOption::VALUE_OPTIONAL,
    'id' => InputOption::VALUE_OPTIONAL,
    'label' => InputOption::VALUE_OPTIONAL,
    'hostname' => InputOption::VALUE_OPTIONAL,
    'sync' => TRUE,
  ]) {

    $inputs = [
      'platform' => [
        'label' => "Platform",
        'type' => 'choice',
        'choice' => $this->getOptionSysPlatform(),
      ],
      'label' => [
        'label' => "Acquia Cloud Application UUID",
      ],
    ];

    $defaults = [];

    $options = $this->buildInput($inputs, $options, $defaults);

    if (empty($options['id'])) {
      $options['id'] = $options['platform'];
    }

    if (empty($options['hostname'])) {
      $options['hostname'] = "{$options['id']}.local";
    }

    // Add to env.yml.
    $config_env = $this->getConfigEnv()->export();
    $config_env['platform'][$options['id']] = [
      'id' => $options['id'],
      'platform' => $options['platform'],
      'hostname' => $options['hostname'],
      'created' => $this->getDateString(),
      'role' => 'dev',
    ];
    $this->setConfigEnv($config_env);

    // Add host.

  }

}
