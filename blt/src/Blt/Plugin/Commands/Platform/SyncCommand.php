<?php

namespace Add\Blt\Plugin\Commands\Platform;

use Add\Blt\Plugin\Commands\BaseCommand;

/**
 * Defines the "platform:sync" command.
 */
class SyncCommand extends BaseCommand {

  /**
   * Sync a platform across environments, including database and managed files.
   *
   * @param array $options
   *   The command options.
   *
   * @command platform:sync
   */
  public function exec($options = [
    'ni' => FALSE,
  ]) {
  }

}
