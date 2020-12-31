<?php

namespace Add\Blt\Plugin\Commands\Platform;

use Add\Blt\Plugin\Commands\BaseCommand;

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
    'ni' => FALSE,
  ]) {
  }

}
