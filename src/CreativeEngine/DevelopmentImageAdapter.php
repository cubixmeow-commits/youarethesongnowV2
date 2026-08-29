<?php

declare(strict_types=1);

namespace Yatsn\CreativeEngine;

use Yatsn\AI\ImageAdapterInterface;

/**
 * Deterministic local image adapter. Identifies itself in pixels only when text is allowed.
 */
final class DevelopmentImageAdapter implements ImageAdapterInterface
{
    public function name(): string
    {
        return 'deterministic-development-image';
    }

    public function isAvailable(): bool
    {
        return true;
    }

    public function generate(array $package, array $snapshot): array
    {
        $orientation = (string) ($snapshot['orientation'] ?? 'square');
        [$w, $h] = match ($orientation) {
            'portrait' => [768, 1024],
            'landscape' => [1024, 768],
            default => [1024, 1024],
        };

        $img = imagecreatetruecolor($w, $h);
        $ink = imagecolorallocate($img, 18, 16, 18);
        $paper = imagecolorallocate($img, 236, 228, 214);
        $muted = imagecolorallocate($img, 170, 160, 148);
        $stage = imagecolorallocate($img, 196, 78, 54);
        imagefilledrectangle($img, 0, 0, $w, $h, $ink);

        for ($i = 0; $i < 8; $i++) {
            $c = imagecolorallocatealpha($img, 196, 78, 54, 110 - ($i * 8));
            imagefilledellipse($img, (int) ($w * 0.65), (int) ($h * 0.35), (int) ($w * 0.7) + $i * 20, (int) ($h * 0.55) + $i * 20, $c);
        }

        $noText = !empty($snapshot['noTextInImage']);
        $style = (string) ($snapshot['styleName'] ?? 'Style');

        if (!$noText) {
            $y = (int) ($h * 0.72);
            imagestring($img, 5, 40, $y, 'DEVELOPMENT IMAGE', $stage);
            imagestring($img, 3, 40, $y + 24, 'adapter: deterministic-development', $muted);
            imagestring($img, 3, 40, $y + 42, 'style: ' . substr($style, 0, 40), $paper);
            imagestring($img, 3, 40, $y + 60, 'inspired by themes of a song', $muted);
        }

        $count = max(1, min(2, (int) ($snapshot['portraitCount'] ?? 1)));
        for ($i = 0; $i < $count; $i++) {
            $cx = (int) ($w * (0.35 + $i * 0.22));
            $cy = (int) ($h * 0.42);
            imagefilledellipse($img, $cx, $cy, 120, 160, $paper);
            imagefilledellipse($img, $cx, $cy - 10, 70, 80, $ink);
        }

        ob_start();
        imagejpeg($img, null, 90);
        $bytes = (string) ob_get_clean();

        return [
            'adapter' => $this->name(),
            'mime' => 'image/jpeg',
            'width' => $w,
            'height' => $h,
            'bytes' => $bytes,
            'costCents' => 0,
        ];
    }
}
