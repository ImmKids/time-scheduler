<?php
// Database connection details
$host = 'localhost';
$dbname = 'timeschema';
$username = 'root';
$password = '';

try {
    // Create PDO instance
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Fetch all users
$user_query = "SELECT id, name FROM users";
$user_result = $pdo->query($user_query);
$users = $user_result->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dynamic Weekly Availability Viewer</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th, td {
            padding: 10px;
            text-align: center;
            width: 4%;
            height: 30px;
        }
        th {
            background-color: #f4f4f4;
        }
        .form-container {
            margin-bottom: 20px;
        }
        .grid-cell {
            background-color: #f4f4f4;
            height: 50px;
            overflow-y: auto;
        }
        .grid-cell.overlap {
            background-color: green;
            color: white;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <h1>Dynamic Weekly Availability Viewer</h1>
    <div class="form-container">
        <label for="user_id_1">Select First Person:</label>
        <select id="user_id_1">
            <option value="">-- Select --</option>
            <?php foreach ($users as $user): ?>
                <option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['name']) ?></option>
            <?php endforeach; ?>
        </select>

        <label for="user_id_2">Select Second Person:</label>
        <select id="user_id_2">
            <option value="">-- Select --</option>
            <?php foreach ($users as $user): ?>
                <option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div id="availability-container">
        <!-- Availability timetable will be dynamically loaded here -->
    </div>

    <script>
        $(document).ready(function() {
            // Function to fetch and render availability for both users
            function fetchAvailability() {
                const userId1 = $('#user_id_1').val();
                const userId2 = $('#user_id_2').val();

                if (userId1 || userId2) {
                    $.ajax({
                        url: 'fetch_merged_availability.php',
                        type: 'GET',
                        data: { user_id_1: userId1, user_id_2: userId2 },
                        success: function(response) {
                            $('#availability-container').html(response);
                        },
                        error: function() {
                            $('#availability-container').html('<p>Error fetching availability data.</p>');
                        }
                    });
                } else {
                    $('#availability-container').html('<p>Please select at least one person.</p>');
                }
            }

            // Trigger fetch when either dropdown changes
            $('#user_id_1, #user_id_2').on('change', fetchAvailability);
        });
    </script>
</body>
</html>
