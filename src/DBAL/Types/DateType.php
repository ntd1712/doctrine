<?php

namespace Chaos\Support\Doctrine\DBAL\Types;

/**
 * Class DateType.
 *
 * @see \Doctrine\DBAL\Types\DateType
 */
class DateType extends Type
{
    // <editor-fold defaultstate="collapsed" desc="Default properties">

    /**
     * @var string
     */
    private $format = 'Y-m-d+';

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
     * $value = Type::getType(Type::DATE_TYPE)
     *   ->setFormat('Y-m-d')
     *   ->convertToPHPValue($value);
     *
     * // $value = '2018-10-05T14:48:01.017Z'; // return '2018-10-05 00:00:00.000000';
     * //          '2018-10-05T14:48:01';      //        '2018-10-05 00:00:00.000000';
     * //          '2018-10-05T14:48';         //        '2018-10-05 00:00:00.000000';
     * //          '2018-10-05T14';            //        '2018-10-05 00:00:00.000000';
     * //          '2018-10-05';               //        '2018-10-05 00:00:00.000000';
     * //          '2018-10';                  //        'error';
     * //          '2018';                     //        'error';
     * //          '';                         //        'error';
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

        $val = \DateTime::createFromFormat('!' . $this->format, $value);

        if (!$val) {
            throw ConversionException::conversionFailedFormat($value, $this, $this->format);
        }

        return $val;
    }
}
