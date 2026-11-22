<?php

namespace App\Helpers;

use DOMDocument;
use DOMElement;

class XmlHelper
{
    /**
     * Membuat response XML secara dinamis dari array PHP apa pun.
     * Semua controller & exception handler memakai fungsi ini,
     * sehingga seluruh endpoint selalu menghasilkan XML.
     */
    public static function response($data = null, $status = 200,
                                    $message = 'OK', $errors = null)
    {
        $payload = [
            'success' => $status >= 200 && $status < 300,
            'status'  => $status,
            'message' => $message,
        ];

        if ($data !== null) {
            $payload['data'] = $data;
        }
        if ($errors !== null) {
            $payload['errors'] = $errors;
        }

        return response(self::toXml($payload), $status)
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    public static function toXml(array $payload, $root = 'response')
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;

        $rootElement = $dom->createElement($root);
        $dom->appendChild($rootElement);

        self::build($dom, $rootElement, $payload);

        return $dom->saveXML();
    }

    private static function build(DOMDocument $dom, DOMElement $parent, array $data)
    {
        foreach ($data as $key => $value) {
            $name = is_int($key) ? 'item' : $key;

            // array berurutan (list) -> elemen diulang dengan nama key yang sama
            if (is_array($value) && self::isList($value)) {
                foreach ($value as $item) {
                    self::append($dom, $parent, $name, $item);
                }
                continue;
            }

            self::append($dom, $parent, $name, $value);
        }
    }

    private static function append(DOMDocument $dom, DOMElement $parent, $name, $value)
    {
        $element = $dom->createElement($name);

        if (is_array($value)) {
            self::build($dom, $element, $value);
        } else {
            if (is_bool($value)) {
                $value = $value ? 'true' : 'false';
            }
            // createTextNode otomatis meng-escape karakter khusus (& < >)
            $element->appendChild($dom->createTextNode((string) $value));
        }

        $parent->appendChild($element);
    }

    private static function isList(array $array)
    {
        return $array === [] || array_keys($array) === range(0, count($array) - 1);
    }

    // Format waktu wajib: 05-11-2018 08:40:30
    public static function formatTime($time)
    {
        return $time ? $time->format('d-m-Y H:i:s') : null;
    }
}
