<?php
require_once 'config/class.user.php';
$user = new USER();

if (!isset($_SESSION['userSession'])) {
    $user->redirect('index.php?page=relog&msg=signin_required');
    exit();
}

// Verify loan ID and ownership
$loanId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$uid = $_SESSION['userSession'];

$stmt = $user->runQuery("
    SELECT l.*, u.user_name, u.user_email, p.project_name 
    FROM loans l
    JOIN users u ON l.user_id = u.id
    LEFT JOIN projects p ON l.project_uniqid = p.uniqid
    WHERE l.id = :id AND l.user_id = :uid
");
$stmt->execute([':id' => $loanId, ':uid' => $uid]);
$loan = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$loan) {
    echo "<div class='alert alert-danger m-4'><i class='fas fa-exclamation-triangle mr-2'></i> Loan not found or access denied</div>";
    exit;
}
?>

<div class="container mt-4">
    <div class="card shadow-lg border-0">
        <!-- Report Header -->
        <div class="card-header bg-success text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="mb-0">
                    <i class="fas fa-file-contract mr-2"></i>
                    Loan Confirmation Report
                </h3>
                <a href="index.php?page=dashboard&pages=userloans" class="btn btn-light btn-sm">
                    <i class="fas fa-arrow-left mr-1"></i> Back to Loans
                </a>
            </div>
        </div>
        
        <div class="card-body bg-light">
            <!-- Loan Summary -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card h-100 border-success">
                        <div class="card-body">
                            <h5 class="card-title text-success">
                                <i class="fas fa-user-tie mr-2"></i> Borrower Details
                            </h5>
                            <hr class="border-success">
                            <p><strong>Name:</strong> <?= htmlspecialchars($loan['user_name']) ?></p>
                            <p><strong>Email:</strong> <?= htmlspecialchars($loan['user_email']) ?></p>
                            <p><strong>Loan ID:</strong> LN-<?= str_pad($loan['id'], 6, '0', STR_PAD_LEFT) ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card h-100 border-success">
                        <div class="card-body">
                            <h5 class="card-title text-success">
                                <i class="fas fa-project-diagram mr-2"></i> Project Details
                            </h5>
                            <hr class="border-success">
                            <?php if (!empty($loan['project_name'])): ?>
                                <p><strong>Project:</strong> <?= htmlspecialchars($loan['project_name']) ?></p>
                                <p><strong>Project ID:</strong> <?= htmlspecialchars($loan['project_uniqid']) ?></p>
                            <?php else: ?>
                                <p class="text-muted"><i class="fas fa-info-circle mr-2"></i> No associated project</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Loan Terms -->
            <div class="card mb-4 border-secondary">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-file-signature mr-2"></i>
                        Loan Terms & Conditions
                    </h5>
                </div>
                <div class="card-body bg-white">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong><i class="fas fa-money-bill-wave mr-2"></i> Amount:</strong> 
                               ৳<?= number_format($loan['loan_amount'], 2) ?></p>
                            <p><strong><i class="fas fa-percentage mr-2"></i> Interest Rate:</strong> 
                               10% per annum</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong><i class="fas fa-calendar-alt mr-2"></i> Term:</strong> 
                               12 months</p>
                            <p><strong><i class="fas fa-exclamation-triangle mr-2"></i> Late Fee:</strong> 
                               2% per month</p>
                        </div>
                    </div>
                    <div class="mt-3 p-3 bg-light rounded">
                        <p class="mb-0"><strong><i class="fas fa-info-circle mr-2"></i> Note:</strong> 
                           The disbursed amount will reflect in your account within 3-5 business days.</p>
                    </div>
                </div>
            </div>
            
            <!-- Documents -->
            <div class="card mb-4 border-info">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-file-upload mr-2"></i>
                        Submitted Documents
                    </h5>
                </div>
                <div class="card-body bg-white">
                    <div class="d-flex flex-wrap gap-2">
                        <?php if ($loan['tin_certificate']): ?>
                            <a href="../../assets/doc/<?= htmlspecialchars($loan['tin_certificate']) ?>" 
                               target="_blank" class="btn btn-outline-success">
                               <i class="fas fa-file-pdf mr-1"></i> TIN Certificate
                            </a>
                        <?php endif; ?>
                        
                        <?php if ($loan['business_id_certificate']): ?>
                            <a href="../../assets/doc/<?= htmlspecialchars($loan['business_id_certificate']) ?>" 
                               target="_blank" class="btn btn-outline-success">
                               <i class="fas fa-file-pdf mr-1"></i> Business ID
                            </a>
                        <?php endif; ?>
                        
                        <?php if ($loan['national_id']): ?>
                            <a href="../../assets/doc/<?= htmlspecialchars($loan['national_id']) ?>" 
                               target="_blank" class="btn btn-outline-success">
                               <i class="fas fa-file-pdf mr-1"></i> National ID
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Important Notes -->
            <div class="alert alert-warning border-warning">
                <h5 class="text-dark"><i class="fas fa-exclamation-circle mr-2"></i> Important Notes</h5>
                <ul class="mb-0">
                    <li class="text-dark">Repayments should be made by the 5th of each month</li>
                    <li class="text-dark">Late payments will affect your credit rating</li>
                    <li class="text-dark">Contact support@grownet.com for any queries</li>
                </ul>
            </div>
        </div>
        
        <div class="card-footer bg-success text-white text-center">
            <small><i class="fas fa-calendar-check mr-2"></i>Report generated on <?= date('F j, Y \a\t g:i A') ?></small>
        </div>
    </div>
</div>