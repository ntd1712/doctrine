<?php

namespace Chaos\Support\Doctrine\DBAL\Types;

/**
 * Class DateImmutableType.
 *
 * @see \Doctrine\DBAL\Types\DateImmutableType
 */
class DateImmutableType extends Type
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
     * $value = Type::getType(Type::DATE_IMMUTABLE_TYPE)
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
     * @return null|\DateTimeImmutable
     */
    public function convertToPHPValue($value)
    {
        if (null === $value || $value instanceof \DateTimeImmutable) {
            return $value;
        }

        $val = \DateTimeImmutable::createFromFormat('!' . $this->format, $value);

        if (!$val) {
            throw ConversionException::conversionFailedFormat($value, $this, $this->format);
        }

        return $val;
    }
}
