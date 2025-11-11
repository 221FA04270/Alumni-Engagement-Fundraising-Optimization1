<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "alumni";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL query to fetch all events
$sql = "SELECT ID, Title, Location, Description, EventDate, Type, Cost FROM event_temp";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Event Table</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            margin: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0px 2px 8px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 10px 15px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background: #007BFF;
            color: white;
        }
        tr:hover {
            background: #f1f1f1;
        }
        button {
            background-color: #28a745;
            border: none;
            color: white;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #218838;
        }
        h1 {
            color: #333;
            text-align: center;
        }
    </style>
</head>
<body>

<h1>Event List</h1>

<table>
    <tr>
        <th>ID</th>
        <th>Title</th>
        <th>Location</th>
        <th>Description</th>
        <th>Event Date</th>
        <th>Type</th>
        <th>Cost</th>
        <th>Action</th>
    </tr>

    <?php
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>{$row['ID']}</td>";
            echo "<td>" . htmlspecialchars($row['Title']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Location']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Description']) . "</td>";
            echo "<td>{$row['EventDate']}</td>";
            echo "<td>{$row['Type']}</td>";
            echo "<td>{$row['Cost']}</td>";

            // Donate button form (POST method)
            echo "<td>
                    <form action='view_page.php' method='POST'>
                        <input type='hidden' name='ID' value='{$row['ID']}'>
                        <input type='hidden' name='Title' value='" . htmlspecialchars($row['Title'], ENT_QUOTES) . "'>
                        <input type='hidden' name='Location' value='" . htmlspecialchars($row['Location'], ENT_QUOTES) . "'>
                        <input type='hidden' name='Description' value='" . htmlspecialchars($row['Description'], ENT_QUOTES) . "'>
                        <input type='hidden' name='EventDate' value='{$row['EventDate']}'>
                        <input type='hidden' name='Type' value='{$row['Type']}'>
                        <input type='hidden' name='Cost' value='{$row['Cost']}'>
                        <button type='submit'>Donate</button>
                    </form>
                  </td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='8' style='text-align:center;'>No events found</td></tr>";
    }

    $conn->close();
    ?>
</table>

</body>
</html>
