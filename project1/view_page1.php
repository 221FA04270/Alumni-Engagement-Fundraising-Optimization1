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

$message = "";

// Donation handling
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = $_POST['ID'];
    $title = $_POST['Title'];
    $location = $_POST['Location'];
    $description = $_POST['Description'];
    $eventDate = $_POST['EventDate'];
    $type = $_POST['Type'];
    $cost = (float)$_POST['Cost'];

    // Check if event already exists
    $check = $conn->prepare("SELECT Cost FROM donated_events WHERE EventID = ?");
    $check->bind_param("i", $id);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $newCost = $row['Cost'] * 2;

        $update = $conn->prepare("UPDATE donated_events 
                                  SET Cost = ?, Title=?, Location=?, Description=?, EventDate=?, Type=? 
                                  WHERE EventID=?");
        $update->bind_param("dsssssi", $newCost, $title, $location, $description, $eventDate, $type, $id);
        $update->execute();
        $update->close();
        $message = "✅ Donation multiplied successfully! New total cost: ₹" . $newCost;
    } else {
        $insert = $conn->prepare("INSERT INTO donated_events (EventID, Title, Location, Description, EventDate, Type, Cost)
                                  VALUES (?, ?, ?, ?, ?, ?, ?)");
        $insert->bind_param("isssssd", $id, $title, $location, $description, $eventDate, $type, $cost);
        $insert->execute();
        $insert->close();
        $message = "✅ First donation added successfully!";
    }

    $check->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Donated Events</title>
    <style>
        body {
            font-family: "Segoe UI", Arial, sans-serif;
            background: #f4f6f8;
            margin: 40px;
        }
        h1 {
            text-align: center;
            color: #222;
            margin-bottom: 25px;
        }
        .msg {
            text-align: center;
            font-size: 18px;
            margin-bottom: 20px;
            padding: 10px;
            border-radius: 8px;
            width: fit-content;
            margin-left: auto;
            margin-right: auto;
        }
        .success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0px 2px 8px rgba(0,0,0,0.1);
        }
        th {
            background: #007BFF;
            color: #fff;
            padding: 10px;
        }
        td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }
        tr:hover {
            background: #f1f1f1;
        }
        td b {
            color: #007BFF;
        }

        /* 🧠 AI Widget Styling */
        .ai-widget {
            margin: 30px auto;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            padding: 20px 25px;
            width: 60%;
            text-align: center;
            animation: fadeIn 0.6s ease-in;
        }
        .ai-title {
            font-weight: bold;
            color: #007BFF;
            font-size: 20px;
            margin-bottom: 10px;
        }
        .ai-text {
            font-size: 16px;
            color: #333;
        }
        @keyframes fadeIn {
            from {opacity: 0; transform: translateY(10px);}
            to {opacity: 1; transform: translateY(0);}
        }
    </style>
</head>
<body>

<h1>Donated Events</h1>

<?php if (!empty($message)): ?>
    <div class="msg success"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<table>
    <tr>
        <th>Event ID</th>
        <th>Title</th>
        <th>Location</th>
        <th>Description</th>
        <th>Date</th>
        <th>Type</th>
        <th>Total Cost</th>
    </tr>

<?php
// Fetch all donated events
$sql = "SELECT * FROM donated_events";
$result = $conn->query($sql);

$totalDonations = 0;
$highestEvent = null;
$highestAmount = 0;
$eventCount = 0;

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $totalDonations += $row['Cost'];
        $eventCount++;
        if ($row['Cost'] > $highestAmount) {
            $highestAmount = $row['Cost'];
            $highestEvent = $row['Title'];
        }

        echo "<tr>";
        echo "<td>{$row['EventID']}</td>";
        echo "<td>" . htmlspecialchars($row['Title']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Location']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Description']) . "</td>";
        echo "<td>{$row['EventDate']}</td>";
        echo "<td>{$row['Type']}</td>";
        echo "<td><b>₹{$row['Cost']}</b></td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='7' style='text-align:center;'>No donated events yet</td></tr>";
}
$conn->close();
?>

</table>

<!-- 🧠 AI Agent Section -->
<?php if ($eventCount > 0): ?>
<div class="ai-widget">
    <div class="ai-title">🤖 Smart Donation Assistant</div>
    <div class="ai-text">
        <?php
        echo "Hello donor! You’ve contributed to <b>$eventCount</b> event(s) so far.<br>";
        echo "💰 Total donations raised: <b>₹$totalDonations</b><br>";

        if ($highestEvent) {
            echo "🏆 The most funded event is <b>" . htmlspecialchars($highestEvent) . "</b> with ₹$highestAmount collected.<br>";
        }

        // Simple smart advice
        if ($totalDonations < 500) {
            echo "✨ Tip: Small steps make a big impact. Support another event to reach ₹500!";
        } elseif ($totalDonations < 1000) {
            echo "🔥 Great progress! You're on your way to crossing ₹1000 in donations!";
        } else {
            echo "🌟 Outstanding! You’re among the top supporters this week!";
        }
        ?>
    </div>
</div>
<?php endif; ?>

</body>
</html>
