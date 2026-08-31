#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Build V1 showcase assets: copy originals, generate WebP derivatives, write manifest.
 *
 * Usage: php bin/build-v1-showcase.php /path/to/v1/sample_images
 */

if ($argc < 2) {
    fwrite(STDERR, "Usage: php bin/build-v1-showcase.php <source-directory>\n");
    exit(1);
}

$sourceDir = rtrim($argv[1], '/');
if (!is_dir($sourceDir)) {
    fwrite(STDERR, "Source directory not found: {$sourceDir}\n");
    exit(1);
}

$root = dirname(__DIR__);
$altPath = $root . '/bin/v1-showcase-alt.json';
if (!is_file($altPath)) {
    fwrite(STDERR, "Alt metadata file missing: {$altPath}\n");
    exit(1);
}

$altRows = json_decode((string) file_get_contents($altPath), true, 512, JSON_THROW_ON_ERROR);
$altByFile = [];
foreach ($altRows as $row) {
    $altByFile[$row['sourceFilename']] = $row;
}

$patterns = ['*.jpg', '*.jpeg', '*.png', '*.JPG', '*.JPEG', '*.PNG'];
$files = [];
foreach ($patterns as $pattern) {
    foreach (glob($sourceDir . '/' . $pattern) ?: [] as $file) {
        $files[] = $file;
    }
}
$files = array_values(array_unique($files));
sort($files, SORT_STRING);

if (count($files) !== 77) {
    fwrite(STDERR, 'Expected exactly 77 image files, found ' . count($files) . "\n");
    exit(1);
}

$outOriginals = $root . '/public/assets/images/showcase/originals';
$outDisplay = $root . '/public/assets/images/showcase/display';
$outThumbs = $root . '/public/assets/images/showcase/thumbs';
$manifestPath = $root . '/public/assets/data/v1-showcase.json';

foreach ([$outOriginals, $outDisplay, $outThumbs, dirname($manifestPath)] as $dir) {
    if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
        fwrite(STDERR, "Failed to create directory: {$dir}\n");
        exit(1);
    }
}

function classifyOrientation(int $w, int $h): string
{
    if ($w > (int) round($h * 1.02)) {
        return 'landscape';
    }
    if ($h > (int) round($w * 1.02)) {
        return 'portrait';
    }

    return 'square';
}

function loadImage(string $path)
{
    $info = getimagesize($path);
    if ($info === false) {
        throw new RuntimeException("Unreadable image: {$path}");
    }
    [$w, $h, $type] = $info;
    $image = match ($type) {
        IMAGETYPE_JPEG => imagecreatefromjpeg($path),
        IMAGETYPE_PNG => imagecreatefrompng($path),
        default => false,
    };
    if ($image === false) {
        throw new RuntimeException("Failed to decode image: {$path}");
    }

    return [$image, $w, $h];
}

function resizeToMax($src, int $srcW, int $srcH, int $maxDim): array
{
    $scale = min(1.0, $maxDim / max($srcW, $srcH));
    $dstW = max(1, (int) round($srcW * $scale));
    $dstH = max(1, (int) round($srcH * $scale));
    $dst = imagecreatetruecolor($dstW, $dstH);
    imagealphablending($dst, false);
    imagesavealpha($dst, true);
    $transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
    imagefilledrectangle($dst, 0, 0, $dstW, $dstH, $transparent);
    imagecopyresampled($dst, $src, 0, 0, 0, 0, $dstW, $dstH, $srcW, $srcH);

    return [$dst, $dstW, $dstH];
}

function saveWebp($image, string $path, int $quality): void
{
    if (!imagewebp($image, $path, $quality)) {
        throw new RuntimeException("Failed to write WebP: {$path}");
    }
}

function stripMetadataCopy(string $source, string $dest): array
{
    [$image, $w, $h] = loadImage($source);
    $ext = strtolower(pathinfo($source, PATHINFO_EXTENSION));
    $ok = match ($ext) {
        'jpg', 'jpeg' => imagejpeg($image, $dest, 92),
        'png' => imagepng($image, $dest, 6),
        default => false,
    };
    imagedestroy($image);
    if (!$ok) {
        throw new RuntimeException("Failed to copy original: {$dest}");
    }

    return [$w, $h];
}

