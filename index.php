<?php
$uploadDir = __DIR__ . '/uploads';
$stateFile = __DIR__ . '/active_team.txt';
$ip = $_SERVER['REMOTE_ADDR'];
$team = (preg_match('/^(192\.168|10\.|127\.0\.0\.1)/', $ip)) ? 'rot' : 'blau';

if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);

// Upload-Handling
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $base = pathinfo($file['name'], PATHINFO_FILENAME);
    $timestamp = date('Ymd');
    $newName = "$base.$timestamp"."_$team.$ext";
    move_uploaded_file($file['tmp_name'], "$uploadDir/$newName");
    header("Location: index.php");
    exit;
}

// Status setzen
if (isset($_GET['set_active'])) {
    file_put_contents($stateFile, $team);
    header("Location: index.php");
    exit;
}

// Aktives Team laden
$activeTeam = file_exists($stateFile) ? trim(file_get_contents($stateFile)) : 'niemand';
$files = glob("$uploadDir/*");
usort($files, fn($a, $b) => filemtime($b) - filemtime($a));
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Dateiaustausch</title>
    <style>
        body {
            background-color: <?= $activeTeam === 'rot' ? '#ffdddd' : ($activeTeam === 'blau' ? '#ddeeff' : '#eeeeee') ?>;
            font-family: sans-serif;
        }
    </style>
</head>
<body>
    <h1>Es arbeitet Team <?= htmlspecialchars($activeTeam) ?> daran</h1>

    <form method="get">
        <input type="hidden" name="set_active" value="1">
        <button type="submit">Jetzt arbeite ich daran (Team <?= $team ?>)</button>
    </form>

    <h2>Datei hochladen</h2>
    <form method="post" enctype="multipart/form-data">
        <input type="file" name="file" required>
        <button type="submit">Hochladen</button>
    </form>

    <h2>Bereits hochgeladene Dateien</h2>
    <ul>
        <?php foreach ($files as $file): 
            $filename = basename($file);
            $info = explode('_', $filename);
            $teamInfo = $info[1] ?? 'unbekannt';
        ?>
            <li>
                <a href="uploads/<?= $filename ?>"><?= $filename ?></a> – von Team <?= htmlspecialchars(pathinfo($teamInfo, PATHINFO_FILENAME)) ?>
            </li>
        <?php endforeach; ?>
    </ul>

    <?php if (!empty($files)): ?>
        <form method="get" action="download.php?file=<?= urlencode(basename($files[0])) ?>&orig=<?= urlencode(pathinfo($files[0], PATHINFO_FILENAME)) ?>.zip">
            <button type="submit">Jüngste Datei herunterladen</button>
        </form>
    <?php endif; ?>
</body>
</html>