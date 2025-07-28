<?php
require_once __DIR__ . '/../config/class.user.php';
$user = new USER();

// Mark notifications as seen when viewing pending projects
if (isset($_GET['mark_seen'])) {
    $user->markProjectAsSeen($_GET['mark_seen']);
}
?>

<div class="container-fluid mt-5 pt-4">
    <div class="row">
        <div class="col-md-12">
            <h2>Active Projects</h2>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Project Name</th>
                            <th>Category</th>
                            <th>Owner ID</th>
                            <th>Shares</th>
                            <th>Total Amount</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $projects = $user->getProjectsByStatus('active');
                        foreach ($projects as $project): 
                            $category = $user->getCategoryById($project['categoryId']);
                            $owner = $user->getUserById($project['owner_id']);
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($project['project_name']) ?></td>
                            <td><?= htmlspecialchars($category['category_name']) ?></td>
                            <td><?= htmlspecialchars($owner['f_name']) ?></td>
                            <td><?= $project['shares'] ?></td>
                            <td><?= number_format($project['total_amount'], 2) ?></td>
                            <td>
                                <a href="index.php?page=viewProject&id=<?= $project['id'] ?>" class="btn btn-sm btn-primary">View</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
