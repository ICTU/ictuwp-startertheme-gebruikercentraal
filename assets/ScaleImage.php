<?php

namespace App;


use Intervention\Image\ImageManagerStatic as Image;
use Symfony\Component\Filesystem\Filesystem;

// create an image manager instance with favored driver

//Image::configure(array('driver' => 'imagick'));

class ScaleImage {

  public static function Scale($dir, $file) {
    $filesystem = new Filesystem();
    $thumb = [];


    $img = Image::make($file);

    $path = $dir . '/thumbs';

    // Make directory if not exists
    if (!$filesystem->exists($path)) {
      $filesystem->mkdir($path);
    }

    // Make thumbnail if not exists
    if (!$filesystem->exists($path . '/' . $file->getBaseName())) {
      // Save 500_auto image
      $img->resize(600, NULL, function ($constraint) {
        $constraint->aspectRatio();
      });

      $img->save($path . '/' . $file->getBaseName());
    }
    else {
      // file exists, return thumb
      $thumb['path'] = '/' . $path . '/' . $file->getBaseName();
      $thumb['filename'] = $file->getBaseName();
    }

    return $thumb;
  }


}

