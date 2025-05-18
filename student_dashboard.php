<?php
require_once 'includes/config.php';

// Check if user is logged in and is a student
if (!isLoggedIn() || isAdmin()) {
    redirect('index.php');
}

// Get student statistics
$user_id = $_SESSION['user_id'];
$stats = [
    'total_attempts' => $conn->query("SELECT COUNT(*) FROM quiz_attempts WHERE user_id = $user_id")->fetchColumn(),
    'avg_score' => $conn->query("SELECT AVG(score) FROM quiz_attempts WHERE user_id = $user_id")->fetchColumn(),
    'best_score' => $conn->query("SELECT MAX(score) FROM quiz_attempts WHERE user_id = $user_id")->fetchColumn(),
    'subjects_taken' => $conn->query("SELECT COUNT(DISTINCT subject_id) FROM quiz_attempts WHERE user_id = $user_id")->fetchColumn()
];

// Get recent quiz attempts
$stmt = $conn->prepare("
    SELECT qa.*, s.name as subject_name 
    FROM quiz_attempts qa 
    JOIN subjects s ON qa.subject_id = s.id 
    WHERE qa.user_id = ? 
    ORDER BY qa.completed_at DESC 
    LIMIT 5
");
$stmt->execute([$user_id]);
$recent_attempts = $stmt->fetchAll();

// Get available subjects
$stmt = $conn->query("SELECT * FROM subjects ORDER BY name");
$subjects = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - Quiz System</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Student Dashboard</h1>
            <nav>
                <a href="quiz.php"><i class="fas fa-question-circle"></i> Take Quiz</a>
                <a href="leaderboard.php"><i class="fas fa-trophy"></i> Leaderboard</a>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </nav>
        </header>

        <div class="dashboard-container">
            <div class="welcome-section">
                <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
                <p>Track your quiz performance and continue learning.</p>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <i class="fas fa-tasks"></i>
                    <h3>Total Attempts</h3>
                    <div class="stat-value"><?php echo $stats['total_attempts']; ?></div>
                </div>
                <div class="stat-card">
                    <i class="fas fa-chart-line"></i>
                    <h3>Average Score</h3>
                    <div class="stat-value"><?php echo round($stats['avg_score'], 1); ?>%</div>
                </div>
                <div class="stat-card">
                    <i class="fas fa-star"></i>
                    <h3>Best Score</h3>
                    <div class="stat-value"><?php echo $stats['best_score']; ?>%</div>
                </div>
                <div class="stat-card">
                    <i class="fas fa-book"></i>
                    <h3>Subjects Taken</h3>
                    <div class="stat-value"><?php echo $stats['subjects_taken']; ?></div>
                </div>
            </div>

            <div class="dashboard-grid">
                <div class="recent-activity">
                    <h2><i class="fas fa-history"></i> Recent Quiz Attempts</h2>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Subject</th>
                                    <th>Score</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_attempts as $attempt): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($attempt['subject_name']); ?></td>
                                        <td><?php echo $attempt['score']; ?>%</td>
                                        <td><?php echo date('M d, Y H:i', strtotime($attempt['completed_at'])); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="available-quizzes">
                    <h2><i class="fas fa-list"></i> Available Quizzes</h2>
                    <div class="quiz-list">
                        <?php foreach ($subjects as $subject): ?>
                            <div class="quiz-card">
                                <h3><?php echo htmlspecialchars($subject['name']); ?></h3>
                                <p><?php echo htmlspecialchars($subject['description']); ?></p>
                                <a href="quiz.php?subject=<?php echo $subject['id']; ?>" class="btn btn-primary">
                                    <i class="fas fa-play"></i> Start Quiz
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .dashboard-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .welcome-section {
            text-align: center;
            margin-bottom: 30px;
            padding: 20px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border-radius: 8px;
        }

        .welcome-section h2 {
            margin-bottom: 10px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-card i {
            font-size: 2rem;
            color: var(--primary-color);
            margin-bottom: 10px;
        }

        .stat-card h3 {
            color: #2c3e50;
            margin-bottom: 10px;
            font-size: 16px;
        }

        .stat-value {
            font-size: 24px;
            font-weight: bold;
            color: var(--primary-color);
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
        }

        .recent-activity, .available-quizzes {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
        }

        .recent-activity h2, .available-quizzes h2 {
            color: #2c3e50;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #fff;
            font-weight: 600;
            color: #2c3e50;
        }

        tr:hover {
            background-color: #fff;
        }

        .quiz-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .quiz-card {
            background: #fff;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .quiz-card h3 {
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .quiz-card p {
            color: #666;
            margin-bottom: 15px;
            font-size: 0.9rem;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background: var(--primary-color);
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background: var(--secondary-color);
        }

        @media (max-width: 768px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</body>
</html> 