<?php

namespace App\Http\Controllers;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;


class QrCodeController extends Controller
{
    public function generate(Request $request): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
    {
        $request->validate([
            'text' => 'nullable|string',
            'email' => 'nullable|string|email',
            'geo' => 'nullable|array',
            'geo.latitude' => 'required_with:geo|numeric|between:-90,90',
            'geo.longitude' => 'required_with:geo|numeric|between:-180,180',
            'phone_number' => 'nullable|string',
            'wifi' => 'nullable|array',
            'wifi.encryption' => 'required_with:wifi|string|in:WPA,WEP,none',
            'wifi.ssid' => 'required_with:wifi|string',
            'wifi.password' => 'nullable|string',
            'wifi.hidden' => 'nullable|boolean',
            'color' => 'nullable|string',
            'type' => 'required|string|in:svg,png',
            'subtitle' => 'nullable|string',
            'style' => 'nullable|string|in:square,dot,round',
            'eye' => 'nullable|string|in:square,circle',
        ]);

        $colorKey = $request->input('color', 'black');
        $backgroundColorKey = $request->input('background_color', 'white');
        $type = $request->input('type', 'svg');
        $style = $request->input('style', 'round');
        $eye = $request->input('eye', 'square');

        //GET COLOR AND BACKGROUND COLOR FROM THE RGB CODE
        $color = $this->rgbColor($colorKey);
        $backgroundColor = $this->rgbColor($backgroundColorKey);

        //TODO: ADD SUBTITLE TO THE IMAGE/SVG GENERATED
        $subtitle = $request->input('subtitle', 'SCAN ME');


        if ($request->filled('wifi')) {
            $wifi = $request->input('wifi');
            $hidden = $wifi['hidden'] ? 'true' : 'false';
            $qrcodeData = "WIFI:S:{$wifi['ssid']};T:{$wifi['encryption']};P:{$wifi['password']};H:{$hidden};";
        } elseif ($request->filled('phone_number')) {
            $qrcodeData = "tel:{$request->input('phone_number')}";
        } elseif ($request->filled('geo')) {
            $geo = $request->input('geo');
            $qrcodeData = "geo:{$geo['latitude']},{$geo['longitude']}";
        } elseif ($request->filled('email')) {
            $email = $request->input('email');
            $qrcodeData = "mailto:{$email}";
        } elseif ($request->filled('text')) {
            $qrcodeData = $request->input('text');
        }


        $qrCode = QrCode::size(300)
            ->style($style)
            ->eye($eye)
            ->color(...$color)
            ->margin(2)
            ->errorCorrection('M')
            ->backgroundColor(...$backgroundColor)
            ->format($type)
            ->encoding('UTF-8')
            ->generate($qrcodeData ?? 'THIS IS A QRCODE');

        //JUST FOR PNG
        if ($type === 'png') {
            $base64QrCode = base64_encode($qrCode);
            $manager = new ImageManager(new Driver());
            $qrImage = $manager->read($base64QrCode);
            $background = $manager->create(600, 600)->fill('fff');
            $background->place($qrImage, 'center');
            $encodedImage = $background->encodeByMediaType('image/png');
            // Return the final image with correct headers
            return response($encodedImage)->header('Content-Type', 'image/png');
        }
        //FOR SVG
        $qrCodeSvg = trim($qrCode);
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

        // Return the complete SVG
        return response($finalSvg)->header('Content-Type', 'image/svg+xml');
    }

    private function rgbColor($color)
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
