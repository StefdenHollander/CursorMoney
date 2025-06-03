<?php
require_once 'config.php';
require_once 'classes/AIWrapper.php';
require_once 'classes/Recipe.php';
require_once 'classes/RecipeFormatter.php';

$recipe = null;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['ingredients'])) {
    try {
        $ingredients = explode(',', $_POST['ingredients']);
        $ingredients = array_map('trim', $ingredients);
        $aantalPersonen = isset($_POST['aantal_personen']) ? (int)$_POST['aantal_personen'] : 4;
        $wrapper = new AIWrapper(OPENAI_API_KEY);
        $recipe = $wrapper->generateRecipe($ingredients, $aantalPersonen);
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="nl">
    <head>
        <title>AI Recept Generator</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                line-height: 1.6;
                margin: 0;
                padding: 20px;
                background-color: #f5f5f5;
            }
            .container {
                max-width: 800px;
                margin: 0 auto;
                background-color: white;
                padding: 20px;
                border-radius: 8px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            }
            h1 {
                color: #333;
                text-align: center;
            }
            .error {
                color: #dc3545;
                padding: 10px;
                margin: 10px 0;
                border: 1px solid #dc3545;
                border-radius: 4px;
                background-color: #f8d7da;
            }
            form {
                margin: 20px 0;
            }
            .form-group {
                margin-bottom: 15px;
            }
            .form-group label {
                display: block;
                margin-bottom: 5px;
                font-weight: bold;
            }
            .form-group input[type="number"] {
                width: 100px;
                padding: 8px;
                border: 1px solid #ddd;
                border-radius: 4px;
            }
            textarea {
                width: 100%;
                padding: 8px;
                border: 1px solid #ddd;
                border-radius: 4px;
                margin-bottom: 10px;
            }
            button {
                background-color: #007bff;
                color: white;
                padding: 10px 20px;
                border: none;
                border-radius: 4px;
                cursor: pointer;
            }
            button:hover {
                background-color: #0056b3;
            }
            .recipe-card {
                margin-top: 20px;
                padding: 20px;
                background-color: #f8f9fa;
                border-radius: 8px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            }
            .recipe-card h2 {
                color: #333;
                margin-top: 0;
                border-bottom: 2px solid #007bff;
                padding-bottom: 10px;
            }
            .recipe-details {
                display: flex;
                gap: 20px;
                margin: 15px 0;
                padding: 10px;
                background-color: #fff;
                border-radius: 4px;
            }
            .recipe-details p {
                margin: 0;
            }
            .recipe-card h3 {
                color: #444;
                margin: 20px 0 10px;
            }
            .recipe-card ul, .recipe-card ol {
                margin: 0;
                padding-left: 20px;
            }
            .recipe-card li {
                margin-bottom: 8px;
            }
            .step-time {
                color: #666;
                font-size: 0.9em;
                font-style: italic;
                margin-left: 5px;
            }
            .ingredient-quantity {
                font-weight: bold;
                color: #007bff;
            }
            .step-tip {
                margin-top: 5px;
                padding: 8px;
                background-color: #fff3cd;
                border-left: 4px solid #ffc107;
                border-radius: 4px;
                font-size: 0.9em;
                color: #856404;
            }
            .recipe-tips {
                margin-top: 20px;
                padding: 15px;
                background-color: #e8f4f8;
                border-radius: 8px;
            }
            .recipe-tips h3 {
                color: #0056b3;
                margin-top: 0;
            }
            .equipment-list {
                list-style-type: none;
                padding-left: 0;
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
                gap: 10px;
            }
            .equipment-list li {
                padding: 8px;
                background-color: #f8f9fa;
                border-radius: 4px;
                border: 1px solid #dee2e6;
            }
        </style>
    </head>

    <body>
        <div class="container">
            <h1>AI Recept Generator</h1>
            <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <form method="post">
                <div class="form-group">
                    <label for="ingredients">Ingrediënten (gescheiden door komma's):</label>
                    <textarea id="ingredients" name="ingredients" rows="3" required><?php echo isset($_POST['ingredients']) ? htmlspecialchars($_POST['ingredients']) : ''; ?></textarea>
                </div>
                <div class="form-group">
                    <label for="aantal_personen">Aantal personen:</label>
                    <input type="number" id="aantal_personen" name="aantal_personen" min="1" max="20" value="<?php echo isset($_POST['aantal_personen']) ? (int)$_POST['aantal_personen'] : 4; ?>" required>
                </div>
                <button type="submit">Genereer Recept</button>
            </form>
            <?php if ($recipe instanceof Recipe): ?>
            <div class="recipe-card">
                <h2><?php echo htmlspecialchars($recipe->naam); ?></h2>
                <div class="recipe-details">
                    <p><strong>Bereidingstijd:</strong> <?php echo htmlspecialchars($recipe->bereidingstijd); ?></p>
                    <p><strong>Moeilijkheidsgraad:</strong> <?php echo htmlspecialchars($recipe->moeilijkheidsgraad); ?></p>
                </div>

                <?php if (!empty($recipe->benodigdheden)): ?>
                <h3>Benodigdheden:</h3>
                <ul class="equipment-list">
                    <?php foreach ($recipe->benodigdheden as $item): ?>
                        <li><?php echo htmlspecialchars($item); ?></li>
                    <?php endforeach; ?>
                </ul>
                <?php endif; ?>

                <h3>Ingrediënten:</h3>
                <ul>
                    <?php foreach ($recipe->ingrediënten as $ingredient): ?>
                        <li>
                            <?php 
                            if (is_array($ingredient) && isset($ingredient['naam']) && isset($ingredient['hoeveelheid'])) {
                                echo '<span class="ingredient-quantity">' . htmlspecialchars($ingredient['hoeveelheid']) . '</span> ' . 
                                     htmlspecialchars($ingredient['naam']);
                            } else {
                                echo htmlspecialchars($ingredient);
                            }
                            ?>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <h3>Bereidingswijze:</h3>
                <ol>
                    <?php foreach ($recipe->stappen as $stap): ?>
                        <li>
                            <?php 
                            if (is_array($stap) && isset($stap['beschrijving']) && isset($stap['tijd'])) {
                                echo htmlspecialchars($stap['beschrijving']);
                                echo ' <span class="step-time">(' . htmlspecialchars($stap['tijd']) . ' min)</span>';
                                if (isset($stap['tips']) && !empty($stap['tips'])) {
                                    echo '<div class="step-tip">💡 Tip: ' . htmlspecialchars($stap['tips']) . '</div>';
                                }
                            } else {
                                echo htmlspecialchars($stap);
                            }
                            ?>
                        </li>
                    <?php endforeach; ?>
                </ol>

                <?php if (!empty($recipe->tips)): ?>
                <div class="recipe-tips">
                    <h3>Tips voor het beste resultaat:</h3>
                    <ul>
                        <?php foreach ($recipe->tips as $tip): ?>
                            <li><?php echo htmlspecialchars($tip); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </body>
</html>
