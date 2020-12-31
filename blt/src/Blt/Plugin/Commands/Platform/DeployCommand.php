<?php

namespace Add\Blt\Plugin\Commands\Platform;

use Add\Blt\Plugin\Commands\BaseCommand;

/**
 * Defines the "platform:deploy" command.
 */
class DeployCommand extends BaseCommand {

  /**
   * Add a new platform.
   *
   * @param array $options
   *   The command options.
   *
   * @command platform:deploy
   */
  public function exec($options = [
    'ni' => FALSE,
  ]) {
  }

}
