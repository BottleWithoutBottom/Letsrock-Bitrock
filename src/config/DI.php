<?php
use Bitrock\Utils\Logger\Logger;
use Symfony\Component\HttpFoundation\Request;

return [
    Logger::class => Logger::class,
    Request::class => Request::createFromGlobals()
];