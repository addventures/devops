<?php

namespace Add\Blt\Plugin\Commands\Env;

use Add\Blt\Plugin\Commands\BaseCommand;

/**
 * Defines the "env:import" command.
 */
class ImportCommand extends BaseCommand {

  /**
   * Imports an environment based on the export config from another.
   *
   * @param array $options
   *   The command options.
   *
   * @command env:import
   */
  public function exec($options = [
    'ni' => FALSE,
  ]) {
  }

}
