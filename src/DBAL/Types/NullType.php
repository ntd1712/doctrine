<?php

namespace Chaos\Support\Doctrine\DBAL\Types;

/**
 * Class NullType.
 *
 * @author t(-.-t) <ntd1712@mail.com>
 */
class NullType extends Type
{
    /**
     * {@inheritDoc}
     *
     * <code>
     * $value = Type::getType(Type::NULL_TYPE)->convertToPHPValue($value);
     * </code>
     *
     * @param mixed $value The value to convert.
     *
     * @return null
     */
    public function convertToPHPValue($value)
    {
        return null;
    }
}
