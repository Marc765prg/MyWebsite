<?php
require 'db.php';

// --- DELETE LOGIC ---
if (isset($_GET['delete_id'])) {
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

// --- READ LOGIC ---
$stmt = $pdo->query("SELECT * FROM users ORDER BY id DESC");
$users = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        
        <!-- Search Bar -->
        <div class="gx-search-container">
            <span class="gx-search-icon">🔍</span>
            <input type="text" class="gx-search-bar" placeholder="Search subjects, topics, or notes...">
        </div>

        <!-- School Widgets Grid -->
        <div class="school-widgets-grid">
            
            <!-- Class Schedule Widget -->
            <div class="gx-card widget-schedule">
                <div class="card-header">
                    <h3>📅 Today's Schedule</h3>
                </div>
                <ul class="schedule-list">
                    <li>
                        <span class="time">09:00 AM</span>
                        <span class="subject">Web Development</span>
                        <span class="room">Lab 302</span>
                    </li>
                    <li>
                        <span class="time">11:00 AM</span>
                        <span class="subject">Database Systems</span>
                        <span class="room">Room 405</span>
                    </li>
                    <li>
                        <span class="time">01:30 PM</span>
                        <span class="subject">Computer Networks</span>
                        <span class="room">Lab 201</span>
                    </li>
                </ul>
            </div>

            <!-- Academic Performance Widget -->
            <div class="gx-card widget-performance">
                <div class="card-header">
                    <h3>📊 Academic Performance</h3>
                </div>
                <div class="gpa-badge">
                    <span class="gpa-score">3.85</span>
                    <span class="gpa-label">Current Cumulative GPA</span>
                </div>
                <div class="performance-stats">
                    <div class="stat-item">
                        <span class="stat-val">A</span>
                        <span class="stat-lbl">Web Dev</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-val">A-</span>
                        <span class="stat-lbl">Databases</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-val">B+</span>
                        <span class="stat-lbl">Networks</span>
                    </div>
                </div>
            </div>

            <!-- Homework / Tasks Widget -->
            <div class="gx-card widget-tasks">
                <div class="card-header">
                    <h3>📝 Pending Tasks</h3>
                </div>
                <ul class="task-list">
                    <li>
                        <input type="checkbox" id="task1" checked>
                        <label for="task1">Submit PHP CRUD Project</label>
                    </li>
                    <li>
                        <input type="checkbox" id="task2">
                        <label for="task2">Database Midterm Review</label>
                    </li>
                    <li>
                        <input type="checkbox" id="task3">
                        <label for="task3">Read Chapter 4 (Networking)</label>
                    </li>
                </ul>
            </div>

        </div>

        <!-- User / Student Management Table -->
        <div class="table-header-action">
            <h2>User Management Dashboard</h2>
            <a href="create.php" class="btn-primary">+ Add New User</a>
        </div>
        
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