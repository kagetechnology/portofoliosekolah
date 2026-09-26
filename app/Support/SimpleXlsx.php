<?php

namespace App\Support;

use DOMDocument;
use DOMElement;
use DOMXPath;
use RuntimeException;
use ZipArchive;

class SimpleXlsx
{
    public static function rows(string $path): array
    {
        $zip = new ZipArchive;
        if ($zip->open($path) !== true) {
            throw new RuntimeException('File XLSX tidak dapat dibuka.');
        }

        try {
            $strings = self::sharedStrings($zip);
            $sheet = $zip->getFromName('xl/worksheets/sheet1.xml');
            if ($sheet === false) {
                throw new RuntimeException('Sheet pertama tidak ditemukan.');
            }

            $document = new DOMDocument;
            $document->loadXML($sheet, LIBXML_NONET);
            $xpath = new DOMXPath($document);
            $xpath->registerNamespace('x', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');

            $rows = [];
            foreach ($xpath->query('//x:sheetData/x:row') as $row) {
                $values = [];
                foreach ($xpath->query('./x:c', $row) as $cell) {
                    if (! $cell instanceof DOMElement) {
                        continue;
                    }
                    preg_match('/^[A-Z]+/', $cell->getAttribute('r'), $match);
                    $column = $match[0] ?? '';
                    $type = $cell->getAttribute('t');
                    $value = $type === 'inlineStr'
                        ? $xpath->query('./x:is/x:t', $cell)->item(0)?->textContent ?? ''
                        : $xpath->query('./x:v', $cell)->item(0)?->textContent ?? '';
                    if ($type === 's') {
                        $value = $strings[(int) $value] ?? '';
                    }
                    $values[$column] = trim($value);
                }
                $rows[] = $values;
            }

            return $rows;
        } finally {
            $zip->close();
        }
    }

    private static function sharedStrings(ZipArchive $zip): array
    {
        $xml = $zip->getFromName('xl/sharedStrings.xml');
        if ($xml === false) {
            return [];
        }

        $document = new DOMDocument;
        $document->loadXML($xml, LIBXML_NONET);
        $xpath = new DOMXPath($document);
        $xpath->registerNamespace('x', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');

        $strings = [];
        foreach ($xpath->query('//x:si') as $item) {
            $value = '';
            foreach ($xpath->query('.//x:t', $item) as $text) {
                $value .= $text->textContent;
            }
            $strings[] = $value;
        }

        return $strings;
    }
}
