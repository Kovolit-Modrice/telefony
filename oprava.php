<?php
$csv_file = 'telefony.csv';
$vysledky = [];
$zaznam = null;
$index = null;
$edit_mode = false;
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
    if (isset($_POST['search'])) {
        // Hledání čísla
        $hledane_cislo = trim($_POST['telefon'] ?? '');
        if (!empty($hledane_cislo) && file_exists($csv_file)) {
            $temp_data = array_map(function($line) {
                return str_getcsv($line, ";");
            }, file($csv_file));
            
            foreach ($temp_data as $key => $row) {
                if (isset($row[2]) && ($row[2] === $hledane_cislo || (isset($row[3]) && $row[3] === $hledane_cislo))) {
                    $zaznam = $row;
                    $index = $key;
                    $edit_mode = true;
                    break;
                }
            }
            if (!$edit_mode) {
                $message = "❌ Číslo nebylo nalezeno.";
            }
        }
    } elseif (isset($_POST['update']) && isset($_POST['index'])) {
        // Aktualizace záznamu
        $prijmeni = $_POST['prijmeni'] ?? '';
        $stredisko = $_POST['stredisko'] ?? '';
        $telefon = $_POST['telefon'] ?? '';
        $mobil = formatPhoneNumber($_POST['mobil'] ?? ''); // Naformátování mobilního čísla
        $index = (int)$_POST['index'];
        
        if ($prijmeni && $stredisko && $telefon && file_exists($csv_file)) {
            $data = array_map(function($line) {
                return str_getcsv($line, ";");
            }, file($csv_file));
            
            if (isset($data[$index])) {
                $data[$index] = [$prijmeni, $stredisko, $telefon, $mobil];
                
                // Otevřít soubor pro zápis a přepsat obsah
                $file = fopen($csv_file, "w");
                if ($file) {
                    foreach ($data as $row) {
                        fputcsv($file, $row, ';');
                    }
                    fclose($file);
                    $message = "✅ Záznam byl úspěšně aktualizován!";
                    $edit_mode = false;
                } else {
                    $message = "❌ Chyba při otevírání souboru pro zápis.";
                }
            } else {
                $message = "❌ Chyba: Index neodpovídá žádnému řádku.";
            }
        } else {
            $message = "❌ Vyplňte všechna povinná pole!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Oprava záznamu</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container">
    <h1>Správa telefonního seznamu Kovolit, a.s.</h1>
    <div class="buttons" style="text-align: center; margin-bottom: 20px;">
        <button onclick="window.location.href='index.php'">⬅️ Zpět</button>
    </div>
    <h2>Oprava záznamu</h2>
    
    <?php if (!empty($message)): ?>
        <p style="text-align: center; font-weight: bold; color: <?php echo strpos($message, '✅') !== false ? 'green' : 'red'; ?>;">
            <?php echo $message; ?>
        </p>
    <?php endif; ?>
    
    <?php if (!$edit_mode): ?>
        <form method="post">
            <label for="telefon">Telefonní číslo:</label>
            <input type="text" id="telefon" name="telefon" required>
            <button type="submit" name="search">🔍 Hledat</button>
        </form>
    <?php else: ?>
        <form method="post">
            <input type="hidden" name="index" value="<?php echo htmlspecialchars($index); ?>">
            <label for="prijmeni">Příjmení:</label>
            <input type="text" id="prijmeni" name="prijmeni" value="<?php echo htmlspecialchars($zaznam[0] ?? ''); ?>" required>
            
            <label for="stredisko">Středisko:</label>
            <input type="text" id="stredisko" name="stredisko" value="<?php echo htmlspecialchars($zaznam[1] ?? ''); ?>" required>
            
            <label for="telefon">Telefon:</label>
            <input type="text" id="telefon" name="telefon" value="<?php echo htmlspecialchars($zaznam[2] ?? ''); ?>" required>
            
            <label for="mobil">Mobil (nepovinné):</label>
            <input type="text" id="mobil" name="mobil" value="<?php echo htmlspecialchars($zaznam[3] ?? ''); ?>">
            
            <button type="submit" name="update">💾 Uložit změny</button>
        </form>
    <?php endif; ?>
</div>
</body>
</html>
