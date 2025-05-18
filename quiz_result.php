<?php
require_once 'includes/config.php';

// Check if user is logged in and has completed a quiz
if (!isLoggedIn() || !isset($_SESSION['quiz_completed'])) {
    redirect('index.php');
}

$score = $_SESSION['quiz_score'];
$total_questions = $_SESSION['total_questions'];

// Clear quiz session data
unset($_SESSION['quiz_completed']);
unset($_SESSION['quiz_score']);
unset($_SESSION['total_questions']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Results</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Quiz Results</h1>
            <nav>
                <a href="index.php">Home</a>
                <a href="leaderboard.php">Leaderboard</a>
                <a href="profile.php">Profile</a>
                <a href="logout.php">Logout</a>
            </nav>
        </header>

        <div class="result-container">
            <div class="result-card">
                <h2>Your Score</h2>
                <div class="score">
                    <span class="score-number"><?php echo $score; ?></span>
                    <span class="score-total">/ <?php echo $total_questions; ?></span>
                </div>
                <div class="percentage">
                    <?php echo round(($score / $total_questions) * 100); ?>%
                </div>
                
                <div class="result-message">
                    <?php
                    $percentage = ($score / $total_questions) * 100;
                    if ($percentage >= 90) {
                        echo "Excellent! You're a master!";
                    } elseif ($percentage >= 70) {
                        echo "Great job! You did well!";
                    } elseif ($percentage >= 50) {
                        echo "Not bad! Keep practicing!";
                    } else {
                        echo "Keep studying! You can do better!";
                    }
                    ?>
                </div>

                <div class="result-actions">
                    <a href="index.php" class="btn">Take Another Quiz</a>
                    <a href="leaderboard.php" class="btn">View Leaderboard</a>
                </div>
            </div>
        </div>
    </div>

    <style>
        .result-container {
            max-width: 600px;
            margin: 30px auto;
        }

        .result-card {
            background-color: #fff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .score {
            font-size: 48px;
            margin: 20px 0;
            color: #2c3e50;
        }

        .score-number {
            color: #3498db;
            font-weight: bold;
        }

        .percentage {
            font-size: 24px;
            color: #7f8c8d;
            margin-bottom: 20px;
        }

        .result-message {
            font-size: 20px;
            color: #2c3e50;
            margin: 20px 0;
        }

        .result-actions {
            display: flex;
            gap: 20px;
            justify-content: center;
            margin-top: 30px;
        }

        .result-actions .btn {
            flex: 1;
            max-width: 200px;
        }
    </style>
</body>
</html> 