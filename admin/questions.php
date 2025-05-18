<?php
require_once '../includes/config.php';

// Check if user is logged in and is admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('../index.php');
}

// Handle question creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_question'])) {
    $subject_id = (int)$_POST['subject_id'];
    $question_text = sanitize($_POST['question_text']);
    $option_a = sanitize($_POST['option_a']);
    $option_b = sanitize($_POST['option_b']);
    $option_c = sanitize($_POST['option_c']);
    $option_d = sanitize($_POST['option_d']);
    $correct_answer = sanitize($_POST['correct_answer']);
    $points = (int)$_POST['points'];
    
    $stmt = $conn->prepare("INSERT INTO questions (subject_id, question_text, option_a, option_b, option_c, option_d, correct_answer, points) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$subject_id, $question_text, $option_a, $option_b, $option_c, $option_d, $correct_answer, $points]);
    $success = "Question added successfully!";
}

// Handle question deletion
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM questions WHERE id = ?");
    $stmt->execute([$id]);
    $success = "Question deleted successfully!";
}

// Handle question update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_question'])) {
    $id = (int)$_POST['id'];
    $subject_id = (int)$_POST['subject_id'];
    $question_text = sanitize($_POST['question_text']);
    $option_a = sanitize($_POST['option_a']);
    $option_b = sanitize($_POST['option_b']);
    $option_c = sanitize($_POST['option_c']);
    $option_d = sanitize($_POST['option_d']);
    $correct_answer = sanitize($_POST['correct_answer']);
    $points = (int)$_POST['points'];
    
    $stmt = $conn->prepare("UPDATE questions SET subject_id = ?, question_text = ?, option_a = ?, option_b = ?, option_c = ?, option_d = ?, correct_answer = ?, points = ? WHERE id = ?");
    $stmt->execute([$subject_id, $question_text, $option_a, $option_b, $option_c, $option_d, $correct_answer, $points, $id]);
    $success = "Question updated successfully!";
}

// Get all subjects for dropdown
$stmt = $conn->query("SELECT * FROM subjects ORDER BY name");
$subjects = $stmt->fetchAll();

