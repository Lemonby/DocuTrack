<?php

namespace App\Http\Controllers;

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

        $width = 200;
        $height = 60;

        // Generate SVG content
        $svg = '<svg width="'.$width.'" height="'.$height.'" xmlns="http://www.w3.org/2000/svg" style="background: #f8fafc; border-radius: 8px; font-family: \'Outfit\', \'Inter\', sans-serif; overflow: hidden; user-select: none; border: 1px solid #e2e8f0;">';

        // Add decorative background grid / lines
        for ($i = 0; $i < 8; $i++) {
            $x1 = rand(0, $width);
            $y1 = rand(0, $height);
            $x2 = rand(0, $width);
            $y2 = rand(0, $height);
            $svg .= '<line x1="'.$x1.'" y1="'.$y1.'" x2="'.$x2.'" y2="'.$y2.'" stroke="rgba(59, 130, 246, 0.25)" stroke-width="'.rand(1, 2).'" />';
        }

        // Add background noise circles
        for ($i = 0; $i < 25; $i++) {
            $cx = rand(0, $width);
            $cy = rand(0, $height);
            $r = rand(1, 3);
            $svg .= '<circle cx="'.$cx.'" cy="'.$cy.'" r="'.$r.'" fill="rgba(148, 163, 184, 0.4)" />';
        }

        // Add random curves (bezier) for more premium noise
        for ($i = 0; $i < 2; $i++) {
            $x1 = rand(0, 40);
            $y1 = rand(10, 50);
            $cx1 = rand(50, 100);
            $cy1 = rand(0, 60);
            $cx2 = rand(100, 150);
            $cy2 = rand(0, 60);
            $x2 = rand(160, 200);
            $y2 = rand(10, 50);
            $svg .= '<path d="M '.$x1.' '.$y1.' C '.$cx1.' '.$cy1.', '.$cx2.' '.$cy2.', '.$x2.' '.$y2.'" fill="none" stroke="rgba(23, 161, 138, 0.3)" stroke-width="2" />';
        }

        // Render each letter with individual styling, rotation, and offset
        $xOffset = 25;
        for ($i = 0; $i < strlen($captchaCode); $i++) {
            $char = $captchaCode[$i];
            $rotation = rand(-15, 15);
            $yOffset = rand(38, 44);
            $fontSize = rand(26, 32);

            // Slate/Blue/Teal tailored colors for premium aesthetics matching the website theme
            $colors = [
                '#0A2540', // Slate Navy
                '#006A9A', // Ocean Blue
                '#17A18A', // Teal Green
                '#1E3A8A', // Dark Blue
                '#0F766E', // Deep Teal
            ];
            $color = $colors[array_rand($colors)];

            $svg .= '<text x="'.$xOffset.'" y="'.$yOffset.'" font-size="'.$fontSize.'px" font-weight="800" fill="'.$color.'" transform="rotate('.$rotation.', '.($xOffset + 10).', '.($yOffset - 10).')">'.$char.'</text>';
            $xOffset += 32;
        }

        // Add foreground overlapping noise lines to protect against basic OCR bots
        for ($i = 0; $i < 3; $i++) {
            $x1 = rand(0, $width);
            $y1 = rand(0, $height);
            $x2 = rand(0, $width);
            $y2 = rand(0, $height);
            $svg .= '<line x1="'.$x1.'" y1="'.$y1.'" x2="'.$x2.'" y2="'.$y2.'" stroke="rgba(10, 37, 64, 0.2)" stroke-width="'.rand(1, 2).'" />';
        }

        $svg .= '</svg>';

        return response($svg)->header('Content-Type', 'image/svg+xml');
    }
}
