<?php

namespace Add\Blt\Plugin\Commands\App;

use Add\Blt\Plugin\Commands\BaseCommand;

/**
 * Defines the "app:deploy" command.
 */
class DeployCommand extends BaseCommand {

  /**
   * Deploys a single app.
   *
   * @param array $options
   *   The command options.
   *
   * @command app:deploy
   */
  public function exec($options = [
    'ni' => FALSE,
  ]) {
  }

}
