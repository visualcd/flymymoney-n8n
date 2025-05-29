<?php
include 'config.php';

// Procesare formular
$success = false;
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nume = mysqli_real_escape_string($conn, $_POST['nume']);
    $prenume = mysqli_real_escape_string($conn, $_POST['prenume']);
    $telefon = mysqli_real_escape_string($conn, $_POST['telefon']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $cnp = mysqli_real_escape_string($conn, $_POST['cnp']);

    if ($nume && $prenume && $telefon && $email && $cnp) {
        // Verificare CNP sau Email existente
        $check = mysqli_query($conn, "SELECT 1 FROM utilizatori WHERE CNP='$cnp' OR Email='$email' LIMIT 1");
        if (mysqli_num_rows($check) > 0) {
            $error = 'Eroare la inregistrare - Suna la 27009!';
        } else {
            $sql = "INSERT INTO utilizatori (Nume, Prenume, Telefon, Email, CNP) VALUES ('$nume', '$prenume', '$telefon', '$email', '$cnp')";
            $result = @mysqli_query($conn, $sql);
            if ($result) {
                $success = true;
            } else {
                if (strpos(mysqli_error($conn), 'Duplicate entry') !== false) {
                    $error = 'Eroare la inregistrare - Suna la 27009';
                } else {
                    $error = 'Eroare la salvare: ' . htmlspecialchars(mysqli_error($conn));
                }
            }
        }
    } else {
        $error = 'Toate câmpurile sunt obligatorii!';
    }
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Înregistrare utilizator</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .card { box-shadow: 0 4px 24px rgba(0,0,0,0.08); border: none; }
        .form-label { font-weight: 500; }
        .container { max-width: 430px; margin-top: 60px; }
    </style>
</head>
<body>
<div class="container">
    <div class="card p-4">
        <h2 class="mb-4 text-center">Înregistrare utilizator</h2>
        <?php if ($success): ?>
            <div class="alert alert-success text-center">Înregistrare realizată cu succes!</div>
        <?php elseif ($error): ?>
            <div class="alert alert-danger text-center"><?= $error ?></div>
        <?php endif; ?>
        <form method="post" autocomplete="off">
            <div class="mb-3">
                <label for="nume" class="form-label">Nume</label>
                <input type="text" class="form-control" id="nume" name="nume" required value="<?= isset($_POST['nume']) ? htmlspecialchars($_POST['nume']) : '' ?>">
            </div>
            <div class="mb-3">
                <label for="prenume" class="form-label">Prenume</label>
                <input type="text" class="form-control" id="prenume" name="prenume" required value="<?= isset($_POST['prenume']) ? htmlspecialchars($_POST['prenume']) : '' ?>">
            </div>
            <div class="mb-3">
                <label for="telefon" class="form-label">Telefon</label>
                <input type="text" class="form-control" id="telefon" name="telefon" required pattern="[0-9]{10,15}" value="<?= isset($_POST['telefon']) ? htmlspecialchars($_POST['telefon']) : '' ?>">
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
            </div>
            <div class="mb-3">
                <label for="cnp" class="form-label">CNP</label>
                <input type="text" class="form-control" id="cnp" name="cnp" required pattern="[0-9]{13}" value="<?= isset($_POST['cnp']) ? htmlspecialchars($_POST['cnp']) : '' ?>">
            </div>
            <?php if ($error): ?>
                <div class="alert alert-danger text-center mb-3"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <button type="submit" class="btn btn-primary w-100">Înregistrează</button>
        </form>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
