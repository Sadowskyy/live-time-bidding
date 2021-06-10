<?php


namespace App\Service;


class FileValidator
{

    private const AVAILABLE_EXTENSIONS = array('gif', 'jpg', 'jpeg', 'png');

    const KB = 1024;
    const MB = 1048576;
    const GB = 1073741824;
    const TB = 1099511627776;

    public function isValid(string $extension, int $filesize, string $filename, string $uploadDirectory)
    {
        if (false === in_array($extension, self::AVAILABLE_EXTENSIONS)) {
            return false;
        }
        if (true === file_exists($uploadDirectory . $filename)) {
            return false;
        }
        if ($filesize > 5 * self::MB || $filesize <= 0) {
            return false;
        }

        return true;
    }

    public function fileExists(string $directory, string $filepath)
    {
        return is_file($directory . DIRECTORY_SEPARATOR . $filepath);
    }
}