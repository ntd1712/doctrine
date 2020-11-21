<?php

namespace Chaos\Support\Doctrine\DBAL\Types;

/**
 * Class BinaryType.
 *
 * @see \Doctrine\DBAL\Types\BinaryType
 */
class BinaryType extends Type
{
    /**
     * {@inheritDoc}
     *
     * <code>
     * $value = Type::getType(Type::BINARY_TYPE)->convertToPHPValue($value);
     * </code>
     *
     * @param mixed $value The value to convert.
     *
     * @throws ConversionException
     *
     * @return null|resource
     */
    public function convertToPHPValue($value)
    {
        if (null === $value) {
            return $value;
        }

        if (is_string($value)) {
            $fp = fopen('php://temp', 'rb+');
            fwrite($fp, $value);
            fseek($fp, 0);
            $value = $fp;
        }

        if (!is_resource($value)) {
            throw ConversionException::conversionFailed($value, $this);
        }

        return $value;
    }
}
