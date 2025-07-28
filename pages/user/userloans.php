<?php
require_once 'config/class.user.php';
$user = new USER();

if (!isset($_SESSION['userSession'])) {
    $user->redirect('index.php?page=relog&msg=signin_required');
    exit();
}

$uid = $_SESSION['userSession'];

$stmt = $user->runQuery("
    SELECT l.*, u.user_name, u.user_email, p.project_name 
    FROM loans l
    JOIN users u ON l.user_id = u.id
    LEFT JOIN projects p ON l.project_uniqid = p.uniqid
    WHERE l.user_id = :uid
    ORDER BY l.created_at DESC
");
$stmt->execute([':uid' => $uid]);
$loans = $stmt->fetchAll(PDO::FETCH_ASSOC);

$grouped = [
    'pending' => [],
    'approved' => [],
    'rejected' => [],
    'cancelled' => [],
    'due' => [],
    'paid' => [],
];

foreach ($loans as $loan) {
    $grouped[$loan['status']][] = $loan;
}
?>

<div class="container-fluid card shadow">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 mt-4">
            <h4><i class="fas fa-hand-holding-usd"></i> Your Loans</h4>
            <ul class="list-group">
                <?php foreach ($grouped as $status => $list): ?>
                    <li class="list-group-item">
                        <a href="#" onclick="showSection('<?= $status ?>')">
                            <i class="fas fa-<?= 
                                $status === 'approved' ? 'check-circle' : 
                                ($status === 'pending' ? 'clock' : 'times-circle')
                            ?> mr-2"></i>
                            <?= ucfirst($status) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <!-- Loan Content -->
        <div class="col-md-9 mt-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4><i class="fas fa-tasks"></i> Manage Loans</h4>
                <?php if (count($grouped['due']) > 0): ?>
                    <a href="index.php?page=payloan" class="btn btn-primary">
                        <i class="fas fa-money-bill-wave mr-1"></i> Pay Back Loan
                    </a>
                <?php else: ?>
                    <a href="index.php?page=dashboard&pages=userloans&action=request" class="btn btn-success">
                        <i class="fas fa-plus mr-1"></i> Request Loan
                    </a>
                <?php endif; ?>
            </div>

            <?php foreach ($grouped as $status => $items): ?>
                <div id="<?= $status ?>-section" class="loan-section" style="display: none;">
                    <h5 class="text-capitalize">
                        <i class="fas fa-<?= 
                            $status === 'approved' ? 'check-circle text-success' : 
                            ($status === 'pending' ? 'clock text-warning' : 'times-circle text-danger')
                        ?> mr-2"></i>
                        <?= $status ?> Loans
                    </h5>
                    
                    <?php if (empty($items)): ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle mr-2"></i> No <?= $status ?> loans found
                        </div>
                    <?php else: ?>
                        <div class="row">
                            <?php foreach ($items as $loan): ?>
                                <div class="col-md-6 mb-4">
                                    <div class="card h-100 shadow-sm">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between">
                                                <h5 class="card-title">
                                                    ৳<?= number_format($loan['loan_amount'], 2) ?>
                                                </h5>
                                                <span class="badge badge-<?= 
                                                    $status === 'rejected' || $status === 'cancelled' ? 'danger' : 
                                                    ($status === 'paid' ? 'success' : 'warning')
                                                ?>">
                                                    <?= ucfirst($loan['status']) ?>
                                                </span>
                                            </div>
                                            
                                            <p class="card-text"><strong><i class="fas fa-crosshairs mr-1"></i> Purpose:</strong> <?= htmlspecialchars($loan['purpose']) ?></p>
                                            <p><strong><i class="fas fa-calendar-day mr-1"></i> Requested:</strong> <?= date('M j, Y', strtotime($loan['created_at'])) ?></p>
                                            
                                            <?php if ($status === 'approved'): ?>
                                                <div class="mt-3">
                                                    <a href="index.php?page=dashboard&pages=userloans&action=viewreport&id=<?= $loan['id'] ?>" 
                                                       class="btn btn-block btn-info">
                                                       <i class="fas fa-file-invoice mr-2"></i> View Confirmation Report
                                                    </a>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<script>
function showSection(sectionId) {
    document.querySelectorAll('.loan-section').forEach(sec => {
        sec.style.display = 'none';
    });
    document.getElementById(sectionId + '-section').style.display = 'block';
    
    document.querySelectorAll('.list-group-item').forEach(item => {
        item.classList.remove('active');
    });
    event.currentTarget.parentElement.classList.add('active');
}

window.onload = () => {
    showSection('pending');
    document.querySelector('.list-group-item:first-child').classList.add('active');
};
</script>