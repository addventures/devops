<?php

namespace Add\Blt\Plugin\Commands\App;

use Add\Blt\Plugin\Commands\BaseCommand;

/**
 * Defines the "app:add" command.
 */
class AddCommand extends BaseCommand {

  /**
   * Add a new app to a platform.
   *
   * @param array $options
   *   The command options.
   *
   * @command app:add
   */
  public function exec($options = [
    'ni' => FALSE,
  ]) {
  }

}
