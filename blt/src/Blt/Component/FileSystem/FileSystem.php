<?php

namespace Add\Blt\Component\FileSystem;

use Symfony\Component\Filesystem\Filesystem as Base;
use Symfony\Component\Finder\Finder;

/**
 * Extends Symfony FileSystem component with Finder and others.
 *
 * @package Add\Blt\Component\FileSystem
 */
class FileSystem extends Base {

  /**
   * @param $path
   * @param null $pattern
   *
   * @return array
   */
  public function getFilesInPath($path, $pattern = NULL) {

    $files = [];

    $finder = new Finder();
    $finder->in($path)
      ->files();

    foreach ($finder->getIterator() as $file_path => $file_info) {

      if (!empty($pattern)) {
        if (!fnmatch($pattern, $file_path)) {
          continue;
        }
      }

      $files[] = $file_path;
    }

    return $files;
    
  }

  /**
   * @param $path
   *
   * @return false|string
   */
  public function getFileContent($path) {
    return file_get_contents($path);
  }

  /**
   * @param $path
   * @param $data
   *
   * @return false|int
   */
  public function setFileContent($path, $data) {
    return file_put_contents($path, $data);
  }
  
}
