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
    'path' => InputOption::VALUE_OPTIONAL,
    'appid' => InputOption::VALUE_OPTIONAL,
  ]) {

    $inputs = [
      'path' => [
        'label' => "Absolute path to directory of Drupal project root",
      ],
      'appid' => [
        'label' => "Acquia shortname app ID such as \"omnicare\"",
      ],
    ];

    $defaults = [];

    $options = $this->buildInput($inputs, $options, $defaults);

    $path_root = $options['path'];
    $path_docroot = "{$path_root}/{$this->relPathDocroot}";
    $appid = $options['appid'];

    if ($this->fs->exists("{$path_root}/composer.json")) {
      $path_composer = $path_root;
    }
    elseif ($this->fs->exists("{$path_docroot}/composer.json")) {
      $move_composer = $this->io()->askQuestion(new Question("Composer was installed to the wrong location within the docroot at {$path_docroot} instead of {$path_root}. Do you want to move it? (y/n)", 'n'));
      if ($move_composer == 'y') {
        $result = $this->taskExecStack()
          ->exec("mv {$path_docroot}/composer.json {$path_root}")
          ->exec("mv {$path_docroot}/composer.lock {$path_root}")
          ->exec("rm -rf {$path_docroot}/vendor rm -rf {$path_docroot}/core {$path_docroot}/modules/contrib {$path_docroot}/themes/contrib")
          ->stopOnFail(TRUE)
          ->run();

        if ($result->getExitCode()) {
          throw new \Exception("Composer build move failed.");
        }
      }
      else {
        throw new \Exception("Composer is installed at wrong location.");
      }

      $path_composer = $path_root;
    }
    else {
      throw new \Exception("composer.json not found.");
    }

    $path_composer_json = "{$path_composer}/composer.json";

    // Run fin init.
    $path_docksal = "{$path_root}/.docksal";
    if (!$this->fs->exists($path_docksal)) {
      $result = $this->taskExecStack()
        ->dir($path_root)
        ->exec("echo 'y' | fin init")
        ->exec("fin config set DOCKSAL_STACK=acquia")
        ->exec("fin config set COMPOSER_MEMORY_LIMIT=-1")
        ->exec("fin config set XDEBUG_ENABLED=1")
        ->exec("fin p reset -f")
        ->exec("fin exec 'sudo composer self-update --1'")
        ->stopOnFail(TRUE)
        ->run();
      if ($result->getExitCode()) {
        throw new \Exception("Error starting Docksal.");
      }
    }

    $path_grumphp_source = "{$this->pathProject}/template/grumphp.yml.twig";
    $path_grumphp_target = "{$path_composer}/grumphp.yml";
    if (!$this->fs->exists($path_grumphp_target)) {
      $this->fs->copy($path_grumphp_source, $path_grumphp_target);
    }

    if (!$composer_build = $this->fs->getFileContent($path_composer_json)) {
      throw new \Exception("Composer build does not exist at: {$path_composer_json}");
    }

    if (!$composer_build = json_decode($composer_build, TRUE)) {
      throw new \Exception("Error parsing composer build at: {$path_composer_json}");
    }

    // Confirm in repositories.
    if (empty($composer_build['repositories'][$this->projectName])) {
      $composer_build['repositories'][$this->projectName] = [
        'type' => 'vcs',
        'url' => 'https://github.com/addventures/devops',
      ];
    }

    $dependencies_remove = [
      'wikimedia/composer-merge-plugin',
      'composer/installers',
      'cweagans/composer-patches',
      'oomphinc/composer-installers-extender,'
    ];
    foreach ($dependencies_remove as $dependency) {
      if (isset($composer_build['require'][$dependency])) {
        unset($composer_build['require'][$dependency]);
      }
    }

    // Fix library types.
    $map_library_type = [
      'library' => 'drupal-library',
    ];
    foreach ($map_library_type as $original_type => $new_type) {
      if (in_array($original_type, $composer_build['extra']['installer-types'])) {
        $key = array_search($original_type, $composer_build['extra']['installer-types']);
        $composer_build['extra']['installer-types'][$key] = $new_type;
      }
    }

    $this->fs->setFileContent($path_composer_json, json_encode($composer_build, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));

    if (empty($composer_build['require'][$this->projectName])) {

      $result = $this->taskExecStack()
        ->dir($path_composer)
        ->exec("fin exec 'composer require {$this->projectName}'")
        ->run();

      if ($result->getExitCode() == 2) {

        $reset_composer = $this->io()->askQuestion(new Question("There was an issue with composer require command. Do you want to delete composer.lock, delete dependency directories, and try again? (y/n)", 'n'));
        if ($reset_composer == 'y') {
          $this->taskExecStack()
            ->exec("rm {$path_composer}/composer.lock")
            ->exec("rm -rf {$path_composer}/vendor rm -rf {$path_docroot}/core {$path_docroot}/modules/contrib {$path_docroot}/themes/contrib")
            ->stopOnFail(TRUE)
            ->run();

          $result = $this->taskExecStack()
            ->dir($path_composer)
            ->exec("fin exec 'composer require {$this->projectName}'")
            ->run();
          if ($result->getExitCode()) {
            throw new \Exception("Composer build process failed.");
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
    $path_settings_php_target = "{$path_composer}/blt/blt.yml";
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

    $path_drush_alias_dir = "{$path_composer}/drush/sites";
    $path_drush_alias = "{$path_composer}/drush/sites/{$appid}.site.yml";
    if (!$this->fs->exists($path_drush_alias)) {
      $this->copyDrushAliasesToPath([$appid], $path_drush_alias_dir);
    }

  }

}
