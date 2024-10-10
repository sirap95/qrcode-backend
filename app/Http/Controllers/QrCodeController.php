<?php

namespace App\Http\Controllers;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Http\Request;
use Intervention\Image\Geometry\Factories\CircleFactory;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

// Ensure this is imported

class QrCodeController extends Controller
{
    public function generate(Request $request): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
    {
        $request->validate([
            'text' => 'nullable|string',
            'email' => 'nullable|string|email',
            'geo' => 'nullable|array',
            'geo.latitude' => 'required_with:geo|numeric|between:-90,90', // Latitude should be a valid number between -90 and 90
            'geo.longitude' => 'required_with:geo|numeric|between:-180,180', // Longitude should be a valid number between -180 and 180
            'phone_number' => 'nullable|string',
            'wifi' => 'nullable|array',
            'wifi.encryption' => 'required_with:wifi|string|in:WPA,WEP,none', // Restrict the encryption types to valid values
            'wifi.ssid' => 'required_with:wifi|string',
            'wifi.password' => 'nullable|string', // Password can be nullable if not required
            'wifi.hidden' => 'nullable|boolean',  // Hidden should be true/false
            'color' => 'nullable|string',
            'type' => 'required|string|in:svg,png',
            'subtitle' => 'nullable|string'
        ]);

        $colorKey = $request->input('color', 'black'); // Default color to black
        $backgroundColorKey = $request->input('background_color', 'white'); // Default background color to white
        $type = $request->input('type', 'svg'); // Default type is SVG
        $subtitle = $request->input('subtitle', 'SCAN ME'); // Default subtitle

        $color = $this->rgbColor($colorKey);
        $backgroundColor = $this->rgbColor($backgroundColorKey);

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
            $manager = new ImageManager(new Driver()); // Specify the driver
            $qrImage = $manager->read($base64QrCode);

            // Create a rounded background with the same dimensions
            $background = $manager->create(600, 600)->fill('fff'); // White background
            $background->drawCircle(20, 20, null);

            // Overlay the QR code on the rounded background
            $background->place($qrImage, 'center');

            $encodedImage = $background->encodeByMediaType('image/png');

            // Return the final image with correct headers
            return response($encodedImage)->header('Content-Type', 'image/png');
        }
        //FOR SVG
        return response($qrCode)->header('Content-Type', 'image/svg+xml');
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
