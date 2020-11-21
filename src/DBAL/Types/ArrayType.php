<?php

namespace Chaos\Support\Doctrine\DBAL\Types;

/**
 * Class ArrayType.
 *
 * @see \Doctrine\DBAL\Types\ArrayType
 */
class ArrayType extends Type
{
    /**
     * {@inheritDoc}
     *
     * <code>
     * $value = Type::getType(Type::ARRAY_TYPE)->convertToPHPValue($value);
     * </code>
     *
     * @param mixed $value The value to convert.
     *
     * @return null|array
     */
    public function convertToPHPValue($value)
    {
        if (null === $value || is_array($value)) {
            return $value;
        }

        if (is_resource($value)) {
            $value = stream_get_contents($value);
        }

        set_error_handler(
            function ($errno, $errstr) {
                throw ConversionException::conversionFailedUnserialization($this, $errstr);
            }
        );

        try {
            $value = unserialize($value);

            return null === $value ? null : (array) $value;
        } finally {
            restore_error_handler();
        }
    }
}
