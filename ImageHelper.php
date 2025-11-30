<?php

function getCacheDir(): string {
    $cacheDir = __DIR__ . "/data/cache";

    if (!is_dir($cacheDir)) {
        mkdir($cacheDir, 0755, true);
    }

    if (!is_writable($cacheDir)) {
        error_log("WARNING: the cache directory is not writable: $cacheDir");
    }

    return $cacheDir;
}

function generateThumbnail(string $originalPath, string $cachePath): void {
    if (!file_exists($cachePath)) {
        $src = @imagecreatefromjpeg($originalPath);
        if ($src) {
            $origWidth = imagesx($src);
            $origHeight = imagesy($src);

            $newWidth = 100;
            $newHeight = intval(($origHeight / $origWidth) * $newWidth);

            $tmp = imagecreatetruecolor($newWidth, $newHeight);

            imagecopyresampled(
                $tmp, $src,
                0, 0, 0, 0,
                $newWidth, $newHeight,
                $origWidth, $origHeight
            );

            imagejpeg($tmp, $cachePath, 85);
        }
    }
}
