<?php
namespace ctur\xml;

use DOMDocument, SimpleXMLElement;

class Formatter
{
    public static function toXml(array $data, $ownerTag)
    {
        $xml = new SimpleXMLElement("<?xml version=\"1.0\"?><{$ownerTag}></{$ownerTag}>");

        self::_itemToXml($data, $xml);

        return $xml;
    }

    private static function _itemToXml(array $data, SimpleXMLElement &$xml)
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                if (!is_numeric($key)) {
                    $subItem = $xml->addChild("{$key}");
                    self::_itemToXml($value, $subItem);
                } else {
                    $subItem = $xml->addChild("item{$key}");
                    self::_itemToXml($value, $subItem);
                }
            } else {
                $xml->addChild("{$key}", htmlspecialchars("{$value}"));
            }
        }
    }

    public static function toObject($xml)
    {
        return (object)self::toArray($xml);
    }

    public static function toArray($xml)
    {
        $doc = new \DOMDocument();
        $doc->loadXML($xml);
        $root = $doc->documentElement;
        $output = self::_itemToArray($root);
        $output['@root'] = $root->tagName;

        return $output;
    }

    private static function _itemToArray($node)
    {
        $output = [];
        switch ($node->nodeType) {
            case XML_CDATA_SECTION_NODE:
            case XML_TEXT_NODE:
                $output = trim($node->textContent);
                break;
            case XML_ELEMENT_NODE:
                for ($i = 0, $m = $node->childNodes->length; $i < $m; $i++) {
                    $child = $node->childNodes->item($i);
                    $v = self::_itemToArray($child);
                    if (isset($child->tagName)) {
                        $t = $child->tagNameF;
                        if (!isset($output[$t])) {
                            $output[$t] = [];
                        }
                        $output[$t][] = $v;
                    } elseif ($v || $v === '0') {
                        $output = (string)$v;
                    }
                }
                if ($node->attributes->length && !is_array($output)) { //Has attributes but isn't an array
                    $output = ['@content' => $output]; //Change output into an array.
                }
                if (is_array($output)) {
                    if ($node->attributes->length) {
                        $a = [];
                        foreach ($node->attributes as $attrName => $attrNode) {
                            $a[$attrName] = (string)$attrNode->value;
                        }
                        $output['@attributes'] = $a;
                    }
                    foreach ($output as $t => $v) {
                        if (is_array($v) && count($v) == 1 && $t != '@attributes') {
                            $output[$t] = $v[0];
                        }
                    }
                }
                break;
        }

        return $output;
    }
}
