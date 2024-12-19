<?php

namespace App\Service;

class FileManager
{

    public function deleteFolder(string $folderPath): bool
    {
        if (!is_dir($folderPath)) {
            return false;
        }

        $files = array_diff(scandir($folderPath), ['.', '..']);
        foreach ($files as $file) {
            $filePath = $folderPath . DIRECTORY_SEPARATOR . $file;
            if (is_dir($filePath)) {
                $this->deleteFolder($filePath);
            } else {
                unlink($filePath);
            }
        }

        return rmdir($folderPath);
    }

    public function deleteFile(string $filePath): bool
    {
        if (!is_file($filePath)) {
            return false;
        }
        return unlink($filePath);
    }

    public function createDirectory(string $path): void {
        if (!file_exists($path) && !mkdir($path, 0777, true) && !is_dir($path)) {
            throw new \RuntimeException(sprintf('Le répertoire "%s" n\'a pas pu être créé.', $path));
        }
    }

    public function isWritable(string $path): void {
        if (!is_writable($path)) {
            throw new \RuntimeException(sprintf('Le répertoire "%s" n\'est pas accessible en écriture.', $path));
        }
    }
}
