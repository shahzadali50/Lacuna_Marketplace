<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

if (!function_exists('autoTranslate')) {
    function autoTranslate($text, $targetLang = 'es')
    {
        try {
            $text = trim(strip_tags($text)); // Clean input
            $apiKey = env('GOOGLE_TRANSLATE_API_KEY');
            $url = 'https://translation.googleapis.com/language/translate/v2';

            $response = Http::post($url, [
                'q' => $text,
                'source' => 'en', // Force English source
                'target' => $targetLang,
                'format' => 'text',
                'key' => $apiKey,
            ]);

            $data = $response->json();

            return $data['data']['translations'][0]['translatedText'] ?? $text;
        } catch (\Exception $e) {
            Log::error("Translation failed [{$targetLang}]: " . $e->getMessage());
            return $text;
        }
    }
}
