<?php
// filepath: h:\xampp\htdocs\Web_Dev\GrowNet\admin\pages\viewLoan.php
require_once __DIR__ . '/../config/class.user.php';
$user = new USER();

$loanId = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$loanId) {
    echo "<div class='alert alert-danger'>Invalid loan ID.</div>";
    exit;
}

$stmt = $user->runQuery("
    SELECT loans.*, users.user_name, users.uniqid AS user_uniqid, projects.project_name
    FROM loans
    LEFT JOIN users ON loans.user_id = users.id
    LEFT JOIN projects ON loans.project_uniqid = projects.uniqid
    WHERE loans.id = :id
");
$stmt->execute([':id' => $loanId]);
$loan = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$loan) {
    echo "<div class='alert alert-danger'>Loan not found.</div>";
    exit;
}
?>

<h2>Loan Details</h2>
<table class="table table-bordered">
    <tr><th>Founder</th><td><?= htmlspecialchars($loan['user_name']) ?> (<?= htmlspecialchars($loan['user_uniqid']) ?>)</td></tr>
    <tr><th>Project</th><td><?= htmlspecialchars($loan['project_name'] ?? $loan['project_uniqid']) ?></td></tr>
    <tr><th>Amount</th><td><?= number_format($loan['loan_amount'], 2) ?> BDT</td></tr>
    <tr><th>Purpose</th><td><?= nl2br(htmlspecialchars($loan['purpose'])) ?></td></tr>
    <tr><th>Status</th><td><?= htmlspecialchars($loan['status']) ?></td></tr>
    <tr><th>Requested At</th><td><?= htmlspecialchars($loan['created_at']) ?></td></tr>
    <tr>
        <th>Documents</th>
        <td>
            <?php if ($loan['tin_certificate']): ?>
                <div class="mb-2"><strong>TIN Certificate:</strong></div>
                <embed src="../assets/doc/<?= htmlspecialchars($loan['tin_certificate']) ?>" type="application/pdf" width="100%" height="400px" />
                <hr>
            <?php endif; ?>
            <?php if ($loan['business_id_certificate']): ?>
                <div class="mb-2"><strong>Business ID Certificate:</strong></div>
                <embed src="../assets/doc/<?= htmlspecialchars($loan['business_id_certificate']) ?>" type="application/pdf" width="100%" height="400px" />
                <hr>
            <?php endif; ?>
            <?php if ($loan['national_id']): ?>
                <div class="mb-2"><strong>National ID:</strong></div>
                <embed src="../assets/doc/<?= htmlspecialchars($loan['national_id']) ?>" type="application/pdf" width="100%" height="400px" />
                <hr>
            <?php endif; ?>
        </td>
    </tr>
</table>
<a href="index.php?page=requestedLoans" class="btn btn-secondary">Back to Requested Loans</a>
