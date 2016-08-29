<?php

namespace s10Core\MyFile;

/**
 * @todo Generate XML From Array
 * @version 1.2
 * 
 * @author Tien Giang <nguyentiengiang@outlook.com> Phone: +84 1282 303 100
 * @copyright (c) 2016, Tien Giang
 */
class XML {

    function generate_xml_from_array($array, $node_name) {
        $xml = '';
        if (is_array($array) || is_object($array)) {
            foreach ($array as $key => $value) {
                if (is_numeric($key)) {
                    $key = $node_name;
                }
                $xml .= '<' . $key . '>' . self::generate_xml_from_array($value, $node_name) . '</' . $key . '>';
            }
        } else {
            $xml = htmlspecialchars($array, ENT_QUOTES);
        }
        return $xml;
    }

    static function generate_valid_xml_from_array($array, $node_block = 'nodes', $node_name = 'node') {
        $xml = '<?xml version="1.0" encoding="UTF-8" ?>';
        $xml .= '<' . $node_block . '>';
        $xml .= self::generate_xml_from_array($array, $node_name);
        $xml .= '</' . $node_block . '>';
        return $xml;
    }
}
