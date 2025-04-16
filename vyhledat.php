<?php
$vysledky = [];
$csv_file = 'telefony.csv';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $hledane_cislo = trim($_POST['telefon'] ?? '');
    if (!empty($hledane_cislo) && file_exists($csv_file) && ($file = fopen($csv_file, 'r'))) {
        while (($row = fgetcsv($file, 1000, ';')) !== false) {
            if (strpos($row[2], $hledane_cislo) !== false || strpos($row[3], $hledane_cislo) !== false) {
                $vysledky[] = $row;
            }
        }
        fclose($file);
    }
}
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vyhledat podle čísla telefonu</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container">
    <h1>Správa telefonního seznamu Kovolit, a.s.</h1>
    <div class="buttons" style="text-align: center; margin-bottom: 20px;">
        <button onclick="window.location.href='index.php'">⬅️ Zpět</button>
    </div>
    <h2>Vyhledat podle čísla telefonu</h2>
    <form method="post">
        <label for="telefon">Telefonní číslo:</label>
        <input type="text" id="telefon" name="telefon" required>
        <button type="submit">🔍 Hledat</button>
    </form>
    
    <?php if (!empty($vysledky)): ?>
        <h3>Výsledky hledání:</h3>
        <table>
            <thead>
                <tr>
                    <th>Příjmení</th>
                    <th>Středisko</th>
                    <th>Telefon</th>
                    <th>Mobil</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($vysledky as $row): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row[0] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row[1] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row[2] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row[3] ?? ''); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php elseif ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
        <p style="text-align: center; color: red; font-weight: bold;">❌ Nebyl nalezen žádný záznam.</p>
    <?php endif; ?>
</div>
</body>
</html>
