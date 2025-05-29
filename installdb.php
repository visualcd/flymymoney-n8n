<?php
// !!! IMPORTANT SECURITY WARNING !!!
// This script is used for initial database setup only.
// Once your database tables are created, YOU MUST DELETE OR SECURE THIS FILE IMMEDIATELY.
// Leaving this file accessible on a production server can lead to accidental or malicious
// data loss or corruption.
//
// To secure, you could:
// 1. Delete this file (installdb.php).
// 2. Rename it to a non-obvious name.
// 3. Restrict access to it via .htaccess or server configuration.

include 'config.php';

$sql = "
CREATE TABLE IF NOT EXISTS utilizatori (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    Nume VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    Prenume VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    Telefon VARCHAR(10),
    CNP VARCHAR(13) UNIQUE,
    Email VARCHAR(255)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS fluturasi (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    fisier VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    data_incarcare DATE,
    data_accesare DATETIME,
    numar_accesari INT DEFAULT 0,
    CNP_utilizator VARCHAR(13),
    CONSTRAINT fk_fluturasi_utilizatori
        FOREIGN KEY (CNP_utilizator) REFERENCES utilizatori(CNP)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
";

// Execută fiecare query separat (mysqli_query nu suportă multi_query cu mai multe CREATE TABLE cu constrângeri)
$queries = explode(';', $sql);
$setup_successful = true;
$error_messages = [];

foreach ($queries as $query) {
    $query = trim($query);
    if ($query) {
        if (!mysqli_query($conn, $query)) {
            $setup_successful = false;
            $error_detail = "SQL Error in installdb.php: " . mysqli_error($conn) . " | Query: " . $query;
            error_log($error_detail);
            // Optionally, collect user-friendly parts of errors if needed, but for setup, generic is often better.
            // $error_messages[] = "Failed to execute: " . substr($query, 0, 30) . "..."; // Example
            break; // Stop on first error
        }
    }
}

if ($setup_successful) {
    echo "Database setup completed successfully.";
} else {
    echo "Database setup failed. Check server logs for details.";
    // If you collected specific messages for the user:
    // foreach ($error_messages as $msg) {
    //     echo "<br>" . htmlspecialchars($msg);
    // }
}
?>