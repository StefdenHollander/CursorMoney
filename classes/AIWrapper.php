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
    // Later hier API aanroepen
    return true;
}

public function getResponse() {
// Voorlopig een standaard bericht teruggeven
$ingredientList = implode(',', $this->ingredients);
$this->response = "Recept met $ingredientList wordt verwerkt";
return $this->response;
}
}