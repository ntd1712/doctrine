<?php

namespace Chaos\Support\Doctrine\DBAL\Types;

/**
 * Class JsonArrayType.
 *
 * @see \Doctrine\DBAL\Types\JsonArrayType
 */
class JsonArrayType extends Type
{
    /**
     * {@inheritDoc}
     *
     * <code>
     * $value = Type::getType(Type::JSON_ARRAY_TYPE)->convertToPHPValue($value);
     * </code>
     *
     * @param mixed $value The value to convert.
     *
     * @return array
     */
    public function convertToPHPValue($value)
    {
        if (null === $value || '' === $value) {
            return [];
        }

        if (is_array($value)) {
            return $value;
        }

        if (is_object($value)) {
            return (array) $value;
        }

        if (is_resource($value)) {
            $value = stream_get_contents($value);
        }

        $value = json_decode($value, true);

        return (array) $value;
    }
}
