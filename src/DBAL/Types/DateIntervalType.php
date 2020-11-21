<?php

namespace Chaos\Support\Doctrine\DBAL\Types;

/**
 * Class DateIntervalType.
 *
 * @see \Doctrine\DBAL\Types\DateIntervalType
 */
class DateIntervalType extends Type
{
    // <editor-fold defaultstate="collapsed" desc="Default properties">

    /**
     * @var string
     */
    private $format = '%RP%YY%MM%DDT%HH%IM%SS';

    /**
     * @param string $format The format.
     *
     * @return $this
     */
    public function setFormat($format)
    {
        $this->format = $format;

        return $this;
    }

    // </editor-fold>

    /**
     * {@inheritDoc}
     *
     * <code>
     * $value = Type::getType(Type::DATEINTERVAL_TYPE)
     *   ->convertToPHPValue($value);
     * </code>
     *
     * @param mixed $value The value to convert.
     *
     * @throws ConversionException
     *
     * @return null|\DateInterval
     */
    public function convertToPHPValue($value)
    {
        if (null === $value || $value instanceof \DateInterval) {
            return $value;
        }

        $negative = false;

        if (isset($value[0]) && ('+' === $value[0] || '-' === $value[0])) {
            $negative = '-' === $value[0];
            $value = substr($value, 1);
        }

        try {
            $val = new \DateInterval($value);

            if ($negative) {
                $val->invert = 1;
            }

            return $val;
        } catch (\Exception $e) {
            throw ConversionException::conversionFailedFormat($value, $this, $this->format, $e);
        }
    }
}