// Get all questions with subject names
$stmt = $conn->query("
    SELECT q.*, s.name as subject_name 
    FROM questions q 
    JOIN subjects s ON q.subject_id = s.id 
    ORDER BY s.name, q.id
");
$questions = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Questions</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Manage Questions</h1>
            <nav>
                <a href="dashboard.php">Dashboard</a>
                <a href="subjects.php">Manage Subjects</a>
                <a href="users.php">Manage Users</a>
                <a href="../logout.php">Logout</a>
            </nav>
        </header>

        <div class="admin-container">
            <?php if (isset($success)): ?>
                <div class="success-message"><?php echo $success; ?></div>
            <?php endif; ?>

            <div class="add-question-form">
                <h2>Add New Question</h2>
                <form method="POST">
                    <div class="form-group">
                        <label for="subject_id">Subject:</label>
                        <select id="subject_id" name="subject_id" required>
                            <option value="">Select a subject</option>
                            <?php foreach ($subjects as $subject): ?>
                                <option value="<?php echo $subject['id']; ?>">
                                    <?php echo htmlspecialchars($subject['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="question_text">Question:</label>
                        <textarea id="question_text" name="question_text" rows="3" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="option_a">Option A:</label>
                        <input type="text" id="option_a" name="option_a" required>
                    </div>
                    <div class="form-group">
                        <label for="option_b">Option B:</label>
                        <input type="text" id="option_b" name="option_b" required>
                    </div>
                    <div class="form-group">
                        <label for="option_c">Option C:</label>
                        <input type="text" id="option_c" name="option_c" required>
                    </div>
                    <div class="form-group">
                        <label for="option_d">Option D:</label>
                        <input type="text" id="option_d" name="option_d" required>
                    </div>
                    <div class="form-group">
                        <label for="correct_answer">Correct Answer:</label>
                        <select id="correct_answer" name="correct_answer" required>
                            <option value="">Select correct answer</option>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                            <option value="D">D</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="points">Points:</label>
                        <input type="number" id="points" name="points" value="1" min="1" required>
                    </div>
                    <button type="submit" name="add_question">Add Question</button>
                </form>
            </div>

            <div class="questions-list">
                <h2>Existing Questions</h2>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Subject</th>
                                <th>Question</th>
                                <th>Options</th>
                                <th>Correct</th>
                                <th>Points</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($questions as $question): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($question['subject_name']); ?></td>
                                    <td>
                                        <span class="question-text"><?php echo htmlspecialchars($question['question_text']); ?></span>
                                        <form method="POST" class="edit-form" style="display: none;">
                                            <input type="hidden" name="id" value="<?php echo $question['id']; ?>">
                                            <select name="subject_id" required>
                                                <?php foreach ($subjects as $subject): ?>
                                                    <option value="<?php echo $subject['id']; ?>" 
                                                            <?php echo $subject['id'] === $question['subject_id'] ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($subject['name']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <textarea name="question_text" rows="2" required><?php echo htmlspecialchars($question['question_text']); ?></textarea>
                                            <input type="text" name="option_a" value="<?php echo htmlspecialchars($question['option_a']); ?>" required>
                                            <input type="text" name="option_b" value="<?php echo htmlspecialchars($question['option_b']); ?>" required>
                                            <input type="text" name="option_c" value="<?php echo htmlspecialchars($question['option_c']); ?>" required>
                                            <input type="text" name="option_d" value="<?php echo htmlspecialchars($question['option_d']); ?>" required>
                                            <select name="correct_answer" required>
                                                <?php foreach (['A', 'B', 'C', 'D'] as $option): ?>
                                                    <option value="<?php echo $option; ?>" 
                                                            <?php echo $option === $question['correct_answer'] ? 'selected' : ''; ?>>
                                                        <?php echo $option; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <input type="number" name="points" value="<?php echo $question['points']; ?>" min="1" required>
                                            <button type="submit" name="update_question">Save</button>
                                            <button type="button" class="cancel-edit">Cancel</button>
                                        </form>
                                    </td>
                                    <td>
                                        A: <?php echo htmlspecialchars($question['option_a']); ?><br>
                                        B: <?php echo htmlspecialchars($question['option_b']); ?><br>
                                        C: <?php echo htmlspecialchars($question['option_c']); ?><br>
                                        D: <?php echo htmlspecialchars($question['option_d']); ?>
                                    </td>
                                    <td><?php echo $question['correct_answer']; ?></td>
                                    <td><?php echo $question['points']; ?></td>
                                    <td>
                                        <button class="edit-btn">Edit</button>
                                        <a href="?delete=<?php echo $question['id']; ?>" 
                                           class="delete-btn"
                                           onclick="return confirm('Are you sure you want to delete this question?')">
                                            Delete
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <style>
        .admin-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .add-question-form {
            margin-bottom: 30px;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
        }

        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            margin-bottom: 15px;
        }

        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            font-family: inherit;
            margin-bottom: 15px;
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
            background-color: #f8f9fa;
            font-weight: 600;
            color: #2c3e50;
        }

        .edit-form {
            margin-top: 10px;
        }

        .edit-form input,
        .edit-form select,
        .edit-form textarea {
            margin-bottom: 10px;
        }

        .edit-btn,
        .delete-btn {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-right: 5px;
            text-decoration: none;
            font-size: 14px;
        }

        .edit-btn {
            background-color: #3498db;
            color: #fff;
        }

        .delete-btn {
            background-color: #e74c3c;
            color: #fff;
        }

        .cancel-edit {
            background-color: #95a5a6;
            color: #fff;
            margin-left: 5px;
        }

        @media (max-width: 768px) {
            .edit-btn,
            .delete-btn {
                display: block;
                margin-bottom: 5px;
            }
        }
    </style>

    <script>
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function() {
                const row = this.closest('tr');
                row.querySelector('.question-text').style.display = 'none';
                row.querySelector('.edit-form').style.display = 'block';
            });
        });

        document.querySelectorAll('.cancel-edit').forEach(button => {
            button.addEventListener('click', function() {
                const row = this.closest('tr');
                row.querySelector('.question-text').style.display = 'block';
                row.querySelector('.edit-form').style.display = 'none';
            });
        });
    </script>
</body>
</html> 