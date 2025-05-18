<?php
require_once 'includes/config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('index.php');
}

// Get all subjects
$stmt = $conn->query("SELECT * FROM subjects ORDER BY name");
$subjects = $stmt->fetchAll();

// Get selected subject or default to all subjects
$selected_subject = isset($_GET['subject']) ? (int)$_GET['subject'] : null;

// Get leaderboard data
$query = "SELECT 
            u.username,
            COUNT(qa.id) as attempts,
            AVG(qa.score) as avg_score,
            MAX(qa.score) as best_score
          FROM users u
          LEFT JOIN quiz_attempts qa ON u.id = qa.user_id
          WHERE u.role = 'student'";

if ($selected_subject) {
    $query .= " AND qa.subject_id = ?";
}

$query .= " GROUP BY u.id, u.username
            ORDER BY best_score DESC, attempts DESC";

$stmt = $conn->prepare($query);
if ($selected_subject) {
    $stmt->execute([$selected_subject]);
} else {
    $stmt->execute();
}
$leaderboard = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaderboard</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Leaderboard</h1>
            <nav>
                <a href="index.php">Home</a>
                <a href="quiz.php">Take Quiz</a>
                <a href="profile.php">Profile</a>
                <a href="logout.php">Logout</a>
            </nav>
        </header>

        <div class="leaderboard-container">
            <div class="subject-filter">
                <h2>Filter by Subject</h2>
                <div class="subject-buttons">
                    <a href="leaderboard.php" class="btn <?php echo !$selected_subject ? 'active' : ''; ?>">
                        All Subjects
                    </a>
                    <?php foreach ($subjects as $subject): ?>
                        <a href="leaderboard.php?subject=<?php echo $subject['id']; ?>" 
                           class="btn <?php echo $selected_subject === $subject['id'] ? 'active' : ''; ?>">
                            <?php echo htmlspecialchars($subject['name']); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="leaderboard-table">
                <table>
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Username</th>
                            <th>Attempts</th>
                            <th>Average Score</th>
                            <th>Best Score</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($leaderboard as $index => $user): ?>
                            <tr class="<?php echo $user['username'] === $_SESSION['username'] ? 'current-user' : ''; ?>">
                                <td><?php echo $index + 1; ?></td>
                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                <td><?php echo $user['attempts']; ?></td>
                                <td><?php echo round($user['avg_score'], 1); ?></td>
                                <td><?php echo $user['best_score']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <style>
        .leaderboard-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .subject-filter {
            margin-bottom: 30px;
        }

        .subject-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 15px;
        }

        .subject-buttons .btn {
            padding: 8px 16px;
            font-size: 14px;
        }

        .subject-buttons .btn.active {
            background-color: #2980b9;
        }

        .leaderboard-table {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #2c3e50;
        }

        tr:hover {
            background-color: #f8f9fa;
        }

        .current-user {
            background-color: #e3f2fd;
        }

        @media (max-width: 768px) {
            .subject-buttons {
                flex-direction: column;
            }
            
            .subject-buttons .btn {
                width: 100%;
            }
        }
    </style>
</body>
</html> 