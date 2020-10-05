<?php

namespace App;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use App\MakeFile;

class Files {

  const BYTE_UNITS = ["B", "KB", "MB", "GB", "TB", "PB", "EB", "ZB", "YB"];

  const BYTE_PRECISION = [0, 0, 1, 2, 2, 3, 3, 4, 4];

  const BYTE_NEXT = 1024;

  /**
   * Convert bytes to be human readable.
   *
   * @param int $bytes Bytes to make readable
   * @param int|null $precision Precision of rounding
   *
   * @return string Human readable bytes
   */
  public static function HumanReadableBytes($bytes, $precision = NULL) {
    for ($i = 0; ($bytes / self::BYTE_NEXT) >= 0.9 && $i < count(self::BYTE_UNITS); $i++) {
      $bytes /= self::BYTE_NEXT;
    }
    return round($bytes, is_null($precision) ? self::BYTE_PRECISION[$i] : $precision) . ' ' . self::BYTE_UNITS[$i];
  }
}

class GetFiles {

  public static function files($dir) {
    // Custom frontpage url for different brands.

    $files = [];
    $finder = new Finder();
    $filesystem = new Filesystem();
    $d = 0;

    $dirs = $finder->directories()
      ->in($dir)
      ->notPath('/files')
      ->notPath('/thumbs')
      ->sortByName()
      ->depth('== 0');

    /**
     * Get the fils
     */

    foreach ($dirs as $maindir) {
      $d++;
      $i = 0;

      $subdir = $maindir->getRealPath();

      if ($filesystem->exists($maindir->getRealPath() . "/config.json")) {
        $dirdata = file_get_contents($maindir->getRealPath() . "/config.json");
        $dirdata = json_decode($dirdata, TRUE);

        $files[$d]['name'] = $dirdata['name'];
        $files[$d]['descr'] = isset($dirdata['descr']) ? $dirdata['descr'] : '';
      }

      // Get the files
      $subdirfinder = new Finder();

      $dirfiles = $subdirfinder->files()->in($subdir)->Name([
        '*.png',
        '*.jpg',
        '*.jpg',
        '*.svg',
        '*.eps',
        '*.pdf',
      ])->depth('== 0')->sortByAccessedTime();


      $maindest = 'files/' . $maindir->getBaseName();

      // Get files or directory
      foreach ($dirfiles as $file) {
        $i++;
        $files[$d]['files'][$i] = MakeFile::Make($file, $maindest, $dirdata);
      }

      /**
       * Get the subdirectories & files
       */

      $subsubdirfinder = new Finder();

      $subdirdirs = $subsubdirfinder->directories()
        ->notName('thumbs')
        ->in($maindir->getRealPath())
        ->depth('== 0');


      if ($subdirdirs->hasResults()) {
        $si = 0;

        foreach ($subdirdirs as $subdir) {
          $si++;
          $fi = 0;

          $subdirname = $subdir->getBaseName();

          $files[$d]['sub'][$si]['name'] = isset($dirdata['subdirs'][$subdirname]['name']) ? $dirdata['subdirs'][$subdirname]['name'] : ucfirst($subdirname);
          $files[$d]['sub'][$si]['descr'] = isset($dirdata['subdirs'][$subdirname]['descr']) ? $dirdata['subdirs'][$subdirname]['descr'] : '';

          $subfilefinder = new Finder();

          $subdirfiles = $subfilefinder->files()
            ->in($subdir->getPath() . '/' . $subdirname)
            ->Name([
              '*.png',
              '*.jpg',
              '*.jpg',
              '*.svg',
              '*.eps',
              '*.pdf',
            ])
            ->depth('== 0')
            ->sortByName();


          foreach ($subdirfiles as $file) {
            $fi++;
            $basefolder = $maindir->getBaseName();
            $dirpath = $basefolder . '/' . $subdirname;

            $files[$d]['sub'][$si]['files'][$fi] = MakeFile::Make($file, $dirpath, $dirdata);
          }
        }
      }
    }

    return $files;

  }
}
