<?php
require_once __DIR__ . '/../config/class.user.php';
$user = new USER();

// Handle actions
if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'approve':
            if (isset($_GET['id']) && $user->approveProject($_GET['id'])) {
                $_SESSION['success_msg'] = "Project approved successfully";
            } else {
                $_SESSION['error_msg'] = "Failed to approve project";
            }
            header("Location: index.php?page=pendingProjects");
            exit;

        case 'reject':
            $projectId = $_POST['id'] ?? $_GET['id'] ?? null;
            $reason = $_POST['rejection_reason'] ?? '';
            if ($projectId && $user->rejectProject($projectId, $reason)) {
                $_SESSION['success_msg'] = "Project rejected successfully";
            } else {
                $_SESSION['error_msg'] = "Failed to reject project";
            }
            header("Location: index.php?page=pendingProjects");
            exit;

        case 'mark_seen':
            if (isset($_GET['id'])) {
                $user->markProjectAsSeen($_GET['id']);
            }
            break;
    }
}

// Get pending projects
$projects = $user->getProjectsByStatus('pending');
?>

<div class="container-fluid mt-5 pt-4">
    <div class="row">
        <div class="col-md-12">
            <?php if (isset($_SESSION['success_msg'])): ?>
                <div class="alert alert-success"><?= $_SESSION['success_msg'] ?></div>
                <?php unset($_SESSION['success_msg']); ?>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error_msg'])): ?>
                <div class="alert alert-danger"><?= $_SESSION['error_msg'] ?></div>
                <?php unset($_SESSION['error_msg']); ?>
            <?php endif; ?>
            
            <h2>Pending Projects</h2>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Project Name</th>
                            <th>Category</th>
                            <th>Owner</th>
                            <th>Shares</th>
                            <th>Total Amount</th>
                            <th>Date Submitted</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($projects as $project): 
                            $category = $user->getCategoryById($project['categoryId']);
                            $owner = $user->getUserById($project['owner_id']);
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($project['project_name']) ?></td>
                            <td><?= htmlspecialchars($category['category_name']) ?></td>
                            <td>
                                <?= htmlspecialchars($owner['f_name']) ?>
                                <small class="text-muted d-block"><?= htmlspecialchars($owner['uniqid']) ?></small>
                            </td>
                            <td><?= $project['shares'] ?></td>
                            <td><?= number_format($project['total_amount'], 2) ?></td>
                            <td><?= date('M d, Y', strtotime($project['created_at'])) ?></td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="index.php?page=viewProject&id=<?= $project['id'] ?>" 
                                       class="btn btn-sm btn-secondary" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    <a href="index.php?page=pendingProjects&action=approve&id=<?= $project['id'] ?>" 
                                       class="btn btn-sm btn-success" title="Approve"
                                       onclick="return confirm('Are you sure you want to approve this project?')">
                                        <i class="fas fa-check"></i>
                                    </a>
                                    
                                    <button type="button" class="btn btn-sm btn-warning reject-btn" 
                                            title="Reject" data-id="<?= $project['id'] ?>">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Rejection Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="post" action="index.php?page=pendingProjects&action=reject">
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectModalLabel">Reject Project</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="reject_project_id" value="">
                    <div class="form-group">
                        <label for="rejection_reason">Reason for Rejection</label>
                        <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Confirm Rejection</button>
                </div>
            </form>
        </div>
    </div>
</div>










