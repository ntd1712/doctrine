<?php

namespace Chaos\Support\Doctrine\DBAL\Types;

/**
 * Class BooleanType.
 *
 * @see \Doctrine\DBAL\Types\BooleanType
 */
class BooleanType extends Type
{
    // <editor-fold defaultstate="collapsed" desc="Default properties">

    /**
     * @var array
     */
    private $literals = ['0', 'f', 'false', '(false)', 'n', 'no', 'off'];

    /**
     * @param array $literals The literals.
     *
     * @return $this
     */
    public function setLiterals($literals)
    {
        $this->literals = $literals;

        return $this;
    }

    // </editor-fold>

    /**
     * {@inheritDoc}
     *
     * <code>
     * $value = Type::getType(Type::BOOLEAN_TYPE)->convertToPHPValue($value);
     * </code>
     *
     * @param mixed $value The value to convert.
     *
     * @return null|bool
     */
    public function convertToPHPValue($value)
    {
        if (null === $value || is_bool($value)) {
            return $value;
        }

        if (is_string($value)) { // we only have to check if something is false
            if (in_array(strtolower($value), $this->literals, true)) {
                return false;
            }
        }

        return (bool) $value;
    }
}
