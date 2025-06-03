<?php

class RecipeFormatter {
    public function formatRecipe(string $rawOutput): ?Recipe {
        try {
            // Probeer de output te decoderen als JSON
            $data = json_decode($rawOutput, true);
            
            // Controleer of de benodigde velden aanwezig zijn
            if (!$data || !isset($data['naam']) || !isset($data['ingrediënten']) ||
                !isset($data['bereidingstijd']) || !isset($data['stappen']) ||
                !isset($data['moeilijkheidsgraad'])) {
                return $this->tryExtractRecipe($rawOutput);
            }

            // Maak een nieuw Recipe object
            return new Recipe(
                $data['naam'],
                $data['ingrediënten'],
                $data['bereidingstijd'],
                $data['stappen'],
                $data['moeilijkheidsgraad']
            );
        } catch (Exception $e) {
            return $this->tryExtractRecipe($rawOutput);
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