<?php

namespace Chaos\Support\Doctrine\DBAL\Types;

/**
 * Class IntegerType.
 *
 * @see \Doctrine\DBAL\Types\IntegerType
 */
class IntegerType extends Type
{
    /**
     * {@inheritDoc}
     *
     * <code>
     * $value = Type::getType(Type::INTEGER_TYPE)->convertToPHPValue($value);
     * </code>
     *
     * @param mixed $value The value to convert.
     *
     * @throws ConversionException
     *
     * @return null|int
     */
    public function convertToPHPValue($value)
    {
        if (is_object($value)) {
            throw ConversionException::conversionFailed($value, $this);
        }

        return null === $value ? null : (int) $value;
    }
}
