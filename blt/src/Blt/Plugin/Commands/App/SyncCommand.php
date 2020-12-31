<?php

namespace Add\Blt\Plugin\Commands\App;

use Add\Blt\Plugin\Commands\BaseCommand;

/**
 * Defines the "app:sync" command.
 */
class SyncCommand extends BaseCommand {

  /**
   * Syncs a single app.
   *
   * @param array $options
   *   The command options.
   *
   * @command app:sync
   */
  public function exec($options = [
    'ni' => FALSE,
  ]) {
  }

}
