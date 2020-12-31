<?php

namespace Add\Blt\Plugin\Commands\Code;

use Add\Blt\Plugin\Commands\BaseCommand;
use Symfony\Component\Console\Question\Question;

/**
 * Defines the "code:scan" command.
 */
class ScanCommand extends BaseCommand {

  /**
   * Generate a report based on Drupal coding standards.
   *
   * @param array $options
   *   The command options.
   *
   * @command code:scan
   *
   * @throws \Exception
   */
  public function exec($options = [
    'ni' => FALSE,
  ]) {

    if (empty($options['path'])) {
      $options['path'] = $this->io()->askQuestion(new Question("Enter a path to scan"));
    }

    $cmd = "{$_ENV['SYS_PATH_ROOT']}/vendor/bin/phpcs --standard=Drupal --extensions=php,module,inc,install,test,profile,theme,css,info,txt,md {$options['path']}";
    $this->taskExec($cmd)
      ->dir($_ENV['SYS_PATH_ROOT'])
      ->run();

  }

}
