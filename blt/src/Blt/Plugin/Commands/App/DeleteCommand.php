<?php

namespace Add\Blt\Plugin\Commands\App;

use Add\Blt\Plugin\Commands\BaseCommand;

/**
 * Defines the "app:delete" command.
 */
class DeleteCommand extends BaseCommand {

  /**
   * Deletes an existing app from a platform.
   *
   * @param array $options
   *   The command options.
   *
   * @command app:delete
   */
  public function exec($options = [
    'ni' => FALSE,
  ]) {
  }

}
