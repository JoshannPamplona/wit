<?php
// Database connection setup
$dbServer = getenv('DB_SERVER'); // Azure environment variable for server
$dbName = getenv('DB_NAME'); // Azure environment variable for database name
$dbUser = getenv('DB_USER'); // Azure environment variable for username
$dbPassword = getenv('DB_PASSWORD'); // Azure environment variable for password

try {
    // Establish database connection
    $conn = new PDO("sqlsrv:server=$dbServer;Database=$dbName", $dbUser, $dbPassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p>Connected to Azure SQL Database successfully!</p>";

    // Check if an update form was submitted
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
        $studentId = $_POST['id'];
        $studentName = $_POST['name'];
        $studentEmail = $_POST['email'];

        // Update the student record in the database
        $sql = "UPDATE Students SET name = ?, email = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$studentName, $studentEmail, $studentId]);

        echo "<p>Student updated successfully!</p>";
    }

    // Retrieve all records from the Students table
    $sql = "SELECT * FROM Students";
    $stmt = $conn->query($sql);
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Database connection failed: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Management</title>
</head>
<body>
    <h1>Update Student Information</h1>

    <!-- Display Registered Students -->
    <h2>Registered Students</h2>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Actions</th>
        </tr>
        <?php if (!empty($students)): ?>
            <?php foreach ($students as $student): ?>
                <tr>
                    <td><?php echo htmlspecialchars($student['id']); ?></td>
                    <td><?php echo htmlspecialchars($student['name']); ?></td>
                    <td><?php echo htmlspecialchars($student['email']); ?></td>
                    <td>
                        <!-- Update Form -->
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($student['id']); ?>">
                            <input type="text" name="name" value="<?php echo htmlspecialchars($student['name']); ?>" required>
                            <input type="email" name="email" value="<?php echo htmlspecialchars($student['email']); ?>" required>
                            <button type="submit" name="update">Update</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="4">No students registered yet.</td>
            </tr>
        <?php endif; ?>
    </table>
</body>
</html>
