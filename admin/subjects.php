<?php
require_once '../includes/config.php';

// Check if user is logged in and is admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('../index.php');
}

// Handle subject creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_subject'])) {
    $name = sanitize($_POST['name']);
    $description = sanitize($_POST['description']);
    
    $stmt = $conn->prepare("INSERT INTO subjects (name, description) VALUES (?, ?)");
    $stmt->execute([$name, $description]);
    $success = "Subject added successfully!";
}

// Handle subject deletion
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM subjects WHERE id = ?");
    $stmt->execute([$id]);
    $success = "Subject deleted successfully!";
}

// Handle subject update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_subject'])) {
    $id = (int)$_POST['id'];
    $name = sanitize($_POST['name']);
    $description = sanitize($_POST['description']);
    
    $stmt = $conn->prepare("UPDATE subjects SET name = ?, description = ? WHERE id = ?");
    $stmt->execute([$name, $description, $id]);
    $success = "Subject updated successfully!";
}

// Get all subjects
$stmt = $conn->query("SELECT * FROM subjects ORDER BY name");
$subjects = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Subjects</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Manage Subjects</h1>
            <nav>
                <a href="dashboard.php">Dashboard</a>
                <a href="questions.php">Manage Questions</a>
                <a href="users.php">Manage Users</a>
                <a href="../logout.php">Logout</a>
            </nav>
        </header>

        <div class="admin-container">
            <?php if (isset($success)): ?>
                <div class="success-message"><?php echo $success; ?></div>
            <?php endif; ?>

            <div class="add-subject-form">
                <h2>Add New Subject</h2>
                <form method="POST">
                    <div class="form-group">
                        <label for="name">Subject Name:</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Description:</label>
                        <textarea id="description" name="description" rows="3"></textarea>
                    </div>
                    <button type="submit" name="add_subject">Add Subject</button>
                </form>
            </div>

            <div class="subjects-list">
                <h2>Existing Subjects</h2>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Questions</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($subjects as $subject): ?>
                                <tr>
                                    <td>
                                        <span class="subject-name"><?php echo htmlspecialchars($subject['name']); ?></span>
                                        <form method="POST" class="edit-form" style="display: none;">
                                            <input type="hidden" name="id" value="<?php echo $subject['id']; ?>">
                                            <input type="text" name="name" value="<?php echo htmlspecialchars($subject['name']); ?>" required>
                                            <textarea name="description" rows="2"><?php echo htmlspecialchars($subject['description']); ?></textarea>
                                            <button type="submit" name="update_subject">Save</button>
                                            <button type="button" class="cancel-edit">Cancel</button>
                                        </form>
                                    </td>
                                    <td><?php echo htmlspecialchars($subject['description']); ?></td>
                                    <td>
                                        <?php
                                        $stmt = $conn->prepare("SELECT COUNT(*) FROM questions WHERE subject_id = ?");
                                        $stmt->execute([$subject['id']]);
                                        echo $stmt->fetchColumn();
                                        ?>
                                    </td>
                                    <td>
                                        <button class="edit-btn">Edit</button>
                                        <a href="?delete=<?php echo $subject['id']; ?>" 
                                           class="delete-btn"
                                           onclick="return confirm('Are you sure you want to delete this subject? This will also delete all questions in this subject.')">
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

        .add-subject-form {
            margin-bottom: 30px;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
        }

        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            font-family: inherit;
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

        .edit-form input[type="text"],
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
                row.querySelector('.subject-name').style.display = 'none';
                row.querySelector('.edit-form').style.display = 'block';
            });
        });

        document.querySelectorAll('.cancel-edit').forEach(button => {
            button.addEventListener('click', function() {
                const row = this.closest('tr');
                row.querySelector('.subject-name').style.display = 'block';
                row.querySelector('.edit-form').style.display = 'none';
            });
        });
    </script>
</body>
</html> 