$manifest = [
    'version' => 1,
    'generatedAt' => gmdate('c'),
    'total' => 77,
    'orientations' => ['portrait' => 0, 'square' => 0, 'landscape' => 0],
    'items' => [],
];

$seenIds = [];
$seenPaths = [];
$originalBytes = 0;
$thumbBytes = 0;
$displayBytes = 0;
$featuredOrder = 0;

foreach ($files as $index => $sourcePath) {
    $basename = basename($sourcePath);
    if (!isset($altByFile[$basename])) {
        fwrite(STDERR, "Missing alt metadata for: {$basename}\n");
        exit(1);
    }
    $meta = $altByFile[$basename];
    $id = $meta['id'];
    if (isset($seenIds[$id])) {
        fwrite(STDERR, "Duplicate id: {$id}\n");
        exit(1);
    }
    $seenIds[$id] = true;

    $originalDest = $outOriginals . '/' . $basename;
    [$width, $height] = stripMetadataCopy($sourcePath, $originalDest);
    $originalBytes += filesize($originalDest);

    [$srcImage] = loadImage($originalDest);
    $orientation = classifyOrientation($width, $height);
    $manifest['orientations'][$orientation]++;

    $thumbPath = $outThumbs . '/' . $id . '.webp';
    [$thumbImage, $thumbW, $thumbH] = resizeToMax($srcImage, $width, $height, 560);
    saveWebp($thumbImage, $thumbPath, 80);
    imagedestroy($thumbImage);
    $thumbBytes += filesize($thumbPath);

    $displayPath = $outDisplay . '/' . $id . '.webp';
    [$displayImage, $displayW, $displayH] = resizeToMax($srcImage, $width, $height, 1600);
    saveWebp($displayImage, $displayPath, 86);
    imagedestroy($displayImage);
    imagedestroy($srcImage);
    $displayBytes += filesize($displayPath);

    $entry = [
        'id' => $id,
        'sourceFilename' => $basename,
        'original' => '/assets/images/showcase/originals/' . $basename,
        'display' => '/assets/images/showcase/display/' . $id . '.webp',
        'thumb' => '/assets/images/showcase/thumbs/' . $id . '.webp',
        'width' => $width,
        'height' => $height,
        'orientation' => $orientation,
        'alt' => $meta['alt'],
        'featured' => !empty($meta['featured']),
    ];
    if ($entry['featured']) {
        $featuredOrder++;
        $entry['featuredOrder'] = $featuredOrder;
    }

    foreach (['original', 'display', 'thumb'] as $key) {
        if (isset($seenPaths[$entry[$key]])) {
            fwrite(STDERR, "Duplicate path: {$entry[$key]}\n");
            exit(1);
        }
        $seenPaths[$entry[$key]] = true;
        if (!str_starts_with($entry[$key], '/assets/images/showcase/')) {
            fwrite(STDERR, "Path outside showcase root: {$entry[$key]}\n");
            exit(1);
        }
    }

    $manifest['items'][] = $entry;
}

if ($manifest['orientations']['portrait'] !== 32
    || $manifest['orientations']['square'] !== 33
    || $manifest['orientations']['landscape'] !== 12) {
    fwrite(STDERR, 'Orientation counts mismatch: ' . json_encode($manifest['orientations']) . "\n");
    exit(1);
}

$encoded = json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
if ($encoded === false || file_put_contents($manifestPath, $encoded . "\n") === false) {
    fwrite(STDERR, "Failed to write manifest: {$manifestPath}\n");
    exit(1);
}

echo "V1 showcase build complete\n";
echo "Items: " . count($manifest['items']) . "\n";
echo 'Orientations: ' . json_encode($manifest['orientations']) . "\n";
echo 'Originals: ' . round($originalBytes / 1024 / 1024, 2) . " MB\n";
echo 'Thumbs: ' . round($thumbBytes / 1024 / 1024, 2) . " MB\n";
echo 'Display: ' . round($displayBytes / 1024 / 1024, 2) . " MB\n";
echo "Manifest: {$manifestPath}\n";
