<?php

namespace Miqoo1996\GDrive\Services;

class MimeTypesService
{
    public static $mimeTypes = [
        "xls" => 'application/vnd.ms-excel',
        "xlsx" => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        "xml" => 'text/xml',
        "ods" => 'application/vnd.oasis.opendocument.spreadsheet',
        "csv" => 'text/plain',
        "tmpl" => 'text/plain',
        "pdf" => 'application/pdf',
        "php" => 'application/x-httpd-php',
        "jpg" => 'image/jpeg',
        "png" => 'image/png',
        "gif" => 'image/gif',
        "bmp" => 'image/bmp',
        "txt" => 'text/plain',
        "doc" => 'application/msword',
        "js" => 'text/js',
        "swf" => 'application/x-shockwave-flash',
        "mp3" => 'audio/mpeg',
        "zip" => 'application/zip',
        "rar" => 'application/rar',
        "tar" => 'application/tar',
        "arj" => 'application/arj',
        "cab" => 'application/cab',
        "html" => 'text/html',
        "htm" => 'text/html',
        "default" => 'application/octet-stream',
        "folder" => 'application/vnd.google-apps.folder'
    ];

    public static function convert($type, $flip = false)
    {
        if ($flip === true) {
            return array_flip(self::$mimeTypes)[$type];
        }

        return self::$mimeTypes[$type];
    }
}