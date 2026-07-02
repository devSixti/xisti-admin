<?php

namespace App\Support;

/**
 * Minimal PNG placeholders for QA driver documents and vehicle photos.
 */
class QaSyntheticAssets
{
    public static function ensureDocumentPng(int $documentId): string
    {
        $filename = 'qa-doc-'.$documentId.'.png';
        self::writeLabeledPng(
            public_path('assets/images/provider-documents/'.$filename),
            'Doc QA #'.$documentId,
            [104, 31, 255],
            [26, 26, 26]
        );

        return $filename;
    }

    /** @return array{vehicle_image: string, vehicle_image_front: string, vehicle_image_side: string, vehicle_image_rear: string} */
    public static function ensureVehiclePhotos(string $slug, string $label): array
    {
        $base = 'qa-vehicle-'.$slug;
        $dir = public_path('assets/images/provider-vehicle-image');
        $angles = [
            'vehicle_image_front' => $base.'-front.png',
            'vehicle_image_side' => $base.'-side.png',
            'vehicle_image_rear' => $base.'-rear.png',
        ];
        $colors = [
            [128, 255, 0],
            [104, 31, 255],
            [116, 214, 3],
        ];
        $i = 0;
        foreach ($angles as $field => $filename) {
            $palette = $colors[$i % 3];
            self::writeLabeledPng(
                $dir.'/'.$filename,
                $label,
                $palette,
                [26, 26, 26],
                strtoupper(str_replace('_', ' ', str_replace('vehicle_image_', '', $field)))
            );
            ++$i;
        }

        return [
            'vehicle_image' => $angles['vehicle_image_front'],
            'vehicle_image_front' => $angles['vehicle_image_front'],
            'vehicle_image_side' => $angles['vehicle_image_side'],
            'vehicle_image_rear' => $angles['vehicle_image_rear'],
        ];
    }

    public static function ensureAvatarPng(string $slug, string $initials): string
    {
        $filename = 'qa-avatar-'.$slug.'.png';
        self::writeLabeledPng(
            public_path('assets/images/profile-image/'.$filename),
            $initials,
            [128, 255, 0],
            [26, 26, 26],
            'QA',
            256,
            256
        );

        return $filename;
    }

    private static function writeLabeledPng(
        string $absolutePath,
        string $title,
        array $accentRgb,
        array $bgRgb,
        string $subtitle = '',
        int $width = 640,
        int $height = 480
    ): void {
        $dir = dirname($absolutePath);
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        if (is_file($absolutePath)) {
            return;
        }

        if (! function_exists('imagecreatetruecolor')) {
            file_put_contents($absolutePath, base64_decode(
                'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg=='
            ));

            return;
        }

        $img = imagecreatetruecolor($width, $height);
        $bg = imagecolorallocate($img, $bgRgb[0], $bgRgb[1], $bgRgb[2]);
        $accent = imagecolorallocate($img, $accentRgb[0], $accentRgb[1], $accentRgb[2]);
        $white = imagecolorallocate($img, 255, 255, 255);
        imagefilledrectangle($img, 0, 0, $width, $height, $bg);
        imagefilledrectangle($img, 24, 24, $width - 24, $height - 24, $accent);
        imagefilledrectangle($img, 36, 36, $width - 36, $height - 36, $bg);

        $titleSize = 5;
        $titleX = (int) max(12, ($width - (strlen($title) * imagefontwidth($titleSize))) / 2);
        imagestring($img, $titleSize, $titleX, (int) ($height / 2) - 24, $title, $white);
        if ($subtitle !== '') {
            $subSize = 4;
            $subX = (int) max(12, ($width - (strlen($subtitle) * imagefontwidth($subSize))) / 2);
            imagestring($img, $subSize, $subX, (int) ($height / 2) + 8, $subtitle, $white);
        }

        imagepng($img, $absolutePath);
        imagedestroy($img);
    }
}
