<?php

namespace Add\Blt\Plugin\Commands\App;

use Add\Blt\Plugin\Commands\BaseCommand;

/**
 * Defines the "app:info" command.
 */
class InfoCommand extends BaseCommand {

  /**
   * Prints info about apps in the current environment.
   *
   * @param array $options
   *   The command options.
   *
   * @command app:info
   */
  public function exec($options = [
    'ni' => FALSE,
  ]) {
  }

}
