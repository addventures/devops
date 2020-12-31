<?php

namespace Add\Blt\Plugin\Commands\Sys;

use Add\Blt\Plugin\Commands\BaseCommand;

/**
 * Defines the "sys:info" command.
 */
class InfoCommands extends BaseCommand {

  /**
   * Prints a table of information about the full system.
   *
   * @param array $options
   *   The command options.
   *
   * @command sys:info
   */
  public function exec($options = [
    'ni' => FALSE,
  ]) {

    $config = $this->getConfigSys()->export();
    $this->printArrayAsTable($config);

  }

}
