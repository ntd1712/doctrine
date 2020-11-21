<?php

namespace Chaos\Support\Doctrine\DBAL\Types;

/**
 * Class BigIntType.
 *
 * @see \Doctrine\DBAL\Types\BigIntType
 */
class BigIntType extends Type
{
    /**
     * {@inheritDoc}
     *
     * <code>
     * $value = Type::getType(Type::BIGINT_TYPE)->convertToPHPValue($value);
     * </code>
     *
     * @param mixed $value The value to convert.
     *
     * @throws ConversionException
     *
     * @return null|string
     */
    public function convertToPHPValue($value)
    {
        if (is_array($value) || is_object($value)) {
            throw ConversionException::conversionFailed($value, $this);
        }

        return null === $value ? null : (string) $value;
    }
}
