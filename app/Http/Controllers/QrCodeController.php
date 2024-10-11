<?php

namespace App\Http\Controllers;

use App\Http\Requests\GenerateQrCodeRequest;
use App\Services\QrCodeService;

class QrCodeController extends Controller
{
    protected $qrCodeService;

    public function __construct(QrCodeService $qrCodeService)
    {
        $this->qrCodeService = $qrCodeService;
    }

    public function generate(GenerateQrCodeRequest $request)
    {
        $colorKey = $request->input('color', 'black');
        $backgroundColorKey = $request->input('background_color', 'white');

        // Get color and background color from RGB code
        $color = $this->rgbColor($colorKey);
        $backgroundColor = $this->rgbColor($backgroundColorKey);

        // Prepare data for QR code
        $qrcodeData = $this->getQrCodeData($request);

        // Generate QR code
        $qrCode = $this->qrCodeService->generateQrCode($request->all(), $color, $backgroundColor, $qrcodeData);

        $subtitle = $request->input('subtitle', 'SCAN ME');
        if ($request->input('type') === 'png') {
            $encodedImage = $this->qrCodeService->processPng($qrCode, $colorKey, $subtitle, $backgroundColorKey);
            return response($encodedImage)->header('Content-Type', 'image/png');
        }


        $finalSvg = $this->qrCodeService->generateSvg($qrCode, $subtitle, $colorKey, $backgroundColorKey);

        return response($finalSvg)->header('Content-Type', 'image/svg+xml');
    }

    private function getQrCodeData($request)
    {
        if ($request->filled('wifi')) {
            $wifi = $request->input('wifi');
            $hidden = $wifi['hidden'] ? 'true' : 'false';
            return "WIFI:S:{$wifi['ssid']};T:{$wifi['encryption']};P:{$wifi['password']};H:{$hidden};";
        } elseif ($request->filled('phone_number')) {
            return "tel:{$request->input('phone_number')}";
        } elseif ($request->filled('geo')) {
            $geo = $request->input('geo');
            return "geo:{$geo['latitude']},{$geo['longitude']}";
        } elseif ($request->filled('email')) {
            return "mailto:{$request->input('email')}";
        } elseif ($request->filled('text')) {
            return $request->input('text');
        }
    }

    private function rgbColor($color): array
    {
        $colors = [
            'black' => [0, 0, 0],
            'red' => [255, 0, 0],
            'green' => [0, 255, 0],
            'blue' => [0, 0, 255],
            'yellow' => [255, 255, 0],
            'cyan' => [0, 255, 255],
            'magenta' => [255, 0, 255],
            'white' => [255, 255, 255],
            'orange' => [255, 165, 0],
            'purple' => [128, 0, 128],
            'pink' => [255, 192, 203],
            'brown' => [165, 42, 42],
            'gray' => [128, 128, 128],
            'light_gray' => [211, 211, 211],
            'dark_gray' => [169, 169, 169],
            'navy' => [0, 0, 128],
            'olive' => [128, 128, 0],
            'teal' => [0, 128, 128]
        ];

        return $colors[$color] ?? $colors['black'];
    }

}
