<?php
require_once('tcpdf/tcpdf.php');

// Vlastní třída pro zápatí s číslováním stránek
class CustomPDF extends TCPDF {
    public function Footer() {
        $this->SetY(-15);
        $this->SetFont('dejavusans', '', 10);
        $this->Cell(0, 10, 'Strana ' . $this->getAliasNumPage() . ' / ' . $this->getAliasNbPages(), 0, 0, 'C');
    }
}

// Funkce pro načtení dat ze CSV
function loadCSV($filename) {
    return array_map(function($line) {
        return str_getcsv($line, ";");
    }, file($filename));
}

$csv_file = 'telefony.csv';
$data = loadCSV($csv_file);

// Mapování sloupců k indexům v CSV
$column_map = [
    'prijmeni' => 0,
    'stredisko' => 1,
    'telefon' => 2,
    'mobil' => 3
];

$sort_column = isset($_POST['sort']) ? $_POST['sort'] : 'prijmeni';
$sort_order = isset($_POST['order']) && $_POST['order'] === 'desc' ? 'desc' : 'asc';

// Řazení dat
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

// Generování PDF
if (isset($_POST['generate_pdf'])) {
    $pdf = new CustomPDF();
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Kovolit, a.s.');
    $pdf->SetTitle('Telefonní seznam');
    $pdf->SetHeaderData('', 0, 'Telefonní seznam', 'Kovolit, a.s. - ' . date('d.m.Y H:i'));
    $pdf->SetMargins(10, 20, 10);
    $pdf->SetFont('dejavusans', '', 10); // Nastavení fontu s podporou UTF-8
    $pdf->AddPage();
    
    $html = '<h1>Seznam Telefonních Čísel Kovolit, a.s.</h1><h4>Datum vytvoření: ' . date('d.m.Y H:i') . '</h4>';
    $html .= '<table border="1" cellpadding="5"><thead><tr><th>Příjmení</th><th>Středisko</th><th>Telefon</th><th>Mobil</th></tr></thead><tbody>';
    foreach ($data as $row) {
        $html .= '<tr><td>' . htmlspecialchars($row[0] ?? '', ENT_QUOTES, 'UTF-8') . '</td>';
        $html .= '<td>' . htmlspecialchars($row[1] ?? '', ENT_QUOTES, 'UTF-8') . '</td>';
        $html .= '<td>' . htmlspecialchars($row[2] ?? '', ENT_QUOTES, 'UTF-8') . '</td>';
        $html .= '<td>' . htmlspecialchars($row[3] ?? '', ENT_QUOTES, 'UTF-8') . '</td></tr>';
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
    <title>Generovat PDF</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container">
    <h1>Generovat PDF - Seznam Telefonních Čísel</h1>
    <form method="post">
        <label for="sort">Seřadit podle:</label>
        <select id="sort" name="sort">
            <option value="prijmeni">Příjmení</option>
            <option value="stredisko">Středisko</option>
            <option value="telefon">Telefon</option>
            <option value="mobil">Mobil</option>
        </select>
        
        <label for="order">Směr řazení:</label>
        <select id="order" name="order">
            <option value="asc">Vzestupně</option>
            <option value="desc">Sestupně</option>
        </select>
        
        <button type="submit" name="generate_pdf">📄 Generovat PDF</button>
    </form>
    <div class="buttons" style="text-align: center; margin-top: 20px;">
        <button onclick="window.location.href='index.php'">⬅️ Zpět</button>
    </div>
</div>
</body>
</html>
