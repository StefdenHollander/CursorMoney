<?php
class AIWrapper
{
    private $ingredients = [];
    private $response = '';
    private $apiKey;
    private $model;
    private $apiUrl = 'https://api.openai.com/v1/chat/completions';

    public function __construct($apiKey = null, $model = 'gpt-3.5-turbo') {
        if (!defined('API_KEY') && $apiKey === null) {
            require_once __DIR__ . '/../config/config.php';
            $this->apiKey = API_KEY;
        } else {
            $this->apiKey = $apiKey;
        }
        $this->model = $model;
    }

    public function processInput($ingredients)
    {
        if (empty($ingredients)) {
            throw new Exception("Geen ingrediënten opgegeven");
        }
        $this->ingredients = $ingredients;
        // Later hier API aanroepen
        return true;
    }

    public function getResponse()
    {
        // Voorlopig een standaard bericht teruggeven
        $ingredientsList = implode(', ', $this->ingredients);
        $this->response = "Recept met $ingredientsList wordt verwerkt";
        return $this->response;
    }

    public function makeApiRequest($prompt)
    {
        $data = [
            'model' => $this->model,
            'messages' => [
                ['role' => 'system', 'content' => 'Je bent een expert chef.'],
                ['role' => 'user', 'content' => $prompt]
            ],
            'temperature' => 0.7
        ];

        $ch = curl_init($this->apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->apiKey
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if (curl_errno($ch)) {
            throw new Exception('cURL error: ' . curl_error($ch));
        }
        curl_close($ch);
        return $this->handleResponse($response, $httpCode);
    }

    private function handleResponse($response, $httpCode)
    {
        if ($httpCode !== 200) {
            $error = json_decode($response, true);
            $message = isset($error['error']['message']) ? $error['error']['message'] : 'Onbekende API fout';
            throw new Exception('API error (Code ' . $httpCode . '): ' . $message);
        }
        $decoded = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('JSON decode error: ' . json_last_error_msg());
        }
        if (!isset($decoded['choices'][0]['message']['content'])) {
            throw new Exception('Onverwachte API response structuur');
        }
        return $decoded['choices'][0]['message']['content'];
    }

    public function generateRecipe($ingredients)
    {
        if (!is_array($ingredients)) {
            throw new Exception('Ingrediënten moeten als array worden doorgegeven');
        }
        if (count($ingredients) === 0) {
            throw new Exception('Geef minimaal één ingrediënt op');
        }
        $ingredientsList = implode(', ', $ingredients);
        $prompt = <<<EOT
        Genereer een recept met de volgende ingrediënten: $ingredientsList.
        Het recept moet de volgende onderdelen bevatten:
        1. Een creatieve naam voor het gerecht
        2. Een lijst met alle benodigde ingrediënten met hoeveelheden
        3. Stap-voor-stap bereidingswijze
        4. Geschatte bereidingstijd
        5. Aantal personen
        EOT;
        return $this->makeApiRequest($prompt);
    }
}