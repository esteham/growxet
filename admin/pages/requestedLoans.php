<?php
require_once __DIR__ . '/../config/class.user.php';
$user = new USER();

// Fetch all requested (pending) loans
$stmt = $user->runQuery("
    SELECT 
        loans.id, loans.loan_amount, loans.purpose, loans.created_at, loans.project_uniqid, 
        loans.tin_certificate, loans.business_id_certificate, loans.national_id,
        loans.status, loans.user_id, users.user_name, users.uniqid AS user_uniqid, projects.project_name,
        users.user_email
    FROM loans
    LEFT JOIN users ON loans.user_id = users.id
    LEFT JOIN projects ON loans.project_uniqid = projects.uniqid
    WHERE loans.status = 'pending'
    ORDER BY loans.created_at DESC
");
$stmt->execute();
$loans = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Requested Loans</h2>
<?php if (empty($loans)): ?>
    <div class="alert alert-info">No requested loans found.</div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-bordered table-hover table-sm align-middle">
            <thead class="thead-dark">
                <tr>
                    <th>#</th>
                    <th>Founder</th>
                    <th>Project</th>
                    <th>Amount</th>
                    <th>Purpose</th>
                    <th>Documents</th>
                    <th>Requested At</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($loans as $i => $loan): ?>
                <tr>
                    <td><?= $i+1 ?></td>
                    <td>
                        <?= htmlspecialchars($loan['user_name']) ?><br>
                        <small class="text-muted"><?= htmlspecialchars($loan['user_uniqid']) ?></small>
                    </td>
                    <td>
                        <?= htmlspecialchars($loan['project_name'] ?? $loan['project_uniqid']) ?><br>
                        <small class="text-muted"><?= htmlspecialchars($loan['project_uniqid']) ?></small>
                    </td>
                    <td><?= number_format($loan['loan_amount'], 2) ?> BDT</td>
                    <td><?= nl2br(htmlspecialchars($loan['purpose'])) ?></td>
                    <td>
                        <?php if ($loan['tin_certificate']): ?>
                            <a href="../../uploads/loans/<?= htmlspecialchars($loan['tin_certificate']) ?>" target="_blank">TIN</a>
                        <?php endif; ?>
                        <?php if ($loan['business_id_certificate']): ?>
                            | <a href="../../uploads/loans/<?= htmlspecialchars($loan['business_id_certificate']) ?>" target="_blank">Business ID</a>
                        <?php endif; ?>
                        <?php if ($loan['national_id']): ?>
                            | <a href="../../uploads/loans/<?= htmlspecialchars($loan['national_id']) ?>" target="_blank">NID</a>
                        <?php endif; ?>
                    </td>
                    <td><?= date('Y-m-d H:i', strtotime($loan['created_at'])) ?></td>
                    <td>
                        <a href="index.php?page=viewLoan&id=<?= $loan['id'] ?>" class="btn btn-info btn-sm mb-1">View</a>
                        <a href="index.php?page=requestedLoans&action=approve&id=<?= $loan['id'] ?>" class="btn btn-success btn-sm mb-1" onclick="return confirm('Approve this loan request?')">Approve</a>
                        <a href="index.php?page=requestedLoans&action=decline&id=<?= $loan['id'] ?>" class="btn btn-danger btn-sm mb-1" onclick="return confirm('Decline this loan request?')">Decline</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php
// Handle approve/decline/view actions
if (isset($_GET['action'], $_GET['id']) && is_numeric($_GET['id'])) {
    $loanId = intval($_GET['id']);
    
    if ($_GET['action'] === 'approve') {
        // Define loan terms
        $interestRate = 10; // Annual interest rate in percentage
        $repaymentPeriodMonths = 12; // Default repayment period in months
        $processingFeePercentage = 1.5; // Processing fee percentage
        
        // Get loan details before approval
        $stmt = $user->runQuery("
            SELECT l.*, u.user_name, u.user_email, p.project_name 
            FROM loans l
            JOIN users u ON l.user_id = u.id
            LEFT JOIN projects p ON l.project_uniqid = p.uniqid
            WHERE l.id = :id
        ");
        $stmt->execute([':id' => $loanId]);
        $loanDetails = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($loanDetails) {
            // Calculate total amount with interest and fees
            $loanAmount = $loanDetails['loan_amount'];
            $processingFee = ($loanAmount * $processingFeePercentage) / 100;
            $totalInterest = ($loanAmount * $interestRate / 100) * ($repaymentPeriodMonths / 12);
            $totalAmountDue = $loanAmount + $totalInterest + $processingFee;
            $monthlyPayment = $totalAmountDue / $repaymentPeriodMonths;
            
            // Calculate dates
            $approvalDate = date('Y-m-d H:i:s');
            $disbursementDate = date('Y-m-d H:i:s', strtotime('+3 days'));
            $dueDate = date('Y-m-d', strtotime("+$repaymentPeriodMonths months"));
            
            try {
                // Begin transaction
                $user->beginTransaction();
                
                // Update loan status to approved and add repayment details
                $stmt = $user->runQuery("
                    UPDATE loans 
                    SET 
                        status = 'approved',
                        approved_by = :adminId,
                        approved_at = :approvalDate,
                        interest_rate = :interestRate,
                        processing_fee_percent = :processingFeePercent,
                        due_amount = :totalAmountDue,
                        monthly_payment = :monthlyPayment,
                        repayment_period_months = :repaymentPeriod,
                        disbursement_date = :disbursementDate,
                        due_date = :dueDate
                    WHERE id = :id
                ");
                $stmt->execute([
                    ':adminId' => $_SESSION['userSession'],
                    ':approvalDate' => $approvalDate,
                    ':interestRate' => $interestRate,
                    ':processingFeePercent' => $processingFeePercentage,
                    ':totalAmountDue' => $totalAmountDue,
                    ':monthlyPayment' => $monthlyPayment,
                    ':repaymentPeriod' => $repaymentPeriodMonths,
                    ':disbursementDate' => $disbursementDate,
                    ':dueDate' => $dueDate,
                    ':id' => $loanId
                ]);
                
                // Create repayment schedule
                $stmt = $user->runQuery("
                    INSERT INTO loan_repayments 
                    (loan_id, due_date, amount, status, created_at) 
                    VALUES 
                    (:loanId, :dueDate, :amount, 'pending', NOW())
                ");
                $stmt->execute([
                    ':loanId' => $loanId,
                    ':dueDate' => $dueDate,
                    ':amount' => $totalAmountDue
                ]);
                
                // Commit transaction
                $user->commit();
                
                // Send approval email with loan terms
                $to = $loanDetails['user_email'];
                $subject = "Your Loan Request Has Been Approved - GrowNet";
                
                $message = "
                <html>
                <head>
                    <style>
                        body { font-family: Arial, sans-serif; line-height: 1.6; }
                        .header { background-color: #4CAF50; color: white; padding: 20px; text-align: center; }
                        .content { padding: 20px; }
                        .signature { margin-top: 30px; border-top: 1px solid #ddd; padding-top: 20px; }
                        .terms { margin-top: 20px; background-color: #f9f9f9; padding: 15px; border-left: 4px solid #4CAF50; }
                    </style>
                </head>
                <body>
                    <div class='header'>
                        <h2>Loan Approval Confirmation</h2>
                    </div>
                    <div class='content'>
                        <p>Dear " . htmlspecialchars($loanDetails['user_name']) . ",</p>
                        
                        <p>We are pleased to inform you that your loan request for <strong>" . number_format($loanAmount, 2) . " BDT</strong> " . 
                        (!empty($loanDetails['project_name']) ? "for project <strong>" . htmlspecialchars($loanDetails['project_name']) . "</strong>" : "") . 
                        " has been approved by GrowNet.</p>
                        
                        <div class='terms'>
                            <h3>Loan Terms & Conditions</h3>
                            <ul>
                                <li><strong>Loan Amount:</strong> " . number_format($loanAmount, 2) . " BDT</li>
                                <li><strong>Interest Rate:</strong> $interestRate% per annum</li>
                                <li><strong>Processing Fee:</strong> " . number_format($processingFee, 2) . " BDT ($processingFeePercentage% of loan amount)</li>
                                <li><strong>Total Interest:</strong> " . number_format($totalInterest, 2) . " BDT</li>
                                <li><strong>Total Amount Due:</strong> " . number_format($totalAmountDue, 2) . " BDT</li>
                                <li><strong>Repayment Period:</strong> $repaymentPeriodMonths months</li>
                                <li><strong>Monthly Payment:</strong> " . number_format($monthlyPayment, 2) . " BDT</li>
                                <li><strong>Disbursement Date:</strong> " . date('F j, Y', strtotime($disbursementDate)) . "</li>
                                <li><strong>Due Date:</strong> " . date('F j, Y', strtotime($dueDate)) . "</li>
                                <li><strong>Repayment Methods:</strong> Bank transfer, bKash, or GrowNet wallet</li>
                            </ul>
                        </div>
                        
                        <p>The approved amount will be disbursed to your account within 3 business days.</p>
                        
                        <div class='signature'>
                            <p><strong>GrowNet Finance Team</strong></p>
                            <img src='https://grownet.com/assets/images/signature.png' alt='GrowNet Signature' width='150'><br>
                            <small>This is an automated message. Please do not reply directly to this email.</small>
                        </div>
                    </div>
                </body>
                </html>
                ";
                
                // Use the USER class's sendMail method instead of mail()
                $user->sendMail($to, $message, $subject);
                
                echo "<script>location.href='index.php?page=requestedLoans';</script>";
                exit;
                
            } catch (Exception $e) {
                $user->rollBack();
                error_log("Loan approval error: " . $e->getMessage());
                echo "<script>alert('Error approving loan: " . addslashes($e->getMessage()) . "');</script>";
            }
        }
        
    } elseif ($_GET['action'] === 'decline') {
        $stmt = $user->runQuery("UPDATE loans SET status='rejected', approved_by=:adminId WHERE id=:id");
        $stmt->execute([':adminId' => $_SESSION['userSession'], ':id' => $loanId]);
        echo "<script>location.href='index.php?page=requestedLoans';</script>";
        exit;
    } elseif ($_GET['action'] === 'view') {
        echo "<script>alert('Loan ID: $loanId\\nAdd your detailed view logic here.');</script>";
    }
}
?>