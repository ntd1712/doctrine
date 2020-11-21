<?php

namespace Chaos\Support\Doctrine\DBAL\Types;

/**
 * Class VoidType.
 *
 * @author t(-.-t) <ntd1712@mail.com>
 */
class VoidType extends Type
{
    /**
     * {@inheritDoc}
     *
     * <code>
     * $value = Type::getType(Type::VOID_TYPE)->convertToPHPValue($value);
     * </code>
     *
     * @param mixed $value The value to convert.
     *
     * @return void
     */
    public function convertToPHPValue($value)
    {
        //
    }
}
