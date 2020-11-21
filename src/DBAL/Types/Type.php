<?php

namespace Chaos\Support\Doctrine\DBAL\Types;

/**
 * Class Type.
 *
 * @see \Doctrine\DBAL\Types\Type
 */
class Type
{
    // <editor-fold defaultstate="collapsed" desc="Types map">

    public const ARRAY_TYPE = 'array';
    public const BIGINT_TYPE = 'bigint';
    public const BINARY_TYPE = 'binary';
    public const BLOB_TYPE = 'blob';
    public const BOOLEAN_TYPE = 'boolean';
    public const DATE_IMMUTABLE_TYPE = 'date_immutable';
    public const DATEINTERVAL_TYPE = 'dateinterval';
    public const DATETIME_IMMUTABLE_TYPE = 'datetime_immutable';
    public const DATETIME_TYPE = 'datetime';
    public const DATETIMETZ_IMMUTABLE_TYPE = 'datetimetz_immutable';
    public const DATETIMETZ_TYPE = 'datetimetz';
    public const DATE_TYPE = 'date';
    public const DECIMAL_TYPE = 'decimal';
    public const ENUM_TYPE = 'enum';
    public const FALSE_TYPE = 'false';
    public const FLOAT_TYPE = 'float';
    public const GUID_TYPE = 'guid';
    public const INTEGER_TYPE = 'integer';
    public const JSON_ARRAY_TYPE = 'json_array';
    public const JSON_TYPE = 'json';
    public const MEDIUMINT_TYPE = 'mediumint';
    public const MIXED_TYPE = 'mixed';
    public const NULL_TYPE = 'null';
    public const OBJECT_TYPE = 'object';
    public const RESOURCE_TYPE = 'resource';
    public const SIMPLE_ARRAY_TYPE = 'simple_array';
    public const SMALLINT_TYPE = 'smallint';
    public const STRING_TYPE = 'string';
    public const TEXT_TYPE = 'text';
    public const TIME_IMMUTABLE_TYPE = 'time_immutable';
    public const TIMESTAMP_TYPE = 'timestamp';
    public const TIME_TYPE = 'time';
    public const TINYINT_TYPE = 'tinyint';
    public const TRUE_TYPE = 'true';
    public const VARDATETIME_IMMUTABLE_TYPE = 'vardatetime_immutable';
    public const VARDATETIME_TYPE = 'vardatetime';
    public const VOID_TYPE = 'void';

    public const BOOL_TYPE = 'bool';
    public const DOUBLE_TYPE = 'double';
    public const INT_TYPE = 'int';
    public const LONG_TYPE = 'long';
    public const REAL_TYPE = 'real';
    public const UNKNOWN_TYPE = 'unknown';
    public const UUID_TYPE = 'uuid';

