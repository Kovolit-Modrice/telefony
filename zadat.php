<?php
$csv_file = 'telefony.csv';
$message = "";

// Funkce pro formátování mobilního čísla na +420 XXX XXX XXX
function formatPhoneNumber($number) {
    // Odebrání všech nečíselných znaků
    $digits = preg_replace('/\D/', '', $number);
    
    // Pokud začíná číslo na 420 nebo 00420, odstranit prefix
    if (preg_match('/^(420|00420)/', $digits)) {
        $digits = preg_replace('/^(420|00420)/', '', $digits);
    }
    
    // Pokud číslo nemá správnou délku, vrátit původní vstup
    if (strlen($digits) == 9) {
        return "+420 " . substr($digits, 0, 3) . " " . substr($digits, 3, 3) . " " . substr($digits, 6, 3);
    }
    
    return $number; // Pokud nelze naformátovat, vrátit původní číslo
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $prijmeni = $_POST['prijmeni'] ?? '';
    $stredisko = $_POST['stredisko'] ?? '';
    $telefon = $_POST['telefon'] ?? '';
    $mobil = formatPhoneNumber($_POST['mobil'] ?? ''); // Formátování mobilního čísla

    if ($prijmeni && $stredisko && $telefon) {
        $file = fopen($csv_file, "a");
        fputcsv($file, [$prijmeni, $stredisko, $telefon, $mobil], ';');
        fclose($file);
        $message = "✅ Záznam byl úspěšně přidán!";
    } else {
        $message = "❌ Vyplňte všechna povinná pole!";
    }
}
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Přidat telefonní číslo</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container">
    <h1>Správa telefonního seznamu Kovolit, a.s.</h1>
    <div class="buttons" style="text-align: center; margin-bottom: 20px;">
        <button onclick="window.location.href='index.php'">⬅️ Zpět</button>
    </div>
    <h2>Přidat telefonní číslo</h2>
    <?php if (!empty($message)): ?>
        <p style="text-align: center; font-weight: bold; color: <?php echo strpos($message, '✅') !== false ? 'green' : 'red'; ?>;">
            <?php echo $message; ?>
        </p>
    <?php endif; ?>
    <form method="post">
        <label for="prijmeni">Příjmení:</label>
        <input type="text" id="prijmeni" name="prijmeni" required>
        
        <label for="stredisko">Středisko:</label>
        <input type="text" id="stredisko" name="stredisko" required>
        
        <label for="telefon">Telefon:</label>
        <input type="text" id="telefon" name="telefon" required>
        
        <label for="mobil">Mobil (nepovinné):</label>
        <input type="text" id="mobil" name="mobil">
        
        <button type="submit">📥 Uložit záznam</button>
    </form>
</div>
</body>
</html>
