<?php

namespace Bitrock\Utils\FileGenerator\Prototypes;

trait CommentTrait
{
    public static function generated()
    {
        return '/** Не вносите правок в этот файл!!! Он был создан автоматически */';
    }
}