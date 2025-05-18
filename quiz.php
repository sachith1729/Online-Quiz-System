<?php
require_once 'includes/config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('index.php');
}

// Check if subject is selected
if (!isset($_GET['subject'])) {
    redirect('index.php');
}

$subject_id = (int)$_GET['subject'];

// Get subject information
$stmt = $conn->prepare("SELECT * FROM subjects WHERE id = ?");
$stmt->execute([$subject_id]);
$subject = $stmt->fetch();

if (!$subject) {
    redirect('index.php');
}

// Handle quiz submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_quiz'])) {
    $score = 0;
    $total_questions = count($_POST['answers']);
    
    foreach ($_POST['answers'] as $question_id => $answer) {
        $stmt = $conn->prepare("SELECT correct_answer, points FROM questions WHERE id = ?");
        $stmt->execute([$question_id]);
        $question = $stmt->fetch();
        
        if ($question && $answer === $question['correct_answer']) {
            $score += $question['points'];
        }
    }
    
    // Save quiz attempt
    $stmt = $conn->prepare("INSERT INTO quiz_attempts (user_id, subject_id, score) VALUES (?, ?, ?)");
    $stmt->execute([$_SESSION['user_id'], $subject_id, $score]);
    
    $_SESSION['quiz_completed'] = true;
    $_SESSION['quiz_score'] = $score;
    $_SESSION['total_questions'] = $total_questions;
    
    redirect('quiz_result.php');
}

// Get questions for the subject
$stmt = $conn->prepare("SELECT * FROM questions WHERE subject_id = ? ORDER BY RAND() LIMIT 10");
$stmt->execute([$subject_id]);
$questions = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz - <?php echo htmlspecialchars($subject['name']); ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1><?php echo htmlspecialchars($subject['name']); ?> Quiz</h1>
            <nav>
                <a href="index.php">Home</a>
                <a href="leaderboard.php">Leaderboard</a>
                <a href="profile.php">Profile</a>
                <a href="logout.php">Logout</a>
            </nav>
        </header>

        <div class="quiz-container">
            <div class="quiz-info">
                <p>Total Questions: <?php echo count($questions); ?></p>
                <p>Time Limit: 30 minutes</p>
            </div>

            <form method="POST" id="quiz-form">
                <?php foreach ($questions as $index => $question): ?>
                    <div class="question-card">
                        <h3>Question <?php echo $index + 1; ?></h3>
                        <p class="question-text"><?php echo htmlspecialchars($question['question_text']); ?></p>
                        
                        <div class="options">
                            <div class="option">
                                <input type="radio" 
                                       name="answers[<?php echo $question['id']; ?>]" 
                                       id="q<?php echo $question['id']; ?>_a" 
                                       value="A" 
                                       required>
                                <label for="q<?php echo $question['id']; ?>_a">
                                    <?php echo htmlspecialchars($question['option_a']); ?>
                                </label>
                            </div>
                            
                            <div class="option">
                                <input type="radio" 
                                       name="answers[<?php echo $question['id']; ?>]" 
                                       id="q<?php echo $question['id']; ?>_b" 
                                       value="B" 
                                       required>
                                <label for="q<?php echo $question['id']; ?>_b">
                                    <?php echo htmlspecialchars($question['option_b']); ?>
                                </label>
                            </div>
                            
                            <div class="option">
                                <input type="radio" 
                                       name="answers[<?php echo $question['id']; ?>]" 
                                       id="q<?php echo $question['id']; ?>_c" 
                                       value="C" 
                                       required>
                                <label for="q<?php echo $question['id']; ?>_c">
                                    <?php echo htmlspecialchars($question['option_c']); ?>
                                </label>
                            </div>
                            
                            <div class="option">
                                <input type="radio" 
                                       name="answers[<?php echo $question['id']; ?>]" 
                                       id="q<?php echo $question['id']; ?>_d" 
                                       value="D" 
                                       required>
                                <label for="q<?php echo $question['id']; ?>_d">
                                    <?php echo htmlspecialchars($question['option_d']); ?>
                                </label>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

                <div class="quiz-actions">
                    <button type="submit" name="submit_quiz">Submit Quiz</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Timer functionality
        let timeLeft = 1800; // 30 minutes in seconds
        const timerDisplay = document.createElement('div');
        timerDisplay.className = 'timer';
        document.querySelector('.quiz-info').appendChild(timerDisplay);

        const timer = setInterval(() => {
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            timerDisplay.textContent = `Time Left: ${minutes}:${seconds.toString().padStart(2, '0')}`;
            
            if (timeLeft <= 0) {
                clearInterval(timer);
                document.getElementById('quiz-form').submit();
            }
            
            timeLeft--;
        }, 1000);

        // Prevent form resubmission
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>
</body>
</html> 