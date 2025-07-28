<?php
require_once 'config/class.user.php';
$user = new USER();

// error_reporting(E_ALL);
// ini_set('display_errors', 1);

// Block/Unblock logic
if (isset($_GET['action'], $_GET['id'])) {
    $userId = intval($_GET['id']);
    $action = $_GET['action'] === 'block' ? 'blocked' : 'active';
    $stmt = $user->runQuery("UPDATE users SET account_status=? WHERE id=?");
    $stmt->execute([$action, $userId]);
    header("Location: allUsers.php");
    exit;
}

// Fetch all users except admin
$stmt = $user->runQuery("SELECT * FROM users WHERE user_name != 'admin'");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

function getCount($user, $table, $userId) {
    if ($table === 'projects') {
        $stmt = $user->runQuery("SELECT COUNT(*) FROM projects WHERE owner_id=?");
    } elseif ($table === 'share_orders') {
        $stmt = $user->runQuery("SELECT COUNT(DISTINCT project_id) FROM share_orders WHERE user_id=?");
    } elseif ($table === 'balance_transactions') {
        $stmt = $user->runQuery("SELECT COUNT(*) FROM balance_transactions WHERE user_id=? AND type='donation'");
    } else {
        return 0;
    }
    $stmt->bindParam(1, $userId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchColumn();
}
?>

<div class="container mt-4">
    <h2>All Users</h2>
    <table class="table table-bordered table-hover mt-3">
        <thead class="thead-dark">
            <tr>
                <th>#</th>
                <th>User Name</th>
                <th>Full Name</th>
                <th>User Types</th>
                <th>Founded Projects</th>
                <th>Invested Projects</th>
                <th>Donations</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($users && count($users) > 0): $i = 1; ?>
            <?php foreach($users as $row): ?>
                <tr>
                    <td><?= $i++ ?></td>
                    <td><?= htmlspecialchars($row['user_name']) ?></td>
                    <td><?= htmlspecialchars($row['f_name']) ?></td>
                    <td>
                        <?php
                            $types = $user->getUserTypes($row['id']);
                            echo $types ? implode(', ', $types) : '<span class="text-muted">None</span>';
                        ?>
                    </td>
                    <td><?= getCount($user, 'projects', $row['id']) ?></td>
                    <td><?= getCount($user, 'share_orders', $row['id']) ?></td>
                    <td><?= getCount($user, 'balance_transactions', $row['id']) ?></td>
                    <td>
                        <?php if ($row['account_status'] === 'active'): ?>
                            <span class="badge badge-success">Active</span>
                        <?php else: ?>
                            <span class="badge badge-danger">Blocked</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($row['account_status'] === 'active'): ?>
                            <a href="?action=block&id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Block this user?')">Block</a>
                        <?php else: ?>
                            <a href="?action=unblock&id=<?= $row['id'] ?>" class="btn btn-sm btn-success" onclick="return confirm('Unblock this user?')">Unblock</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="9" class="text-center">No users found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>