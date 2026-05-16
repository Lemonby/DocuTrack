<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CaptchaController extends Controller
{
    public function generate()
    {
        // Generate random 5 characters (omitting easily confused ones)
        $characters = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $captchaCode = '';
        for ($i = 0; $i < 5; $i++) {
            $captchaCode .= $characters[rand(0, strlen($characters) - 1)];
        }
        
        Session::put('captcha_code', $captchaCode);
        
        // Create image
        $width = 200;
        $height = 60;
        $image = imagecreatetruecolor($width, $height);
        
        // Colors
        $bgColor = imagecolorallocate($image, 240, 240, 240);
        $textColor = imagecolorallocate($image, 10, 37, 64);
        $lineColor = imagecolorallocate($image, 66, 153, 225);
        $noiseColor = imagecolorallocate($image, 200, 200, 200);
        
        // Fill background
        imagefilledrectangle($image, 0, 0, $width, $height, $bgColor);
        
        // Add noise
        for ($i = 0; $i < 100; $i++) {
            imagesetpixel($image, rand(0, $width), rand(0, $height), $noiseColor);
        }
        
        // Add lines
        for ($i = 0; $i < 5; $i++) {
            imageline($image, rand(0, $width), rand(0, $height), rand(0, $width), rand(0, $height), $lineColor);
        }
        
        // Add text
        imagestring($image, 5, 80, 20, $captchaCode, $textColor);
        
        ob_start();
        imagepng($image);
        $imageData = ob_get_clean();
        imagedestroy($image);
        
        return response($imageData)->header('Content-Type', 'image/png');
    }
}
