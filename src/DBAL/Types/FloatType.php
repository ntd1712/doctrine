<?php

namespace Chaos\Support\Doctrine\DBAL\Types;

/**
 * Class FloatType.
 *
 * @see \Doctrine\DBAL\Types\FloatType
 */
class FloatType extends Type
{
    /**
     * {@inheritDoc}
     *
     * <code>
     * $value = Type::getType(Type::FLOAT_TYPE)->convertToPHPValue($value);
     * </code>
     *
     * @param mixed $value The value to convert.
     *
     * @throws ConversionException
     *
     * @return null|float
     */
    public function convertToPHPValue($value)
    {
        if (is_object($value)) {
            throw ConversionException::conversionFailed($value, $this);
        }

        return null === $value ? null : (float) $value;
    }
}
