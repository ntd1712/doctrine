<?php

namespace Chaos\Support\Doctrine\DBAL\Types;

/**
 * Class ConversionException.
 *
 * @see \Doctrine\DBAL\Types\ConversionException
 */
class ConversionException extends \Exception
{
    /**
     * Thrown when a type conversion fails.
     *
     * @param string $value The value.
     * @param string $toType The type to be converted to.
     *
     * @return self
     */
    public static function conversionFailed($value, $toType)
    {
        $value = 32 < strlen($value) ? substr($value, 0, 20) . '...' : $value;

        return new self('Could not convert value "' . $value . '" to ' . $toType);
    }

    /**
     * Thrown when a type conversion fails and we can make a statement about the expected format.
     *
     * @param string $value The value.
     * @param string $toType The type to be converted to.
     * @param string $expectedFormat The expected format.
     * @param null|\Throwable $previous The previous Exception instance.
     *
     * @return self
     */
    public static function conversionFailedFormat($value, $toType, $expectedFormat, ?\Throwable $previous = null)
    {
        $value = 32 < strlen($value) ? substr($value, 0, 20) . '...' : $value;

        return new self(
            'Could not convert value "' . $value . '" to ' . $toType . '. Expected format: ' . $expectedFormat,
            0,
            $previous
        );
    }

    /**
     * @param string $format The value.
     * @param string $error The error.
     *
     * @return self
     */
    public static function conversionFailedUnserialization(string $format, string $error)
    {
        return new self(
            sprintf(
                "Could not convert value to '%s' as an error was triggered by the unserialization: '%s'",
                $format,
                $error
            )
        );
    }

    /**
     * @param string $name The column type name.
     *
     * @return self
     */
    public static function unknownColumnType($name)
    {
        return new self('Unknown column type "' . $name . '" requested');
    }
}
