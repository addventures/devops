<?php

namespace Add\Blt\Plugin\Commands\Env;

use Add\Blt\Plugin\Commands\BaseCommand;

/**
 * Defines the "env:info" command.
 */
class InfoCommand extends BaseCommand {

  /**
   * Prints information about the current environment context.
   *
   * @param array $options
   *   The command options.
   *
   * @command env:info
   */
  public function exec($options = [
    'ni' => FALSE,
  ]) {

    $config = $this->getConfigEnv()->export();
    $this->printArrayAsTable($config);

  }

}
