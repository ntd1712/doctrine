<?php

namespace Chaos\Support\Doctrine\DBAL\Types;

/**
 * Class TimeType.
 *
 * @see \Doctrine\DBAL\Types\TimeType
 */
class TimeType extends Type
{
    // <editor-fold defaultstate="collapsed" desc="Default properties">

    /**
     * @var string
     */
    private $format = 'H:i:s+';

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
     * $value = Type::getType(Type::TIME_TYPE)
     *   ->setFormat('H:i:s')
     *   ->convertToPHPValue($value);
     *
     * // $value = '14:48:01.017Z'; // return '1970-01-01 14:48:01.000000';
     * //          '14:48:01';      //        '1970-01-01 14:48:01.000000';
     * //          '14:48';         //        '1970-01-01 14:48:00.000000';
     * //          '14';            //        '1970-01-01 14:00:00.000000';
     * //          '';              //        'error';
     * //          null;            //        null;
     * </code>
     *
     * @param mixed $value The value to convert.
     *
     * @throws ConversionException
     *
     * @return null|\DateTime
     */
    public function convertToPHPValue($value)
    {
        if (null === $value || $value instanceof \DateTime) {
            return $value;
        }

        switch (substr_count($value, ':')) {
            case 1:
                $value .= ':00';
                break;
            case 0:
                $value .= ':00:00';
                break;
            default:
        }

        $val = \DateTime::createFromFormat('!' . $this->format, $value);

        if (!$val) {
            throw ConversionException::conversionFailedFormat($value, $this, $this->format);
        }

        return $val;
    }
}
