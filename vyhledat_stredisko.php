<?php
// Funkce pro odstranění diakritiky a převod na malá písmena
function normalizeDiacritics($string) {
    return strtolower(strtr($string, [
        'á'=>'a','č'=>'c','ď'=>'d','é'=>'e','ě'=>'e','í'=>'i','ň'=>'n',
        'ó'=>'o','ř'=>'r','š'=>'s','ť'=>'t','ú'=>'u','ů'=>'u','ý'=>'y','ž'=>'z',
        'Á'=>'a','Č'=>'c','Ď'=>'d','É'=>'e','Ě'=>'e','Í'=>'i','Ň'=>'n',
        'Ó'=>'o','Ř'=>'r','Š'=>'s','Ť'=>'t','Ú'=>'u','Ů'=>'u','Ý'=>'y','Ž'=>'z'
    ]));
}

$vysledky = [];
$csv_file = 'telefony.csv';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $hledane_stredisko = normalizeDiacritics(trim($_POST['stredisko']));
    if (file_exists($csv_file) && ($file = fopen($csv_file, 'r'))) {
        while (($row = fgetcsv($file, 1000, ';')) !== false) {
            if (stripos(normalizeDiacritics($row[1]), $hledane_stredisko) !== false) {
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
    <title>Vyhledat podle střediska</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container">
    <h1>Správa telefonního seznamu Kovolit, a.s.</h1>
    <div class="buttons" style="text-align: center; margin-bottom: 20px;">
        <button onclick="window.location.href='index.php'">⬅️ Zpět</button>
    </div>
    <h2>Vyhledat podle střediska</h2>
    <form method="post">
        <label for="stredisko">Středisko:</label>
        <input type="text" id="stredisko" name="stredisko" required>
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
