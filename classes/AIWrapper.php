<?php
class AIWrapper {
private $ingredients = [] ;
private $response = '';

public function __construct(){
// Controleer of config beschikbaar is
if (!defined('API_KEY')) {
    require_once __DIR__ . '/../config/config.php';
}
}

public function processInput($ingredients) {
    if (empty ($ingredients)) {
        throw new Exeption("Geen ingredienten opgegeven");
    }
    $this->ingredients = $ingredients;  
 // Configuratie
$apiKey = "jouw_openai_api_key"; $model = "gpt-3.5-turbo";
// Functie om API-verzoek te doen
function callOpenAI ($prompt, $apiKey, $model) {
$url = 'https://api.openai.com/v1/chat/completions'; $headers = [
'Content-Type: application/json', 'Authorization: Bearer' . $apiKey ];

$data = [
    'model' => $model,
'messages' => [ ['role' => 'system', 'content' => 'Je bent een behulpzame assistent.'], ['role' => 'user', 'content' => $prompt]]
];
// API-verzoek versturen met CURL
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$response = curl_exec($ch); curl_close($ch);
return json_decode($response, true);


}
    return true;
}

public function getResponse() {
// Voorlopig een standaard bericht teruggeven
$ingredientList = implode(',', $this->ingredients);
$this->response = "Recept met $ingredientList wordt verwerkt";
return $this->response;
}
}