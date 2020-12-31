<?php

namespace Add\Blt\Plugin\Commands\Env;

use Add\Blt\Plugin\Commands\BaseCommand;

/**
 * Defines the "env:export" command.
 */
class ExportCommand extends BaseCommand {

  /**
   * Exports all environment data to a single configuration file.
   *
   * @param array $options
   *   The command options.
   *
   * @command env:export
   */
  public function exec($options = [
    'ni' => FALSE,
  ]) {
  }

}
