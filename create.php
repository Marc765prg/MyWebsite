<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $image_name = null;

    // File upload logic
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $image_name = time() . '_' . uniqid() . '.' . $ext;
        
        if (!is_dir('uploads')) {
            mkdir('uploads', 0777, true);
        }
        
        move_uploaded_filename($_FILES['image']['tmp_name'], 'uploads/' . $image_name);
    }

    if (!empty($name) && !empty($email)) {
        $stmt = $pdo->prepare("INSERT INTO users (name, email, image) VALUES (?, ?, ?)");
        if ($stmt->execute([$name, $email, $image_name])) {
            header("Location: index.php");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add User</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Add New User</h2>
        <form method="POST" action="create.php" enctype="multipart/form-data">
            <input type="text" name="name" placeholder="Enter Full Name" required>
            <input type="email" name="email" placeholder="Enter Email Address" required>
            <label class="file-label">Profile Photo</label>
            <input type="file" name="image" accept="image/*">
            <button type="submit">Submit</button>
            <a href="index.php" class="btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>
3. Update index.php
Add an extra <th> and <td> to display the image preview:

PHP
<?php
require 'db.php';

if (isset($_GET['delete_id'])) {
    // Optionally fetch image to delete file from disk
    $stmt = $pdo->prepare("SELECT image FROM users WHERE id = ?");
    $stmt->execute([$_GET['delete_id']]);
    $user = $stmt->fetch();
    if ($user && $user['image'] && file_exists('uploads/' . $user['image'])) {
        unlink('uploads/' . $user['image']);
    }

    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$_GET['delete_id']]);
    header("Location: index.php");
    exit;
}

$stmt = $pdo->query("SELECT * FROM users ORDER BY id DESC");
$users = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>PHP CRUD Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>User Management Dashboard</h2>
        <a href="create.php" class="btn-primary">Add New User</a>
        
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Photo</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($users) > 0): ?>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= htmlspecialchars($user['id']) ?></td>
                            <td>
                                <?php if (!empty($user['image']) && file_exists('uploads/' . $user['image'])): ?>
                                    <img src="uploads/<?= htmlspecialchars($user['image']) ?>" class="user-avatar" alt="Avatar">
                                <?php else: ?>
                                    <div class="user-avatar-placeholder">N/A</div>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($user['name']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td>
                                <a href="update.php?id=<?= $user['id'] ?>" class="btn-edit">Edit</a>
                                <a href="index.php?delete_id=<?= $user['id'] ?>" class="btn-delete" onclick="return confirm('Are you sure?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5" style="text-align:center;">No records found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>