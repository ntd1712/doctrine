<?php

namespace Chaos\Support\Doctrine\DBAL\Types;

/**
 * Class ResourceType.
 *
 * @author t(-.-t) <ntd1712@mail.com>
 */
class ResourceType extends Type
{
    /**
     * {@inheritDoc}
     *
     * <code>
     * $value = Type::getType(Type::RESOURCE_TYPE)->convertToPHPValue($value);
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
            $fp = fopen('php://memory', 'r+b');
            fwrite($fp, $value);
            rewind($fp);
            $value = $fp;
        }

        if (!is_resource($value)) {
            throw ConversionException::conversionFailed($value, $this);
        }

        return $value;
    }
}
