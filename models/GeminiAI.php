<?php
class GeminiAI {
    private $api_key;

    public function __construct($api_key) {
        $this->api_key = $api_key;
    }

    public function analyzeDamage($jenis_hp, $kerusakan) {
        if (empty($this->api_key)) {
            return ['error' => 'API Key Gemini belum diatur.'];
        }

        $prompt = "HP $jenis_hp mengalami masalah: $kerusakan. Berikan estimasi biaya perbaikan dalam IDR, solusi, dan waktu pengerjaan dalam format JSON seperti ini:\n\n{\"estimasi_biaya\": 250000, \"solusi\": \"Ganti LCD original\", \"waktu_pengerjaan\": \"2 jam\"}";

        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=" . $this->api_key;

        $payload = [
            "contents" => [
                ["parts" => [["text" => $prompt]]]
            ],
            "generationConfig" => [
                "temperature" => 0.1,
                "topK" => 1,
                "topP" => 1,
                "maxOutputTokens" => 2048,
                "stopSequences" => []
            ]
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        $response = curl_exec($ch);
        file_put_contents("debug_gemini.txt", $response);
        curl_close($ch);

        $data = json_decode($response, true);

        if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
            $text = $data['candidates'][0]['content']['parts'][0]['text'];
            $json_start = strpos($text, '{');
            $json_end = strrpos($text, '}') + 1;
            $json_str = substr($text, $json_start, $json_end - $json_start);
            $result = json_decode($json_str, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                return $result;
            }
        }

        return ['error' => 'Gagal menganalisis dengan AI. Response: ' . ($data['candidates'][0]['content']['parts'][0]['text'] ?? 'Tidak ada response')];
    }
}
?>