    /**
     * @var string[] The map of supported mapping types.
     */
    private static $typesMap = [
        self::ARRAY_TYPE => 'Chaos\Support\Doctrine\DBAL\Types\ArrayType',
        self::BIGINT_TYPE => 'Chaos\Support\Doctrine\DBAL\Types\BigIntType',
        self::BINARY_TYPE => 'Chaos\Support\Doctrine\DBAL\Types\BinaryType',
        self::BOOLEAN_TYPE => 'Chaos\Support\Doctrine\DBAL\Types\BooleanType',
        self::DATE_IMMUTABLE_TYPE => 'Chaos\Support\Doctrine\DBAL\Types\DateImmutableType',
        self::DATEINTERVAL_TYPE => 'Chaos\Support\Doctrine\DBAL\Types\DateIntervalType',
        self::DATETIME_IMMUTABLE_TYPE => 'Chaos\Support\Doctrine\DBAL\Types\DateTimeImmutableType',
        self::DATETIME_TYPE => 'Chaos\Support\Doctrine\DBAL\Types\DateTimeType',
        self::DATETIMETZ_IMMUTABLE_TYPE => 'Chaos\Support\Doctrine\DBAL\Types\DateTimeTzImmutableType',
        self::DATETIMETZ_TYPE => 'Chaos\Support\Doctrine\DBAL\Types\DateTimeTzType',
        self::DATE_TYPE => 'Chaos\Support\Doctrine\DBAL\Types\DateType',
        self::DECIMAL_TYPE => 'Chaos\Support\Doctrine\DBAL\Types\DecimalType',
        self::ENUM_TYPE => 'Chaos\Support\Doctrine\DBAL\Types\EnumType',
        self::FALSE_TYPE => 'Chaos\Support\Doctrine\DBAL\Types\FalseType',
        self::FLOAT_TYPE => 'Chaos\Support\Doctrine\DBAL\Types\FloatType',
        self::GUID_TYPE => 'Chaos\Support\Doctrine\DBAL\Types\GuidType',
        self::INTEGER_TYPE => 'Chaos\Support\Doctrine\DBAL\Types\IntegerType',
        self::JSON_ARRAY_TYPE => 'Chaos\Support\Doctrine\DBAL\Types\JsonArrayType',
        self::JSON_TYPE => 'Chaos\Support\Doctrine\DBAL\Types\JsonType',
        self::MIXED_TYPE => 'Chaos\Support\Doctrine\DBAL\Types\MixedType',
        self::NULL_TYPE => 'Chaos\Support\Doctrine\DBAL\Types\NullType',
        self::OBJECT_TYPE => 'Chaos\Support\Doctrine\DBAL\Types\ObjectType',
        self::RESOURCE_TYPE => 'Chaos\Support\Doctrine\DBAL\Types\ResourceType',
        self::SIMPLE_ARRAY_TYPE => 'Chaos\Support\Doctrine\DBAL\Types\SimpleArrayType',
        self::SMALLINT_TYPE => 'Chaos\Support\Doctrine\DBAL\Types\SmallIntType',
        self::STRING_TYPE => 'Chaos\Support\Doctrine\DBAL\Types\StringType',
        self::TEXT_TYPE => 'Chaos\Support\Doctrine\DBAL\Types\TextType',
        self::TIME_IMMUTABLE_TYPE => 'Chaos\Support\Doctrine\DBAL\Types\TimeImmutableType',
        self::TIMESTAMP_TYPE => 'Chaos\Support\Doctrine\DBAL\Types\TimestampType',
        self::TIME_TYPE => 'Chaos\Support\Doctrine\DBAL\Types\TimeType',
        self::TINYINT_TYPE => 'Chaos\Support\Doctrine\DBAL\Types\TinyIntType',
        self::TRUE_TYPE => 'Chaos\Support\Doctrine\DBAL\Types\TrueType',
        self::VARDATETIME_IMMUTABLE_TYPE => 'Chaos\Support\Doctrine\DBAL\Types\VarDateTimeImmutableType',
        self::VARDATETIME_TYPE => 'Chaos\Support\Doctrine\DBAL\Types\VarDateTimeType',
        self::VOID_TYPE => 'Chaos\Support\Doctrine\DBAL\Types\VoidType',

        self::BLOB_TYPE => 'Chaos\Support\Doctrine\DBAL\Types\BinaryType',
        self::BOOL_TYPE => 'Chaos\Support\Doctrine\DBAL\Types\BooleanType',
        self::DOUBLE_TYPE => 'Chaos\Support\Doctrine\DBAL\Types\FloatType',
        self::INT_TYPE => 'Chaos\Support\Doctrine\DBAL\Types\IntegerType',
        self::LONG_TYPE => 'Chaos\Support\Doctrine\DBAL\Types\IntegerType',
        self::MEDIUMINT_TYPE => 'Chaos\Support\Doctrine\DBAL\Types\IntegerType',
        self::REAL_TYPE => 'Chaos\Support\Doctrine\DBAL\Types\FloatType',
        self::UNKNOWN_TYPE => 'Chaos\Support\Doctrine\DBAL\Types\MixedType',
        self::UUID_TYPE => 'Chaos\Support\Doctrine\DBAL\Types\GuidType',
    ];

    /**
     * @var self[] Map of already instantiated type objects. One instance per type (flyweight).
     */
    private static $typeObjects = [];

    // </editor-fold>

    /**
     * Private constructor.
     */
    final private function __construct()
    {
        //
    }

    /**
     * Returns the string representation of the name of the class of which the object is an instance.
     *
     * @return string
     */
    public function __toString()
    {
        $parts = explode('\\', static::class);

        return str_replace('Type', '', end($parts));
    }

    /**
     * Converts a value from its database representation to its PHP representation of this type.
     *
     * @param mixed $value The value to convert.
     *
     * @return mixed The PHP representation of the value.
     */
    public function convertToPHPValue($value)
    {
        return $value;
    }

    /**
     * Factory method to create type instances. Type instances are implemented as flyweights.
     *
     * @param string $name The name of the type.
     *
     * @throws ConversionException
     *
     * @return mixed|self
     */
    public static function getType($name)
    {
        if (!isset(self::$typeObjects[$name])) {
            if (!isset(self::$typesMap[$name])) {
                throw ConversionException::unknownColumnType($name);
            }

            self::$typeObjects[$name] = new self::$typesMap[$name]();
        }

        return self::$typeObjects[$name];
    }

    /**
     * Gets the types array map which holds all registered types and the corresponding type class.
     *
     * @return string[]
     */
    public static function getTypesMap()
    {
        return self::$typesMap;
    }

    /**
     * Checks if exists support for a type.
     *
     * @param string $name The name of the type.
     *
     * @return bool TRUE if type is supported, FALSE otherwise.
     */
    public static function hasType($name)
    {
        return isset(self::$typesMap[$name]);
    }
}
