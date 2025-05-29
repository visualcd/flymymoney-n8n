<?php
include 'config.php';

$result = null;
$search = '';
$response_message = '';
$telefon = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $telefon = trim($_POST['telefon'] ?? '');
    $email = trim($_POST['email'] ?? '');

    // Trimite datele către Webhook n8n doar dacă există cel puțin un câmp completat
    if ($telefon !== '' || $email !== '') {
        $payload = [
            'Telefon' => $telefon,
            'Email' => $email
        ];

        // Future Security Enhancement:
        // To further secure the communication with the n8n webhook:
        // 1. Consider adding a secret token/API key to the webhook URL or as a custom HTTP header
        //    (e.g., 'X-Auth-Token: YOUR_SECRET_TOKEN').
        // 2. The n8n workflow should then validate this token to ensure requests are legitimate.
        // This helps prevent unauthorized submissions to your webhook if the URL is exposed.
        $ch = curl_init($n8n_webhook_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        $webhook_response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);

        // IMPORTANT: If this message is ever changed to include dynamic content (e.g., from the webhook response),
        // ensure to use htmlspecialchars() to prevent XSS vulnerabilities.
        if ($http_code >= 200 && $http_code < 300) {
            $response_message = '<div class="alert alert-success mt-3">Datele au fost transmise către WebHook n8n cu succes.</div>';
        } else {
            error_log("Error transmitting data to n8n webhook. HTTP Code: " . $http_code . ". cURL Error: " . $curl_error . ". Response: " . $webhook_response);
            $response_message = '<div class="alert alert-danger mt-3">Eroare la transmiterea datelor către WebHook n8n. Vă rugăm încercați din nou mai târziu.</div>';
        }
    } else {
        // IMPORTANT: If this message is ever changed to include dynamic content,
        // ensure to use htmlspecialchars() to prevent XSS vulnerabilities.
        $response_message = '<div class="alert alert-danger mt-3">Introduceți telefonul sau emailul.</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Trimite către WebHook n8n</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .container { max-width: 500px; margin-top: 60px; }
        .card { box-shadow: 0 4px 24px rgba(0,0,0,0.08); border: none; }
    </style>
</head>
<body>
<div class="container">
    <div class="card p-4">
        <h4 class="mb-4 text-center">Trimite către WebHook n8n</h4>
        <form method="post" autocomplete="off">
            <div class="mb-3">
                <label for="telefon" class="form-label">Telefon</label>
                <input type="text" class="form-control" id="telefon" name="telefon" value="<?= htmlspecialchars($telefon) ?>">
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($email) ?>">
            </div>
            <button type="submit" class="btn btn-primary w-100">Trimite către WebHook</button>
        </form>
        <?= $response_message ?>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>