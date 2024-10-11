# QR Code Generator

A Laravel-based application to generate customizable QR codes. This project allows you to create QR codes with different formats, colors, content types (text, email, geo, phone, Wi-Fi), and styles (square, dot, round). It also supports PNG and SVG outputs.

## Features

- Generate QR codes for different content types:
    - Links/text
    - Email
    - Geo Location
    - Phone Number
    - Wi-Fi
- Customize QR code color and background color.
- Choose between PNG and SVG formats. (PNG WIP)
- Add subtitles to QR codes (SVG). (PNG WIP)
- Customize the style of QR code modules and eye shapes.

## Technologies

- **Backend**: Laravel 11
- **QR Code Generation**: [Simple-QrCode](https://github.com/SimpleSoftwareIO/simple-qrcode)
- **Image Processing**: [Intervention Image](http://image.intervention.io/)
- **Docker**: Docker + Laravel Sail (PHP 8.3)

## Prerequisites

- [Docker](https://www.docker.com/)
- [Composer](https://getcomposer.org/)

## Getting Started

### Clone the repository

```bash
git clone https://github.com/sirap95/qrcode-backend.git
cd qrcode-backend
```
## Example of payload requests

### Payload for WI-FI

```json
{
"wifi": {
"encryption": "WPA",   // Encryption type (WPA, WEP, or none)
"ssid": "MyNetwork",   // SSID of the network
"password": "mypassword", // Password for the network
"hidden": false        // Whether the network is hidden or not
},
"color": "blue",             // Optional: Color of the QR code (e.g., blue)
"background_color": "white", // Optional: Background color of the QR code
"type": "svg",               // Type of QR code (SVG or PNG)
"subtitle": "Connect to WiFi", // Optional: Subtitle or any additional text
"style":"round", // Optional: Style of the QR Code | square/dot/round
"eye": "circle" // Optional: Eye type of the QR Code | circle/square
}

```

### Payload for EMAIL

```json
{
    "email":"email@email.com",
    "color":"black",
    "background_color": "white",
    "type":"svg",
    "subtitle":"subtitle here",
    "style":"round",
    "eye": "circle"
}

```

### Payload for GEO

```json
{
    "geo": {
        "latitude": 37.7749,  // Latitude of the location
        "longitude": -122.4194 // Longitude of the location
    },
    "color":"black",
    "background_color": "blue",
    "type":"svg",
    "subtitle":"review us",
    "style":"round",
    "eye": "circle"
}

```

### Payload for GEO

```json
{
    "phone_number": "+1234567890",
    "color":"black",
    "background_color": "white",
    "type":"SVG",
    "subtitle":"Call us!",
    "style":"round",
    "eye": "circle"
}

```

### Payload for PHONE

```json
{
    "phone_number": "+1234567890",
    "color":"black",
    "background_color": "white",
    "type":"SVG",
    "subtitle":"Call us!",
    "style":"round",
    "eye": "circle"
}

```

### Payload for LINK

```json
{
    "text": "https://your-link-here",
    "color":"black",
    "background_color": "white",
    "type":"svg",
    "subtitle":"Review Us!",
    "style":"round",
    "eye": "square"
}

```

### Payload for TEXT

```json
{
    "text": "YOUR-TEXT-HERE",
    "color":"black",
    "background_color": "white",
    "type":"svg",
    "subtitle":"Review Us!",
    "style":"round",
    "eye": "square"
}

```
