<?php

namespace Bitrock\Models;

trait BitrixModelTrait
{
    public static function getDefaultOrder()
    {
        return ['ID' => 'ASC'];
    }
}