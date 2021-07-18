<?php

namespace Chaos\Support\Doctrine\DBAL\Types;

/**
 * Class TrueType.
 *
 * @author t(-.-t) <ntd1712@mail.com>
 */
class TrueType extends Type
{
    /**
     * {@inheritDoc}
     *
     * <code>
     * $value = Type::getType(Type::TRUE_TYPE)->convertToPHPValue($value);
     * </code>
     *
     * @param mixed $value The value to convert.
     *
     * @return bool
     */
    public function convertToPHPValue($value)
    {
        return true;
    }
}
