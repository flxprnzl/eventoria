<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'dbconnect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (
        !empty($_POST['vorname']) &&
        !empty($_POST['nachname']) &&
        !empty($_POST['email']) &&
        !empty($_POST['password']) &&
        !empty($_POST['password2'])
    ) {
        $vorname = $dbuser->real_escape_string($_POST['vorname']);
        $nachname = $dbuser->real_escape_string($_POST['nachname']);
        $email = $dbuser->real_escape_string($_POST['email']);
        $password = $_POST['password'];
        $password2 = $_POST['password2'];

        // Organizer-Checkbox auswerten
        $organizer = isset($_POST['event_erstellen']) ? "ja" : "nein";

        if ($password !== $password2) {
            echo '<script>alert("Passwörter stimmen nicht überein!"); window.history.back();</script>';
            exit;
        }

        // Prüfen, ob die E-Mail schon existiert
        $checkSql = "SELECT id FROM user WHERE email = ?";
        $checkStmt = $dbuser->prepare($checkSql);
        $checkStmt->bind_param("s", $email);
        $checkStmt->execute();
        $checkStmt->store_result();

        if ($checkStmt->num_rows > 0) {
            echo '<script>alert("Diese E-Mail ist bereits registriert!"); window.history.back();</script>';
            $checkStmt->close();
            exit;
        }
        $checkStmt->close();

        // Nutzer anlegen
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO user (vorname, nachname, email, password, organizer) VALUES (?, ?, ?, ?, ?)";
        $stmt = $dbuser->prepare($sql);
        $stmt->bind_param("sssss", $vorname, $nachname, $email, $hash, $organizer);

        if ($stmt->execute()) {
            echo '<script>alert("Registrierung erfolgreich!"); window.location.href="index.html";</script>';
        } else {
            echo "Fehler: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Bitte alle Felder ausfüllen!";
    }
}
?>

