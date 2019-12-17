<?php

namespace phpsap\saprfc;

use phpsap\classes\Api\Element;
use phpsap\classes\Api\Struct;
use phpsap\classes\Api\Table;
use phpsap\classes\Api\Value;
use phpsap\exceptions\SapLogicException;

/**
 * Trait ApiTrait
 * @package phpsap\saprfc
 * @author Gregor J.
 * @license MIT
 */
trait ApiTrait
{
    /**
     * Create either Value, Struct or Table from a given remote function parameter or return value.
     * @param string $name The name of the parameter or return value.
     * @param string $direction The direction indicating whether it's a parameter or return value.
     * @param bool $optional The flag, whether this parameter or return value is required.
     * @param array $def The parameter or return value definition containing the data type.
     * @return Value|Struct|Table
     * @throws SapLogicException In case a datatype is missing in the mappings array.
     */
    private function createApiValue($name, $direction, $optional, $def)
    {
        if ($direction === Table::DIRECTION_TABLE) {
            return new Table($name, $optional, $this->createMembers($def));
        }
        if ($def[0]['name'] !== '') {
            return new Struct($name, $direction, $optional, $this->createMembers($def));
        }
        return new Value($this->abapToType($def[0]['abap']), $name, $direction, $optional);
    }

    /**
     * Create either struct or table members from the def array of the remote function API.
     * @param array $members
     * @return Element[] An array of IElement compatible objects.
     * @throws SapLogicException In case a datatype is missing in the mappings array.
     */
    private function createMembers($members)
    {
        $result = [];
        foreach ($members as $member) {
            $result[] = new Element($this->abapToType($member['abap']), $member['name']);
        }
        return $result;
    }

    /**
     * Convert SAPRFC abap datatype to PHP-SAP datatype.
     * @param string $type The ABAP data type from the API definition.
     * @return string The PHP/SAP internal data type.
     * @throws SapLogicException In case a datatype is missing in the mappings array.
     */
    private function abapToType($type)
    {
        static $mapping = [
            'b'           => Element::TYPE_INTEGER,  //1-byte integer (internal)
            's'           => Element::TYPE_INTEGER,  //2-byte integer (internal)
            'I'           => Element::TYPE_INTEGER,  //4-byte integer
            'INT8'        => Element::TYPE_INTEGER,  //8-byte integer
            'P'           => Element::TYPE_FLOAT,    //packed number 1-16 bytes
            'DECFLOAT16'  => Element::TYPE_FLOAT,    //floating point with 16 positions
            'DECFLOAT34'  => Element::TYPE_FLOAT,    //floating point with 34 positions
            'F'           => Element::TYPE_FLOAT,    //binary floating point with 17 positions
            'C'           => Element::TYPE_STRING,   //fixed length text field
            'N'           => Element::TYPE_INTEGER,  //fixed length numeric text field 1-262143 positions
            'STRING'      => Element::TYPE_STRING,   //text string
            'X'           => Element::TYPE_HEXBIN,   //fixed length hexadecimal encoded binary data
            'XSTRING'     => Element::TYPE_HEXBIN,   //fixed length hexadecimal encoded binary data
            'D'           => Element::TYPE_DATE,     //date field
            'T'           => Element::TYPE_TIME,     //time field
        ];
        if (!array_key_exists($type, $mapping)) {
            throw new SapLogicException(sprintf('Unknown SAP data type \'%s\'!', $type));
        }
        return $mapping[$type];
    }

    /**
     * Convert SAPRFC type to PHP/SAP direction.
     * @param string $type The remote function parameter type indicating whether it's an input or a return parameter.
     * @return string The PHP/SAP internal direction.
     * @throws SapLogicException In case the given SAP RFC type is missing in the static mapping.
     */
    private function typeToDirection($type)
    {
        static $mapping = [
            'IMPORT' => Value::DIRECTION_INPUT,   //SAP remote function input parameter
            'EXPORT' => Value::DIRECTION_OUTPUT,  //SAP remote function return value or struct
            'TABLE'  => Table::DIRECTION_TABLE    //SAP remote function return table
        ];
        if (!array_key_exists($type, $mapping)) {
            throw new SapLogicException(sprintf('Unknown SAPRFC type \'%s\'!', $type));
        }
        return $mapping[$type];
    }
}
