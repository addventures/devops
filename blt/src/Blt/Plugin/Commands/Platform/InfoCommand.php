<?php

namespace Add\Blt\Plugin\Commands\Platform;

use Add\Blt\Plugin\Commands\BaseCommand;
use Symfony\Component\Console\Input\InputOption;

/**
 * Defines the "platform:info" command.
 */
class InfoCommand extends BaseCommand {

  /**
   * Prints information about the platforms in this environment.
   *
   * @param array $options
   *   The command options.
   *
   * @command platform:info
   */
  public function exec($options = [
    "platform" => InputOption::VALUE_OPTIONAL
  ]) {

    if (!empty($options['platform'])) {
      $config_key = "platform.{$options['platform']}";
    }
    else {
      $config_key = "platform";
    }

    $this->printArrayAsTable($this->getConfigEnv()->get($config_key));

  }

}
