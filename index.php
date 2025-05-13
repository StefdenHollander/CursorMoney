<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ai Recept Generator</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
<h1>Ai Recept Generator</h1>
<p>Voer hieronder je ingedienten in en otvang een recept!</p>

<form action="process.php" method="POST">
<div class="form-group">
<label for="ingredients">Ingredients (gescheiden door komma's):</label>
<textarea id="ingreienets" name="ingredients" rows="4" required
placeholder="bijv. ui, knoflook, tomaat, pasta"></textarea>
</div>
<button type="submit">Genereer Recept</button>
</form>

<?php if (isset($_GET['message'])): ?>
<div class="message">
<?php echo htmlspecialchars($_GET['message']); ?>
</div>
<?php endif; ?>
</div>
</body>
</html>