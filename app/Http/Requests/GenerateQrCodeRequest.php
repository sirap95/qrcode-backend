<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GenerateQrCodeRequest extends FormRequest
{
    public function rules()
    {
        return [
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
            'background_color' => 'nullable|string',
            'type' => 'required|string|in:svg,png',
            'subtitle' => 'nullable|string',
            'style' => 'nullable|string|in:square,dot,round',
            'eye' => 'nullable|string|in:square,circle',
        ];
    }
}
