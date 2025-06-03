<?php

class RecipeFormatter {
    public function formatRecipe($rawOutput) {
        try {
            $data = json_decode($rawOutput, true);
            
            if (!$data || !isset($data['naam']) || !isset($data['ingrediënten'])) {
                throw new Exception('Ongeldige recept data');
            }

            // Verwerk de ingrediënten
            $ingrediënten = [];
            foreach ($data['ingrediënten'] as $ingredient) {
                if (is_array($ingredient) && isset($ingredient['naam']) && isset($ingredient['hoeveelheid'])) {
                    $ingrediënten[] = [
                        'naam' => $ingredient['naam'],
                        'hoeveelheid' => $ingredient['hoeveelheid'],
                        'prijs' => isset($ingredient['prijs']) ? (float)str_replace(['€', ' '], '', $ingredient['prijs']) : 0
                    ];
                } else {
                    $ingrediënten[] = $ingredient;
                }
            }

            // Verwerk de stappen
            $stappen = [];
            foreach ($data['stappen'] as $stap) {
                if (is_array($stap) && isset($stap['beschrijving'])) {
                    $stappen[] = [
                        'beschrijving' => $stap['beschrijving'],
                        'tijd' => $stap['tijd'] ?? '',
                        'tips' => $stap['tips'] ?? ''
                    ];
                } else {
                    $stappen[] = $stap;
                }
            }

            // Haal de totaalprijs uit de data
            $totaalPrijs = isset($data['totaalPrijs']) ? (float)str_replace(['€', ' '], '', $data['totaalPrijs']) : 0;

            return new Recipe(
                $data['naam'],
                $ingrediënten,
                $data['bereidingstijd'],
                $stappen,
                $data['moeilijkheidsgraad'],
                $data['tips'] ?? [],
                $data['benodigdheden'] ?? [],
                $totaalPrijs
            );
        } catch (Exception $e) {
            error_log('Error formatting recipe: ' . $e->getMessage());
            return null;
        }
    }

    public function tryExtractRecipe(string $rawOutput): ?Recipe {
        // Als JSON parsing mislukt, probeer data te extraheren met regex
        $naam = $this->extractName($rawOutput);
        $ingrediënten = $this->extractIngredients($rawOutput);
        $bereidingstijd = $this->extractBereidingstijd($rawOutput);
        $stappen = $this->extractStappen($rawOutput);
        $moeilijkheidsgraad = $this->extractMoeilijkheidsgraad($rawOutput);

        if ($naam && !empty($ingrediënten)) {
            return new Recipe(
                $naam,
                $ingrediënten,
                $bereidingstijd ?? "Onbekend",
                $stappen ?? [],
                $moeilijkheidsgraad ?? "Onbekend"
            );
        }
        return null;
    }

    private function extractName(string $text): ?string {
        if (preg_match('/Titel:?\s*([^\n]+)/i', $text, $matches)) {
            return trim($matches[1]);
        }
        return null;
    }

    private function extractIngredients(string $text): array {
        $ingrediënten = [];
        if (preg_match_all('/[-•*]\s*([^\n]+)/', $text, $matches)) {
            foreach ($matches[1] as $match) {
                $ingrediënten[] = trim($match);
            }
        }
        return $ingrediënten;
    }

    private function extractBereidingstijd(string $text): ?string {
        if (preg_match('/Bereidingstijd:?\s*([^\n]+)/i', $text, $matches)) {
            return trim($matches[1]);
        }
        return null;
    }

    private function extractStappen(string $text): array {
        $stappen = [];
        if (preg_match_all('/\d+\.\s*([^\n]+)/', $text, $matches)) {
            foreach ($matches[1] as $match) {
                $stappen[] = trim($match);
            }
        }
        return $stappen;
    }

    private function extractMoeilijkheidsgraad(string $text): ?string {
        if (preg_match('/Moeilijkheidsgraad:?\s*([^\n]+)/i', $text, $matches)) {
            return trim($matches[1]);
        }
        return null;
    }
} 