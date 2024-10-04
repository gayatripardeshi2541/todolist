<?php
$host = 'localhost';
$db = 'crud';
$user = 'root';
$pass = ''; 

// Creating connection
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Add Task
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_task'])) {
    $task_name = $_POST['task_name'];
    if (!empty($task_name)) {
        $stmt = $conn->prepare("INSERT INTO tasks (name) VALUES (?)");
        $stmt->bind_param("s", $task_name);
        $stmt->execute();
        $stmt->close();
    }
}

// Update Task
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_task'])) {
    $task_id = $_POST['task_id'];
    $task_name = $_POST['task_name'];
    $status = $_POST['status'];
    if (!empty($task_name)) {
        $stmt = $conn->prepare("UPDATE tasks SET name=?, status=? WHERE id=?");
        $stmt->bind_param("ssi", $task_name, $status, $task_id);
        $stmt->execute();
        $stmt->close();
    }
}

// Delete Task
if (isset($_GET['delete'])) {
    $task_id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM tasks WHERE id=?");
    $stmt->bind_param("i", $task_id);
    $stmt->execute();
    $stmt->close();
}

// Fetch Tasks
$result = $conn->query("SELECT * FROM tasks");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Management</title>
</head>
<body>
    <h1>Task Management System</h1>

    <form action="" method="POST">
        <input type="text" name="task_name" placeholder="Enter task name" required>
        <button type="submit" name="add_task">Add Task</button>
    </form>

    <h2>Existing Tasks</h2>
    <ul>
        <?php while ($task = $result->fetch_assoc()): ?>
        <li>
            <form action="" method="POST" style="display:inline;">
                <input type="hidden" name="task_id" value="<?= $task['id'] ?>">
                <input type="text" name="task_name" value="<?= $task['name'] ?>" required>
                <select name="status">
                    <option value="Pending" <?= ($task['status'] == 'Pending') ? 'selected' : '' ?>>Pending</option>
                    <option value="In-Progress" <?= ($task['status'] == 'In-Progress') ? 'selected' : '' ?>>In-Progress</option>
                    <option value="Completed" <?= ($task['status'] == 'Completed') ? 'selected' : '' ?>>Completed</option>
                </select>
                <button type="submit" name="update_task">Update</button>
            </form>
            <a href="?delete=<?= $task['id'] ?>" onclick="return confirm('Are you sure you want to delete this task?');">Delete</a>
        </li>
        <?php endwhile; ?>
    </ul>
</body>
</html>

<?php
$conn->close();
?>
