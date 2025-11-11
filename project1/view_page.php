<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "alumni";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = $_POST['ID'];
    $title = $_POST['Title'];
    $location = $_POST['Location'];
    $description = $_POST['Description'];
    $eventDate = $_POST['EventDate'];
    $type = $_POST['Type'];
    $cost = $_POST['Cost'];

    // Insert permanently into donated_events table
    $stmt = $conn->prepare("INSERT INTO donated_events (EventID, Title, Location, Description, EventDate, Type, Cost)
                            VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssss", $id, $title, $location, $description, $eventDate, $type, $cost);

    if ($stmt->execute()) {
        echo "<h2 style='color:green; text-align:center;'>✅ Event donated successfully!</h2>";
    } else {
        echo "<h2 style='color:red; text-align:center;'>❌ Error saving donation: " . $conn->error . "</h2>";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Donated Events</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #fafafa;
            margin: 40px;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0px 2px 8px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
        }
        th {
            background: #007BFF;
            color: white;
        }
    </style>
</head>
<body>

<h1>Donated Events</h1>

<table>
    <tr>
        <th>Event ID</th>
        <th>Title</th>
        <th>Location</th>
        <th>Description</th>
        <th>Date</th>
        <th>Type</th>
        <th>Cost</th>
    </tr>

<?php
// Fetch all permanently saved events
$sql = "SELECT * FROM donated_events";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['EventID']}</td>";
        echo "<td>" . htmlspecialchars($row['Title']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Location']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Description']) . "</td>";
        echo "<td>{$row['EventDate']}</td>";
        echo "<td>{$row['Type']}</td>";
        echo "<td>{$row['Cost']}</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='7' style='text-align:center;'>No donated events yet</td></tr>";
}

$conn->close();
?>
</table>

</body>
</html>
