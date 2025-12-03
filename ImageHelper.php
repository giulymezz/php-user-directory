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
    $fullOriginalPath = __DIR__ . '/' . ltrim($originalPath, '/');

    if (!file_exists($fullOriginalPath)) {
        error_log("Original image not found: $fullOriginalPath");
        return;
    }
    
    if (!file_exists($cachePath)) {
        $src = @imagecreatefromjpeg($fullOriginalPath);

        if ($src === false) {
            error_log("Failed to load image: $fullOriginalPath");
            return;
        }

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
