<?php
// Načtení dat ze souboru CSV s oddělovačem středníkem
$napoveda = array_map(function($line) {
    return str_getcsv($line, ";");
}, file('napoveda.csv'));
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nápověda</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container">
    <h1>Nápověda k telefonnímu seznamu</h1>
    <div class="buttons" style="text-align: center; margin-bottom: 20px;">
        <button onclick="window.location.href='index.php'">⬅️ Zpět</button>
    </div>
    <table>
        <thead>
            <tr>
                <th>Téma</th>
                <th>Popis</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($napoveda as $item): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item[0] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($item[1] ?? ''); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
