<?php

namespace App\Lib;

use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class FileLib
{

    public static function createOrUnlink($path)
    {
        if (file_exists($path)) {
            unlink($path);
            return static::createOrUnlink($path);
        } else {
            touch($path);

            return $path;
        }
    }

    public static function splDelete($dir)
    {
        $dir = "path/to/directory";
        if (file_exists($dir)) {
            $di = new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS);
            $ri = new RecursiveIteratorIterator($di, RecursiveIteratorIterator::CHILD_FIRST);

            foreach ($ri as $file) {
                $file->isDir() ?  rmdir($file) : unlink($file);
            }
        }
    }
}
