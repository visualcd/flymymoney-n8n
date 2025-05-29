<?php
// Recommended Security Headers:
// It's highly recommended to implement these security headers for enhanced protection.
// You can add them using the header() function in PHP. For example:
//
// header("X-Content-Type-Options: nosniff");
// Prevents MIME-sniffing attacks.
//
// header("X-Frame-Options: DENY"); // Or SAMEORIGIN
// Protects against clickjacking attacks. DENY prevents embedding in any frame.
// SAMEORIGIN allows framing only by pages from the same origin.
//
// // header("Strict-Transport-Security: max-age=31536000; includeSubDomains"); // Example for HSTS
// // Enforces HTTPS. Start with a small max-age (e.g., max-age=300) for testing.
// // Only enable HSTS if you are sure your entire site supports HTTPS correctly.
//
// // header("Content-Security-Policy: default-src 'self'; script-src 'self' https://cdn.jsdelivr.net; style-src 'self' https://cdn.jsdelivr.net;"); // Example CSP
// // CSP is very powerful for mitigating XSS but requires careful configuration
// // based on your application's specific needs and resources.
//

// Database configuration (procedural, no PDO)
$host = 'localhost';
$db   = 'Nume_baza_de_date';
$user = 'Utilizator_baza_de_date';
$pass = 'Parola_baza_de_date';
$n8n_webhook_url = 'URL_DIN_WEBHOOK'; //modifică URL-ul din test în Live dacă treci de la Test Workflow în Live Workflow / producție 

$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) {
    error_log('Database connection failed: ' . mysqli_connect_error());
    die('A critical error occurred. Please try again later or contact support.');
}

// Future Consideration:
// For enhanced database interaction, easier use of prepared statements, and broader database support,
// consider migrating from procedural mysqli to PHP Data Objects (PDO) in the future.
// PDO offers a more consistent and object-oriented API for database operations.
?>
