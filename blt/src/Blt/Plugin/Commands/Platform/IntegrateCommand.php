<?php

namespace Add\Blt\Plugin\Commands\Platform;

use Add\Blt\Plugin\Commands\BaseCommand;
use Drupal\Component\Serialization\Json;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Question\Question;

/**
 * Defines the "platform:integrate" command.
 */
class IntegrateCommand extends BaseCommand {

  /**
   * Integrate an existing platform with this project.
   *
   * @param array $options
   *   The command options.
   *
   * @command platform:integrate
   *
   * @throws \Exception
   */
  public function exec($options = [
    'pathroot' => InputOption::VALUE_OPTIONAL,
    'pathcomposer' => InputOption::VALUE_OPTIONAL,
    'appid' => InputOption::VALUE_OPTIONAL,
  ]) {

    $inputs = [
      'pathroot' => [
        'label' => "Absolute path to directory of Drupal project root",
      ],
      'pathcomposer' => [
        'label' => "Absolute path to directory of Drupal project composer build",
      ],
      'appid' => [
        'label' => "Acquia shortname app ID such as \"omnicare\"",
      ],
    ];

    $defaults = [];

    $options = $this->buildInput($inputs, $options, $defaults);

    $pathroot = $options['pathroot'];
    $pathcomposer = $options['pathcomposer'];
    $pathcomposer_build = "{$pathcomposer}/composer.json";
    $path_docroot = "{$pathroot}/{$this->relPathDocroot}";
    $appid = $options['appid'];

    // Run fin init.
    $path_docksal = "{$pathroot}/.docksal";
    if (!$this->fs->exists($path_docksal)) {
      $result = $this->taskExecStack()
        ->dir($pathroot)
        ->exec("echo 'y' | fin init")
        ->exec("fin config set DOCKSAL_STACK=acquia")
        ->exec("fin config set COMPOSER_MEMORY_LIMIT=-1")
        ->exec("fin config set XDEBUG_ENABLED=1")
        ->exec("fin p reset -f")
        ->stopOnFail(TRUE)
        ->run();
      if ($result->getExitCode()) {
        throw new \Exception("Error starting Docksal.");
      }
    }

    $path_grumphp_source = "{$this->pathProject}/template/grumphp.yml.twig";
    $path_grumphp_target = "{$pathcomposer}/grumphp.yml";
    if (!$this->fs->exists($path_grumphp_target)) {
      $this->fs->copy($path_grumphp_source, $path_grumphp_target);
    }

    // Run composer require devops.
    $pathcomposer_json = "{$pathcomposer_build}/composer.json";

    if (!$composer_build = $this->fs->getFileContent($pathcomposer_build)) {
      throw new \Exception("Composer build does not exist at: {$pathcomposer_json}");
    }

    $composer_build = json_decode($composer_build, TRUE);

    // Confirm in repositories.
    if (empty($composer_build['repositories'][$this->projectName])) {
      $composer_build['repositories'][$this->projectName] = [
        'type' => 'vcs',
        'url' => 'https://github.com/addventures/devops',
      ];
      $this->fs->setFileContent($pathcomposer_build, json_encode($composer_build, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));

    }

    if (empty($composer_build['require'][$this->projectName])) {

      $result = $this->taskExecStack()
        ->dir($pathcomposer)
        ->exec("fin exec 'composer require {$this->projectName}'")
        ->run();

      if ($result->getExitCode() == 2) {

        $reset_composer = $this->io()->askQuestion(new Question("There was an issue with composer require command. Do you want to delete composer.lock, delete dependency directories, and try again? (y/n)", 'n'));
        if ($reset_composer == 'y') {
          $this->taskExecStack()
            ->exec("rm {$pathcomposer}/composer.lock")
            ->exec("rm -rf {$pathcomposer}/vendor rm -rf {$path_docroot}/core {$path_docroot}/modules/contrib {$path_docroot}/themes/contrib")
            ->stopOnFail(TRUE)
            ->run();

          $result = $this->taskExecStack()
            ->dir($pathcomposer)
            ->exec("fin exec 'composer require {$this->projectName}'")
            ->run();
          if ($result->getExitCode()) {
          #  throw new \Exception("Composer build process failed.");
          }
        }
      }

    }

    // Add settings.php require line.
    $path_settings_php_source = "{$this->pathProject}/template/settings.devops.php.twig";
    $path_settings_php_target = "{$path_docroot}/sites/default/settings.devops.php";
    if (!$this->fs->exists($path_settings_php_target)) {
      $this->fs->copy($path_settings_php_source, $path_settings_php_target);
    }

    $path_settings_development_source = "{$this->pathProject}/template/development.services.yml.twig";
    $path_settings_development_target = "{$path_docroot}/sites/development.services.yml";
    if (!$this->fs->exists($path_settings_development_target)) {
      $this->fs->copy($path_settings_development_source, $path_settings_development_target);
    }

    // @todo replace variables in twig.
    $path_settings_php_source = "{$this->pathProject}/template/blt.yml.twig";
    $path_settings_php_target = "{$pathcomposer}/blt/blt.yml";
    if (!$this->fs->exists($path_settings_php_target)) {
      $this->fs->copy($path_settings_php_source, $path_settings_php_target);
    }

    $path_settings_php_main = "{$path_docroot}/sites/default/settings.php";
    $settings_php_content = $this->fs->getFileContent($path_settings_php_main);
    if (stripos($settings_php_content, 'settings.devops.php') === FALSE) {
      $settings_php_content .= "\n";
      $settings_php_content .= "if (getenv('XDEBUG_ENABLED') && getenv('HOST_UID')) {\n";
      $settings_php_content .= "  require DRUPAL_ROOT . '/sites/default/settings.devops.php';\n";
      $settings_php_content .= "}\n";
      $settings_php_content .= "\n";
      $this->fs->setFileContent($path_settings_php_main, $settings_php_content);
    }

    $path_drush_alias_dir = "{$pathcomposer}/drush/sites";
    $path_drush_alias = "{$pathcomposer}/drush/sites/{$appid}.site.yml";
    if (!$this->fs->exists($path_drush_alias)) {
      $this->copyDrushAliasesToPath([$appid], $path_drush_alias_dir);
    }

  }

}
