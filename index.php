<!DOCTYPE html>
<html lang="nl">
    <?php
require_once 'config.php';
require_once 'AIWrapper.php';
$recipe = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['ingredients'])) {
try {
$ingredients = explode(',', $_POST['ingredients']);
$ingredients = array_map('trim', $ingredients);
$wrapper = new AIWrapper(OPENAI_API_KEY);
$recipe = $wrapper->generateRecipe($ingredients); } catch (Exception $e) { $error = "Error: " . $e->getMessage(); } } ?>
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
            label {
                display: block;
                margin-bottom: 5px;
                font-weight: bold;
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
            .recipe {
                margin-top: 20px;
                padding: 20px;
                background-color: #f8f9fa;
                border-radius: 4px;
            }
            .recipe h2 {
                color: #333;
                margin-top: 0;
            }
            pre {
                white-space: pre-wrap;
                word-wrap: break-word;
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
                <label for="ingredients">Ingrediënten (gescheiden door komma's):</label>
                <textarea id="ingredients" name="ingredients" rows="3" required><?php echo isset($_POST['ingredients']) ? htmlspecialchars($_POST['ingredients']) : ''; ?></textarea>
                <button type="submit">Genereer Recept</button>
            </form>
            <?php if ($recipe): ?>
            <div class="recipe">
                <h2>Gegenereerd Recept</h2>
                <pre><?php echo htmlspecialchars($recipe); ?></pre>
            </div>
            <?php endif; ?>
        </div>
    </body>
</html>
