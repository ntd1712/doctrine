<?php

namespace Chaos\Support\Doctrine\DBAL\Types;

/**
 * Class DateTimeType.
 *
 * @see \Doctrine\DBAL\Types\DateTimeType
 */
class DateTimeType extends Type
{
    // <editor-fold defaultstate="collapsed" desc="Default properties">

    /**
     * @var string
     */
    private $format = 'Y-m-d\TH:i:s+';

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
     * $value = Type::getType(Type::DATETIME_TYPE)
     *   ->setFormat('Y-m-d\TH:i:s')
     *   ->convertToPHPValue($value);
     *
     * // $value = '2018-10-05T14:48:01.017Z'; // return '2018-10-05 14:48:01.000000';
     * //          '2018-10-05T14:48:01';      //        '2018-10-05 14:48:01.000000';
     * //          '2018-10-05T14:48';         //        '2018-10-05 14:48:00.000000';
     * //          '2018-10-05T14';            //        '2018-10-05 14:00:00.000000';
     * //          '2018-10-05';               //        '2018-10-05 00:00:00.000000';
     * //          '2018-10';                  //        '2018-10-01 00:00:00.000000';
     * //          '2018';                     //        '2019-03-25 20:18:00.000000';
     * //          '';                         //        '2019-03-25 05:51:02.000000';
     * //          null;                       //        null;
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

        $val = \DateTime::createFromFormat($this->format, $value);

        if (!$val) {
            if (false !== strpos($value, 'T') && false === strpos($value, ':')) {
                $value .= ':00';
            }

            $val = date_create($value);
        }

        if (!$val) {
            throw ConversionException::conversionFailedFormat($value, $this, $this->format);
        }

        return $val;
    }
}
