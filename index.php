<?php
require_once('tcpdf/tcpdf.php');

// Načtení dat ze souboru CSV s oddělovačem středníkem
$data = array_map(function($line) {
    return str_getcsv($line, ";");
}, file('telefony.csv'));

// Pokud je první řádek hlavička, neodstraňovat ho automaticky
if (!empty($data) && count($data[0]) === 4 && strtolower($data[0][0]) === 'prijmeni') {
    array_shift($data); // Odebrání hlavičky, pokud je přítomna
}

// Získání parametru pro řazení
$sort_column = isset($_GET['sort']) ? $_GET['sort'] : 'prijmeni';
$sort_order = isset($_GET['order']) && $_GET['order'] === 'desc' ? 'desc' : 'asc';

// Mapování sloupců k indexům v CSV
$column_map = [
    'prijmeni' => 0,
    'stredisko' => 1,
    'telefon' => 2,
    'mobil' => 3
];

// Funkce pro řazení
usort($data, function ($a, $b) use ($column_map, $sort_column, $sort_order) {
    $index = $column_map[$sort_column];
    $valA = $a[$index] ?? '';
    $valB = $b[$index] ?? '';

    if (is_numeric($valA) && is_numeric($valB)) {
        return $sort_order === 'asc' ? $valA - $valB : $valB - $valA;
    } else {
        return $sort_order === 'asc' ? strcmp($valA, $valB) : strcmp($valB, $valA);
    }
});

// Pokud je požadováno generování PDF
if (isset($_GET['generate_pdf'])) {
    $pdf = new TCPDF();
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Kovolit, a.s.');
    $pdf->SetTitle('Seznam Telefonních Čísel');
    $pdf->SetHeaderData('', 0, 'Seznam Telefonních Čísel', 'Kovolit, a.s.');
    $pdf->SetMargins(10, 20, 10);
    $pdf->AddPage();
    
    $html = '<h1>Seznam Telefonních Čísel Kovolit, a.s.</h1><table border="1" cellpadding="5"><thead><tr><th>Příjmení</th><th>Středisko</th><th>Telefon</th><th>Mobil</th></tr></thead><tbody>';
    foreach ($data as $row) {
        $html .= '<tr><td>' . htmlspecialchars($row[0] ?? '') . '</td>';
        $html .= '<td>' . htmlspecialchars($row[1] ?? '') . '</td>';
        $html .= '<td>' . htmlspecialchars($row[2] ?? '') . '</td>';
        $html .= '<td>' . htmlspecialchars($row[3] ?? '') . '</td></tr>';
    }
    $html .= '</tbody></table>';
    
    $pdf->writeHTML($html, true, false, true, false, '');
    $pdf->Output('seznam_telefonu.pdf', 'D');
    exit;
}
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seznam Telefonů</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        * {
            color: black !important;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Správa telefonního seznamu Kovolit, a.s.</h1>
    <div class="buttons" style="text-align: center; margin-bottom: 20px;">
        <button onclick="window.location.href='zadat.php'">📞 Zadat nové číslo telefonu</button>
        <button onclick="window.location.href='oprava.php'">✏️ Oprava záznamu</button>
        <button onclick="window.location.href='vyhledat.php'">🔍 Vyhledat podle čísla telefonu</button>
        <button onclick="window.location.href='vyhledat_prijmeni.php'">🔍 Vyhledat podle příjmení</button>
        <button onclick="window.location.href='vyhledat_stredisko.php'">🏢 Vyhledat podle střediska</button>
        <button onclick="window.location.href='napoveda.php'">❓ Nápověda</button>
        <button onclick="window.location.href='pdfreport.php'">📄 Vygenerovat PDF</button>
    </div>
    <h2>Seznam telefonních čísel Kovolit, a.s.</h2>
    <table>
        <thead>
            <tr>
                <th><a class="ip-link" href="?sort=prijmeni&order=<?php echo $sort_order === 'asc' ? 'desc' : 'asc'; ?>">Příjmení ↕️</a></th>
                <th><a class="ip-link" href="?sort=stredisko&order=<?php echo $sort_order === 'asc' ? 'desc' : 'asc'; ?>">Středisko ↕️</a></th>
                <th><a class="ip-link" href="?sort=telefon&order=<?php echo $sort_order === 'asc' ? 'desc' : 'asc'; ?>">Telefon ↕️</a></th>
                <th><a class="ip-link" href="?sort=mobil&order=<?php echo $sort_order === 'asc' ? 'desc' : 'asc'; ?>">Mobil ↕️</a></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data as $row): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row[0] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($row[1] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($row[2] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($row[3] ?? ''); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
