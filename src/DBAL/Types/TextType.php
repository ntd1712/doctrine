<?php

namespace Chaos\Support\Doctrine\DBAL\Types;

/**
 * Class TextType.
 *
 * @see \Doctrine\DBAL\Types\TextType
 */
class TextType extends Type
{
    /**
     * {@inheritDoc}
     *
     * <code>
     * $value = Type::getType(Type::TEXT_TYPE)->convertToPHPValue($value);
     * </code>
     *
     * @param mixed $value The value to convert.
     *
     * @return string
     */
    public function convertToPHPValue($value)
    {
        return is_resource($value) ? stream_get_contents($value) : $value;
    }
}
