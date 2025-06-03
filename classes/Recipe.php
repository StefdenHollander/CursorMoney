<?php

class Recipe {
    public string $naam;
    public array $ingrediënten; // Will contain objects with name, quantity and price
    public string $bereidingstijd;
    public array $stappen; // Will contain objects with description, time, and tips
    public string $moeilijkheidsgraad;
    public array $tips; // General tips for the recipe
    public array $benodigdheden; // Required kitchen equipment
    public float $totaalPrijs = 0; // Total price of the recipe

    public function __construct(
        string $naam,
        array $ingrediënten,
        string $bereidingstijd,
        array $stappen,
        string $moeilijkheidsgraad,
        array $tips = [],
        array $benodigdheden = [],
        float $totaalPrijs = 0
    ) {
        $this->naam = $naam;
        $this->ingrediënten = $ingrediënten;
        $this->bereidingstijd = $bereidingstijd;
        $this->stappen = $stappen;
        $this->moeilijkheidsgraad = $moeilijkheidsgraad;
        $this->tips = $tips;
        $this->benodigdheden = $benodigdheden;
        $this->totaalPrijs = $totaalPrijs;
    }
} 