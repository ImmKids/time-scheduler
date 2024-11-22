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

// Day mapping for display
$days = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];

// Initialize the time grid (7 days, 24 hours)
$timeGrid = [];
for ($day = 0; $day < 7; $day++) {
    for ($hour = 0; $hour < 24; $hour++) {
        $timeGrid[$day][$hour] = []; // Store names of overlapping users
    }
}

if (isset($_GET['user_id_1']) || isset($_GET['user_id_2'])) {
    $userIds = [];
    if (!empty($_GET['user_id_1'])) $userIds[] = (int)$_GET['user_id_1'];
    if (!empty($_GET['user_id_2'])) $userIds[] = (int)$_GET['user_id_2'];

    foreach ($userIds as $user_id) {
        // Fetch the user's availability
        $query = "
            SELECT day_of_week, start_time, end_time, :user_id as user_id
            FROM weekly_availability 
            WHERE user_id = :user_id
        ";
        $stmt = $pdo->prepare($query);
        $stmt->execute(['user_id' => $user_id]);
        $availability_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($availability_data as $slot) {
            $day = (int)$slot['day_of_week'];
            $start_hour = (int)date('G', strtotime($slot['start_time']));
            $end_hour = (int)date('G', strtotime($slot['end_time']));

            for ($hour = $start_hour; $hour < $end_hour; $hour++) {
                if (!in_array($user_id, $timeGrid[$day][$hour])) {
                    $timeGrid[$day][$hour][] = $user_id;
                }
            }
        }
    }

    // Render the time grid
    echo '<table>';
    echo '<thead><tr><th>Hour</th>';
    foreach ($days as $day) {
        echo "<th>$day</th>";
    }
    echo '</tr></thead>';
    echo '<tbody>';

    for ($hour = 0; $hour < 24; $hour++) {
        echo '<tr>';
        echo "<td>" . sprintf('%02d:00', $hour) . "</td>"; // Render hour

        for ($day = 0; $day < 7; $day++) {
            $class = !empty($timeGrid[$day][$hour]) && count($timeGrid[$day][$hour]) > 1 ? 'overlap' : 'grid-cell';
            echo "<td class='$class'>";
            if (!empty($timeGrid[$day][$hour])) {
                foreach ($timeGrid[$day][$hour] as $userId) {
                    $userNameQuery = "SELECT name FROM users WHERE id = :user_id";
                    $userNameStmt = $pdo->prepare($userNameQuery);
                    $userNameStmt->execute(['user_id' => $userId]);
                    $userName = $userNameStmt->fetchColumn();
                    echo htmlspecialchars($userName) . '<br>';
                }
            }
            echo '</td>';
        }

        echo '</tr>';
    }

    echo '</tbody>';
    echo '</table>';
} else {
    echo '<p>Invalid request. Please select at least one user.</p>';
}
?>
