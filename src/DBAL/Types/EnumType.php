<?php

namespace Chaos\Support\Doctrine\DBAL\Types;

/**
 * Class EnumType.
 *
 * @author t(-.-t) <ntd1712@mail.com>
 */
class EnumType extends Type
{
    /**
     * {@inheritDoc}
     *
     * <code>
     * $value = Type::getType(Type::ENUM_TYPE)->convertToPHPValue($value);
     * </code>
     *
     * @param mixed $value The value to convert.
     *
     * @return null|object
     */
    public function convertToPHPValue($value)
    {
        if (null === $value || is_object($value)) {
            return $value;
        }

        if (is_string($value) && class_exists($value)) {
            try {
                $value = (new \ReflectionClass($value))
                    ->newInstanceWithoutConstructor();
            } catch (\ReflectionException $e) {
                $value = null;
            }

            return $value;
        }

        if (is_resource($value)) {
            $value = stream_get_contents($value);
        }

        set_error_handler(
            function ($errno, $errstr) {
                throw ConversionException::conversionFailedUnserialization($this, $errstr);
            }
        );

        try {
            $value = unserialize($value);

            return null === $value ? null : (object) $value;
        } finally {
            restore_error_handler();
        }
    }
}
