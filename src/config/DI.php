<?php
use Bitrock\Logger;
use Bitrock\View\View;

return [
    Logger::class => \DI\create(Logger::class),
    View::class => \DI\create(View::class),
];