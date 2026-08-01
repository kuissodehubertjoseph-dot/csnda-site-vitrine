<?php

namespace Database\Seeders\Support;

use Illuminate\Support\Facades\Storage;

class PlaceholderImage
{
    private const PALETTE = [
        [94, 179, 228],  // ciel
        [76, 175, 109],  // laurier
        [242, 166, 176], // saumon
    ];

    public static function make(string $directory, string $label): string
    {
        [$r, $g, $b] = self::PALETTE[array_rand(self::PALETTE)];

        $width = 800;
        $height = 500;
        $image = imagecreatetruecolor($width, $height);

        $bg = imagecolorallocate($image, $r, $g, $b);
        imagefill($image, 0, 0, $bg);

        $white = imagecolorallocate($image, 255, 255, 255);
        $font = 5;
        $text = $label;
        $textWidth = imagefontwidth($font) * strlen($text);
        $x = (int) (($width - $textWidth) / 2);
        $y = (int) ($height / 2) - 10;
        imagestring($image, $font, $x, $y, $text, $white);
        imagestring($image, 3, (int) ($width / 2) - 60, $y + 30, 'CSNDA Cotonou', $white);

        $path = $directory.'/'.uniqid('demo_', true).'.png';

        ob_start();
        imagepng($image);
        $content = ob_get_clean();
        imagedestroy($image);

        Storage::disk('public')->put($path, $content);

        return $path;
    }
}
