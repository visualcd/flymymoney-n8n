<?php
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
$ok = true;
foreach ($queries as $query) {
    $query = trim($query);
    if ($query) {
        if (!mysqli_query($conn, $query)) {
            $ok = false;
            echo "Eroare SQL: " . mysqli_error($conn);
            break;
        }
    }
}
if ($ok) {
    echo "Succes";
}
?>