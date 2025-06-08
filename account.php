<?php
session_start();
require_once 'dbconnect.php';

// Prüfen, ob der User eingeloggt ist
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

// User-Daten auslesen
$user_id = $_SESSION['user_id'];
$sql = "SELECT vorname, nachname, email, organizer FROM user WHERE id = ?";
$stmt = $dbuser->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($vorname, $nachname, $email, $organizer);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="style.css" />
    <link rel="icon" type="image/svg+xml" href="logo.svg" />
    <title>Eventoria</title>
</head>
<body>
    <header>
      <img src="logo.svg" alt="Eventoria Logo" class="logo" />
      <h1>Willkommen bei Eventoria</h1>
      <h2>Dein Event. Dein Ticket. Deine Bühne.</h2>
    </header>
    <nav>
        <a href="index.html">Eventoria</a>
      <a href="login.html">Login</a>
      <a href="#">Events</a>
      <a href="kontakt.html">Kontakt</a>
      <form class="search-form">
        <input type="text" placeholder="Suche Events..." />
        <button type="submit">🔍</button>
      </form>
      <a href="account.php" class="account-btn" style="float: right">Account</a>
      <a
        href="#"
        id="logout-link"
        class="account-btn"
        style="float: right; margin-right: 10px"
        >Logout</a
      >
    </nav>
    <div class="login-register-container">
        <div class="form-section">
            <h2>Mein Account</h2>
            <p><strong>Vorname:</strong> <?php echo htmlspecialchars($vorname); ?></p>
            <p><strong>Nachname:</strong> <?php echo htmlspecialchars($nachname); ?></p>
            <p><strong>E-Mail:</strong> <?php echo htmlspecialchars($email); ?></p>
            <p><strong>Event-Ersteller:</strong> <?php echo htmlspecialchars($organizer); ?></p>
        </div>
        <div class="form-section">
            <h3>Passwort ändern</h3>
            <form action="change_password.php" method="POST">
                <label for="old_password">Altes Passwort:</label>
                <input type="password" id="old_password" name="old_password" required>
                <label for="new_password">Neues Passwort:</label>
                <input type="password" id="new_password" name="new_password" required>
                <label for="new_password2">Neues Passwort wiederholen:</label>
                <input type="password" id="new_password2" name="new_password2" required>
                <button type="submit">Passwort ändern</button>
            </form>
        </div>
    </div>
    <footer class="footer">
      <div class="footer-content">
        <div class="footer-section">
          <h4>Eventoria</h4>
          <p>Deine Plattform für unvergessliche Events.</p>
        </div>
        <div class="footer-section">
          <h4>Navigation</h4>
          <ul>
            <li><a href="index.html">Start</a></li>
            <li><a href="login.html">Login</a></li>
            <li><a href="#">Events</a></li>
            <li><a href="kontakt.html">Kontakt</a></li>
          </ul>
        </div>
        <div class="footer-section">
          <h4>Rechtliches</h4>
          <ul>
            <li><a href="impressum.html">Impressum</a></li>
            <li><a href="datenschutz.html">Datenschutz</a></li>
            <li><a href="agb.html">AGB</a></li>
          </ul>
        </div>
      </div>
      <div class="footer-bottom">
        <p>© 2025 EventTickets. Alle Rechte vorbehalten.</p>
      </div>
    </footer>
    <script src="logout.js"></script>
</body>
</html>