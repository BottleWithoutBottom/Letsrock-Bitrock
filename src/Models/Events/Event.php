<?php

namespace Bitrock\Models\Events;

use Bitrock\Models\Model;

/** Базовый класс для почтовых событий битрикс */
abstract class Event extends Model
{
    protected $eventName;

    public function getEventName(): string
    {
        return $this->eventName;
    }
}