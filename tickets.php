<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

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
    die("DB-Fehler: " . $conn->connect_error);
}

// Tabelle erstellen (nur beim ersten Start notwendig)
$conn->query("CREATE TABLE IF NOT EXISTS ticket (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    category ENUM('VIP','Sitzplatz','Stehplatz') NOT NULL,
    price DECIMAL(8,2) NOT NULL,
    total INT NOT NULL,
    sold INT NOT NULL DEFAULT 0,
    FOREIGN KEY (event_id) REFERENCES event(id)
)");

if (isset($_GET['event_id'])) {
    $event_id = (int)$_GET['event_id'];
    $eventRes = $conn->query("SELECT * FROM event WHERE id=$event_id LIMIT 1");
    $event = $eventRes->fetch_assoc();

    // Ticketdaten initialisieren, falls noch keine
    $catInfos = [
        ['VIP', 250.00, 300],
        ['Sitzplatz', 50.00, 2700],
        ['Stehplatz', 40.00, 4000],
    ];
    foreach ($catInfos as [$cat, $price, $max]) {
        $exists = $conn->query("SELECT 1 FROM ticket WHERE event_id=$event_id AND category='$cat'");
        if ($exists->num_rows == 0) {
            $stmt = $conn->prepare("INSERT INTO ticket (event_id, category, price, total, sold) VALUES (?, ?, ?, ?, 0)");
            $stmt->bind_param("isdi", $event_id, $cat, $price, $max);
            $stmt->execute();
            $stmt->close();
        }
    }
    // Tickets abrufen
    $tickets = [];
    $res = $conn->query("SELECT id, category, price, total, sold FROM ticket WHERE event_id=$event_id");
    while ($row = $res->fetch_assoc()) {
        $row['available'] = $row['total'] - $row['sold'];
        $tickets[] = $row;
    }
    header('Content-Type: application/json');
    echo json_encode(['event' => $event, 'tickets' => $tickets]);
    exit;
}

// Ticket kaufen (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ticket_id = (int)$_POST['ticket_id'];
    $qty = max(1, (int)$_POST['qty']);
    $res = $conn->query("SELECT sold, total FROM ticket WHERE id=$ticket_id");
    if ($row = $res->fetch_assoc()) {
        $verf = $row['total'] - $row['sold'];
        if ($qty > 0 && $verf >= $qty) {
            $conn->query("UPDATE ticket SET sold = sold + $qty WHERE id = $ticket_id");
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['error' => 'Nicht genug Tickets verfügbar.']);
        }
    } else {
        echo json_encode(['error' => 'Ticket nicht gefunden.']);
    }
    exit;
}
?>
