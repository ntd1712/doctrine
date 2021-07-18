<?php

namespace Chaos\Support\Doctrine\DBAL\Types;

/**
 * Class FalseType.
 *
 * @author t(-.-t) <ntd1712@mail.com>
 */
class FalseType extends Type
{
    /**
     * {@inheritDoc}
     *
     * <code>
     * $value = Type::getType(Type::FALSE_TYPE)->convertToPHPValue($value);
     * </code>
     *
     * @param mixed $value The value to convert.
     *
     * @return bool
     */
    public function convertToPHPValue($value)
    {
        return false;
    }
}
