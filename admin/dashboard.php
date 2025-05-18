<?php
require_once '../includes/config.php';

// Check if user is logged in and is admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('../index.php');
}

// Get statistics
$stats = [
    'total_users' => $conn->query("SELECT COUNT(*) FROM users WHERE role = 'student'")->fetchColumn(),
    'total_subjects' => $conn->query("SELECT COUNT(*) FROM subjects")->fetchColumn(),
    'total_questions' => $conn->query("SELECT COUNT(*) FROM questions")->fetchColumn(),
    'total_attempts' => $conn->query("SELECT COUNT(*) FROM quiz_attempts")->fetchColumn(),
    'avg_score' => $conn->query("SELECT AVG(score) FROM quiz_attempts")->fetchColumn()
];

// Get recent quiz attempts
$stmt = $conn->query("
    SELECT qa.*, u.username, s.name as subject_name 
    FROM quiz_attempts qa 
    JOIN users u ON qa.user_id = u.id 
    JOIN subjects s ON qa.subject_id = s.id 
    ORDER BY qa.completed_at DESC 
    LIMIT 5
");
$recent_attempts = $stmt->fetchAll();

// Get top performing students
$stmt = $conn->query("
    SELECT u.username, COUNT(qa.id) as attempts, AVG(qa.score) as avg_score
    FROM users u
    JOIN quiz_attempts qa ON u.id = qa.user_id
    WHERE u.role = 'student'
    GROUP BY u.id
    ORDER BY avg_score DESC
    LIMIT 5
");
$top_students = $stmt->fetchAll();

// Get subject performance
$stmt = $conn->query("
    SELECT s.name, COUNT(qa.id) as attempts, AVG(qa.score) as avg_score
    FROM subjects s
    LEFT JOIN quiz_attempts qa ON s.id = qa.subject_id
    GROUP BY s.id
    ORDER BY attempts DESC
");
$subject_performance = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Quiz System</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Admin Dashboard</h1>
            <nav>
                <a href="../index.php"><i class="fas fa-home"></i> Home</a>
                <a href="subjects.php"><i class="fas fa-book"></i> Manage Subjects</a>
                <a href="questions.php"><i class="fas fa-question-circle"></i> Manage Questions</a>
                <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </nav>
        </header>

        <div class="dashboard-container">
            <div class="welcome-section">
                <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
                <p>Monitor and manage the quiz system.</p>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <i class="fas fa-users"></i>
                    <h3>Total Students</h3>
                    <div class="stat-value"><?php echo $stats['total_users']; ?></div>
                </div>
                <div class="stat-card">
                    <i class="fas fa-book"></i>
                    <h3>Total Subjects</h3>
                    <div class="stat-value"><?php echo $stats['total_subjects']; ?></div>
                </div>
                <div class="stat-card">
                    <i class="fas fa-question-circle"></i>
                    <h3>Total Questions</h3>
                    <div class="stat-value"><?php echo $stats['total_questions']; ?></div>
                </div>
                <div class="stat-card">
                    <i class="fas fa-tasks"></i>
                    <h3>Quiz Attempts</h3>
                    <div class="stat-value"><?php echo $stats['total_attempts']; ?></div>
                </div>
                <div class="stat-card">
                    <i class="fas fa-chart-line"></i>
                    <h3>Average Score</h3>
                    <div class="stat-value"><?php echo round($stats['avg_score'], 1); ?>%</div>
                </div>
            </div>

            <div class="dashboard-grid">
                <div class="recent-activity">
                    <h2><i class="fas fa-history"></i> Recent Quiz Attempts</h2>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Subject</th>
                                    <th>Score</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_attempts as $attempt): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($attempt['username']); ?></td>
                                        <td><?php echo htmlspecialchars($attempt['subject_name']); ?></td>
                                        <td><?php echo $attempt['score']; ?>%</td>
                                        <td><?php echo date('M d, Y H:i', strtotime($attempt['completed_at'])); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="performance-section">
                    <div class="top-students">
                        <h2><i class="fas fa-trophy"></i> Top Performing Students</h2>
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Student</th>
                                        <th>Attempts</th>
                                        <th>Avg Score</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($top_students as $student): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($student['username']); ?></td>
                                            <td><?php echo $student['attempts']; ?></td>
                                            <td><?php echo round($student['avg_score'], 1); ?>%</td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="subject-performance">
                        <h2><i class="fas fa-chart-bar"></i> Subject Performance</h2>
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Subject</th>
                                        <th>Attempts</th>
                                        <th>Avg Score</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($subject_performance as $subject): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($subject['name']); ?></td>
                                            <td><?php echo $subject['attempts']; ?></td>
                                            <td><?php echo round($subject['avg_score'], 1); ?>%</td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
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
            background: linear-gradient(135deg, var(--danger-color), #c0392b);
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
            color: var(--danger-color);
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
            color: var(--danger-color);
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
        }

        .recent-activity, .performance-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
        }

        .recent-activity h2, .performance-section h2 {
            color: #2c3e50;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .performance-section > div {
            margin-bottom: 30px;
        }

        .performance-section > div:last-child {
            margin-bottom: 0;
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