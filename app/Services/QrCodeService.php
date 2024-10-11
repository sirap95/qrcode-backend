<?php

namespace App\Services;

use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Typography\FontFactory;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Intervention\Image\ImageManager;

class QrCodeService
{
    public function generateQrCode($requestData, $color, $backgroundColor, $qrcodeData)
    {
        $qrCode = QrCode::size(300)
            ->style($requestData['style'])
            ->eye($requestData['eye'])
            ->color(...$color)
            ->margin(2)
            ->errorCorrection('M')
            ->backgroundColor(...$backgroundColor)
            ->format($requestData['type'])
            ->encoding('UTF-8')
            ->generate($qrcodeData ?? 'THIS IS A QRCODE');

        return $qrCode;
    }

    public function processPng($qrCode, $colorKey, $subtitle, $backgroundColorKey)
    {

        $base64QrCode = base64_encode($qrCode);
        $manager = new ImageManager(new Driver());
        $qrImage = $manager->read($base64QrCode);
        $background = $manager->create(300, 400)->fill($backgroundColorKey);
        $background->place($qrImage, 'center', 0, -25);

        $background->text($subtitle, 150, 360, function (FontFactory $font) use ($colorKey) {

            $fontPath = public_path('fonts/Rubik-Regular.ttf');

            $font->file($fontPath);
            $font->size(35);
            $font->color($colorKey);
            $font->align('center');
        });

        return $background->encodeByMediaType('image/png');
    }

    public function generateSvg($qrCodeSvg, $subtitle, $colorKey, $backgroundColorKey)
    {
        $qrCodeSvg = trim($qrCodeSvg);
        $qrCodeSvg = str_replace('<?xml version="1.0" encoding="UTF-8"?>', '', $qrCodeSvg);

        $finalSvg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="300" height="350">
    <rect width="300" height="400" fill="$backgroundColorKey" />
    <g>
        $qrCodeSvg
        <text x="150" y="340" font-size="30" text-anchor="middle" fill="$colorKey">$subtitle</text>
    </g>
</svg>
SVG;

        return $finalSvg;
    }
}
