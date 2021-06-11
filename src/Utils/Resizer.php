<?php
namespace Bitrock\Utils;

use Bitrock\Models\Singleton;
use Bitrock\LetsCore;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

class Resizer extends Singleton
{
    public static $resizesStorage;

    public static function preHook()
    {
        $resizeStoragePath = LetsCore::getEnv(LetsCore::RESIZES_STORAGE_PATH);

        if (empty($resizeStoragePath) || !file_exists($resizeStoragePath)) {
            throw new FileNotFoundException(LetsCore::RESIZES_STORAGE_PATH . ' was not set or doesn\'t exists');
        }
        static::$resizesStorage = require($resizeStoragePath);

        return true;
    }

    public function getResizeImageArray($src, $resizeName)
    {
        if (
            empty($src)
            || empty($resizeName)
            || empty(static::$resizesStorage)
        ) return false;

        $usedSize = static::$resizesStorage[$resizeName];
        if (!empty($usedSize)) {
            return \CFile::ResizeImageGet(
                $src,
                ['width' => $usedSize['WIDTH'], 'height' => $usedSize['HEIGHT']],
                $usedSize['BX_RESIZE']
            );
        } else {
            return $src;
        }
    }

    public function getResizeImageArrayById($id, $resizeName)
    {
        if (
            empty($id)
            || empty($resizeName)
            || empty(static::$resizesStorage)
        ) return false;

        $src = \CFile::GetFileArray($id);
        return static::getResizeImageArray($src, $resizeName);
    }
}