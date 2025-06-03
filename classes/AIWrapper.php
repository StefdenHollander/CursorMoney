<?php
require_once 'Recipe.php';
require_once 'RecipeFormatter.php';

class AIWrapper
{
    private $ingredients = [];
    private $response = '';
    private $apiKey;
    private $model;
    private $apiUrl = 'https://api.openai.com/v1/chat/completions';
    private $formatter;

    public function __construct($apiKey = null, $model = 'gpt-3.5-turbo') {
        if (!defined('API_KEY') && $apiKey === null) {
            require_once __DIR__ . '/../config/config.php';
            $this->apiKey = API_KEY;
        } else {
            $this->apiKey = $apiKey;
        }
        $this->model = $model;
        $this->formatter = new RecipeFormatter();
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

    public function generateRecipe($ingredients, $aantalPersonen = 4)
    {
        if (!is_array($ingredients)) {
            throw new Exception('Ingrediënten moeten als array worden doorgegeven');
        }
        if (count($ingredients) === 0) {
            throw new Exception('Geef minimaal één ingrediënt op');
        }

        $ingredientsList = implode(', ', $ingredients);
        $prompt = <<<EOT
        Genereer een duidelijk en gedetailleerd recept voor $aantalPersonen personen met de volgende ingrediënten: $ingredientsList.
        
        Belangrijke instructies voor hoeveelheden:
        - Voor 4 personen is een standaard hoeveelheid bijvoorbeeld: 400g pasta, 200g kaas, 200g ham
        - Voor $aantalPersonen personen moet je deze hoeveelheden proportioneel aanpassen
        - Gebruik realistische hoeveelheden per persoon:
          * Pasta/rijst: ongeveer 100g per persoon
          * Vlees/vis: ongeveer 150-200g per persoon
          * Groenten: ongeveer 200g per persoon
          * Kaas: ongeveer 50g per persoon
          * Sauzen: ongeveer 50ml per persoon
        - Rond de hoeveelheden af naar realistische getallen (bijv. 1.5kg in plaats van 1.47kg)
        - Gebruik logische eenheden (kg voor grote hoeveelheden, g voor kleinere)
        
        Retourneer ALLEEN een JSON object met de volgende structuur:
        {
            "naam": "[receptnaam]",
            "ingrediënten": [
                {
                    "naam": "[ingredient naam]",
                    "hoeveelheid": "[hoeveelheid met eenheid, aangepast voor $aantalPersonen personen]"
                }
            ],
            "bereidingstijd": "[totale tijd in minuten]",
            "stappen": [
                {
                    "beschrijving": "[duidelijke stap beschrijving met specifieke details]",
                    "tijd": "[tijd voor deze stap in minuten]",
                    "tips": "[optionele tips voor deze stap, zoals 'niet te lang koken' of 'goed roeren']"
                }
            ],
            "moeilijkheidsgraad": "[makkelijk/gemiddeld/moeilijk]",
            "tips": [
                "[algemene tip 1]",
                "[algemene tip 2]"
            ],
            "benodigdheden": [
                "[keukengerei 1]",
                "[keukengerei 2]"
            ]
        }
        
        Zorg ervoor dat:
        1. Alle ingrediënten een duidelijke hoeveelheid hebben, aangepast voor $aantalPersonen personen volgens de bovenstaande richtlijnen
        2. Elke stap een geschatte tijd heeft
        3. De totale bereidingstijd de som is van alle stappen
        4. Elke stap is duidelijk beschreven met specifieke details
        5. Voeg nuttige tips toe voor elke stap waar nodig
        6. Vermeld alle benodigde keukengerei
        7. Geef algemene tips voor het beste resultaat
        8. Beschrijf de stappen alsof je het uitlegt aan een beginnende kok
        EOT;

        $rawOutput = $this->makeApiRequest($prompt);
        $recipe = $this->formatter->formatRecipe($rawOutput);
        
        if (!$recipe) {
            throw new Exception('Kon het recept niet correct verwerken');
        }
        
        return $recipe;
    }

    private function makeApiRequest($prompt)
    {
        $data = [
            'model' => $this->model,
            'messages' => [
                ['role' => 'system', 'content' => 'Je bent een ervaren chef-kok die duidelijke en gedetailleerde recepten geeft, speciaal gericht op beginnende koks. Je geeft altijd specifieke details en nuttige tips.'],
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
        
        if ($httpCode !== 200) {
            throw new Exception('API error (Code ' . $httpCode . ')');
        }
        
        $result = json_decode($response, true);
        if (!isset($result['choices'][0]['message']['content'])) {
            throw new Exception('Onverwachte API response structuur');
        }
        
        return $result['choices'][0]['message']['content'];
    }
}