<?php
namespace ctur\helpers;

use SimpleXMLElement, Exception;

/**
 * Class XML Helper.
 * Static class for encode xml string to array or object & encode array to SimpleXMLElement.
 * @package ctur\helpers
 *
 * @author Cyril Turkevich
 */
class XML
{
    /* @var string $_rootNode default root node. */
    protected static $_rootNode = 'root';

    /* @var string $_listNode default list node. */
    protected static $_listNode = 'list';

    /* @var string $_itemNode default item node. */
    protected static $_itemNode = 'item';

    /* @var string $_charset default encoding node. */
    protected static $_charset = 'UTF-8';

    /* @var string $_xmlVersion default version. */
    protected static $_xmlVersion = '1.0';

    /* @var string $_attributeArrayLabel */
    protected static $_attributeArrayLabel = 'attribute:';

    /**
     * Add children node or attribute recursive.
     * @param SimpleXMLElement $element child nodes or attributes.
     * @param array $data data for create child nodes or attributes.
     * @return SimpleXMLElement child nodes with attributes.
     */
    private static function _addChildren(SimpleXMLElement $element, $data)
    {
        foreach ($data as $key => $value) {
            if (preg_match('/^' . static::$_attributeArrayLabel . '([a-z0-9\._-]*)/', $key, $attribute)) {
                if (is_array($value)) {
                    foreach ($value as $k => $v) {
                        $element->addAttribute(self::_formatName($k), self::_formatValue($v));
                    }
                } else {
                    $element->addAttribute(self::_formatName($attribute[1]), self::_formatValue($value));
                }
                continue;
            }
            $name = is_numeric($key) ? (is_array($value) ? static::$_listNode : static::$_itemNode) : $key;
            $node = self::_formatName($name);
            if (is_array($value)) {
                $child = $element->addChild($node);
                self::_addChildren($child, $value);
                continue;
            }
            $element->addChild($node, $value);
        }

        return $element;
    }

    /**
     * Returns formatted name.
     * @param string $string for format.
     * @return string formatted string.
     */
    private static function _formatName($string)
    {
        $pattern = ['/[^a-z0-9\._ -]/i' => '', '/(?=^[[0-9]\.\-\:^xml])/i' => static::$_itemNode . '_', '/ /' => '_'];

        return strtolower(preg_replace(array_keys($pattern), array_values($pattern), $string));
    }

    /**
     * Returns formatted value.
     * @param string $string for format.
     * @return string formatted string.
     */
    private static function _formatValue($string)
    {
        return is_null($string) ? '' : (is_bool($string) ? self::_bool($string) : $string);
    }

    /**
     * Returns bool in string.
     * @param bool $bool for format.
     * @return string formatted string.
     */
    private static function _bool($bool)
    {
        return $bool ? 'TRUE' : 'FALSE';
    }

    /**
     * XML to array.
     * @param SimpleXMLElement $element xml for parse.
     * @return array data.
     */
    private static function _toArray(SimpleXMLElement $element)
    {
        $array = array();
        $attributes = (array)$element->attributes();
        if (isset($attributes['@attributes'])) {
            $array[static::$_attributeArrayLabel] = $attributes['@attributes'];
        }
        foreach ($element->children() as $key => $child) {
            $value = (string)$child;
            $_children = self::_toArray($child);
            $_push = ($_hasChild = (count($_children) > 0)) ? $_children : $value;
            if ($_hasChild && !empty($value)) {
                $_push[] = $value;
            }
            $array[$key] = $_push;
        }

        return $array;
    }

    /**
     * Encode array to XML.
     * @param array $data data for encode.
     * @param string|null $root root element.
     * @return SimpleXMLElement|string
     */
    public static function encode(array $data, $root = null)
    {
        /* Get first node name & value. */
        $node = self::_formatName(is_null($root) ? static::$_rootNode : $root);
        $value = ($isArray = is_array($data)) ? null : self::_formatValue($data);

        /* Create XML document. */
        $xml = "<?xml version=\"" . static::$_xmlVersion . "\"?><{$node}>{$value}</{$node}>";
        $xml = new SimpleXMLElement($xml);

        /* Get other part of document */
        if ($isArray) {
            $xml = self::_addChildren($xml, $data);
        }

        return $xml;
    }

    /**
     * Decode xml in object or array.
     * @param string|SimpleXMLElement $xml xml for decode.
     * @param bool $asObject return as object?
     * @return array|\stdClass
     */
    public static function decode($xml, $asObject = false)
    {
        /* If not instance of SimpleXMLElement create object of SimpleXMLElement */
        !($xml instanceof SimpleXMLElement) && ($xml = new SimpleXMLElement($xml));

        $result = self::_toArray($xml);

        return $asObject ? (object)$result : $result;
    }
}
