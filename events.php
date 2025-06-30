<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Login-Zwang!
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Nicht eingeloggt']);
    exit;
}

$host = "localhost";
$user = "root";
$password = "";
$dbname = "eventoria";
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Verbindung fehlgeschlagen: " . $conn->connect_error);
}

// Tabelle erstellen, falls sie nicht existiert
$createTable = "
CREATE TABLE IF NOT EXISTS event (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    location VARCHAR(255) NOT NULL,
    date DATE NOT NULL,
    image TEXT
)";
$conn->query($createTable);

// Event hinzufügen (POST) — NUR FÜR ORGANIZER!
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_SESSION['organizer']) || $_SESSION['organizer'] !== 'ja') {
        http_response_code(403);
        echo 'Kein Organizer!';
        exit;
    }
    $title = $_POST['event-title'] ?? '';
    $location = $_POST['event-location'] ?? '';
    $date = $_POST['event-date'] ?? '';
    $image = $_POST['event-image'] ?? '';

    if ($title && $location && $date) {
        $stmt = $conn->prepare("INSERT INTO event (title, location, date, image) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $title, $location, $date, $image);
        $stmt->execute();
        $stmt->close();
    }
    exit;
}

// Events abrufen (GET)
if (isset($_GET['get'])) {
    $result = $conn->query("SELECT * FROM event ORDER BY date ASC");
    $events = [];
    while ($row = $result->fetch_assoc()) {
        $events[] = [
            'id' => $row['id'],
            'title' => $row['title'],
            'location' => $row['location'],
            'date' => $row['date'],
            'image' => $row['image']
        ];
    }
    header('Content-Type: application/json');
    echo json_encode($events);
    exit;
}
?>
