<?php

namespace Bitrock\Utils\FileGenerator\Generator;
use Bitrock\Utils\FileGenerator\Exceptions\FileGeneratorException;

trait StubManagerTrait
{
    public function fillPlaceholder(
        string $stubString,
        array $placeHolders,
        string $value,
        string $stubNotFoundException = '',
        string $valueNotFoundException = ''
    ): string
    {
        if (!empty($value)) {
            foreach ($placeHolders as $placeHolder) {
                if (strrpos($stubString, $placeHolder)) {
                    return str_replace($placeHolder, $value, $stubString);
                }
            }
            $stubExceptionMessage = !empty($stubNotFoundException)
                ? $stubNotFoundException
                : 'Stub placeholder was not found in ' . __METHOD__;
            throw new FileGeneratorException($stubExceptionMessage);
        }

        $emptyValueExceptionMessage = !empty($valueNotFoundException)
            ? $valueNotFoundException
            : 'Vale for placeholder is not found in ' . __METHOD__;
        throw new FileGeneratorException($emptyValueExceptionMessage);
    }

    public function clearPlaceholder(
        string $stubString,
        array $placeHolders,
        string $stringToRemove,
        string $exception = ''
    ): string
    {
        foreach ($placeHolders as $placeHolder) {
            if (strrpos($stubString, $placeHolder)) {
                return str_replace($stringToRemove, '', $stubString);
            }
        }

        $exceptionMessage = !empty($exception)
            ? $exception
            : 'Placeholder was not found in ' . __METHOD__;
        throw new FileGeneratorException($exceptionMessage);
    }
}