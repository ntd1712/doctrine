<?php

namespace Chaos\Support\Doctrine\DBAL\Types;

/**
 * Class DateTimeImmutableType.
 *
 * @see \Doctrine\DBAL\Types\DateTimeImmutableType
 */
class DateTimeImmutableType extends Type
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
     * $value = Type::getType(Type::DATETIME_IMMUTABLE_TYPE)
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
     * @return null|\DateTimeImmutable
     */
    public function convertToPHPValue($value)
    {
        if (null === $value || $value instanceof \DateTimeImmutable) {
            return $value;
        }

        $val = \DateTimeImmutable::createFromFormat($this->format, $value);

        if (!$val) {
            if (false !== strpos($value, 'T') && false === strpos($value, ':')) {
                $value .= ':00';
            }

            $val = date_create_immutable($value);
        }

        if (!$val) {
            throw ConversionException::conversionFailedFormat($value, $this, $this->format);
        }

        return $val;
    }
}
