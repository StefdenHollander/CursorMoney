<?php

class AIWrapper {
    private $apiKey;
    private $apiUrl = 'https://api.openai.com/v1/chat/completions';

    public function __construct($apiKey) {
        if (empty($apiKey)) {
            throw new Exception('API key is required');
        }
        $this->apiKey = $apiKey;
    }

    public function generateRecipe($ingredients) {
        if (empty($ingredients) || !is_array($ingredients)) {
            throw new Exception('Ingredients must be a non-empty array');
        }

        $prompt = "Maak een recept met de volgende ingrediënten: " . implode(', ', $ingredients) . 
                 "\nGeef het recept in het Nederlands met:\n" .
                 "1. Een titel\n" .
                 "2. Benodigde ingrediënten met hoeveelheden\n" .
                 "3. Stapsgewijze bereidingswijze\n" .
                 "4. Eventuele tips of variaties";

        $data = [
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'Je bent een ervaren chef-kok die duidelijke en lekkere recepten maakt.'
                ],
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ],
            'temperature' => 0.7,
            'max_tokens' => 1000
        ];

        $ch = curl_init($this->apiUrl);
        if ($ch === false) {
            throw new Exception('Failed to initialize cURL');
        }

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->apiKey
            ],
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_TIMEOUT => 30
        ]);

        $response = curl_exec($ch);
        
        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new Exception('Curl error: ' . $error);
        }
        
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode !== 200) {
            throw new Exception('HTTP error: ' . $httpCode);
        }
        
        $result = json_decode($response, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Failed to decode API response: ' . json_last_error_msg());
        }
        
        if (isset($result['error'])) {
            throw new Exception('OpenAI API error: ' . $result['error']['message']);
        }
        
        if (!isset($result['choices'][0]['message']['content'])) {
            throw new Exception('Unexpected API response format');
        }

        return $result['choices'][0]['message']['content'];
    }
}
?> 