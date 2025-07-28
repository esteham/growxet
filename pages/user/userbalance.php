<?php 
require_once 'config/class.user.php';
$user = new USER();

// Ensure user is logged in
if (!isset($_SESSION['userSession'])) {
    $user->redirect('index.php?page=relog&msg=signin_required');
    exit();
}

$userId = $_SESSION['userSession'];

// Fetch current user balance for validation (includes balance transfer ins and outs)
$balanceStmt = $user->runQuery("SELECT 
    COALESCE(SUM(CASE WHEN type IN ('deposit','investment_return','admin_adjustment','profit_transfer','balance_transfer_in') THEN amount ELSE 0 END) -
    SUM(CASE WHEN type IN ('withdraw','balance_transfer_out') THEN amount ELSE 0 END), 0) AS current_balance
    FROM balance_transactions WHERE user_id = :uid");
$balanceStmt->execute([':uid' => $userId]);
$balanceRow = $balanceStmt->fetch(PDO::FETCH_ASSOC);
$current_balance = $balanceRow['current_balance'] ?? 0;

// Fetch all banks for dropdowns
$banksStmt = $user->runQuery("SELECT bank_name FROM banks WHERE is_active = 1 ORDER BY bank_name ASC");
$banksStmt->execute();
$banks = $banksStmt->fetchAll(PDO::FETCH_ASSOC);

// Handle deposit form submission
if (isset($_POST['action']) && $_POST['action'] === 'deposit') {
    $amount = floatval($_POST['amount']);
    $bank_account = trim($_POST['bank_account']);
    $bank_name = trim($_POST['bank_name']);
    $currency = $_POST['currency'];
    $description = "Deposit from bank account $bank_account ($bank_name)";

    $payment_method = $_POST['payment_method'] ?? 'bank';
    $card_number = trim($_POST['card_number'] ?? '');
    $card_holder = trim($_POST['card_holder'] ?? '');
    $card_cvv = trim($_POST['card_cvv'] ?? '');
    $bkash_phone = trim($_POST['bkash_phone'] ?? '');
    $bkash_pin = trim($_POST['bkash_pin'] ?? '');
    $transaction_type = $_POST['transaction_type'] ?? 'deposit';

    if ($amount > 0 && $bank_account && $bank_name) {
        $insert = $user->runQuery("INSERT INTO balance_transactions 
            (user_id, type, amount, bank_account, bank_name, currency, description, sender_id, payment_method, card_number, card_holder, card_cvv, bkash_phone, bkash_pin) 
            VALUES (:uid, :type, :amt, :bank_acc, :bank_name, :currency, :desc, :uid, :payment_method, :card_number, :card_holder, :card_cvv, :bkash_phone, :bkash_pin)");
        $insert->execute([
            ':uid' => $userId,
            ':type' => $transaction_type,
            ':amt' => $amount,
            ':bank_acc' => $bank_account,
            ':bank_name' => $bank_name,
            ':currency' => $currency,
            ':desc' => $description,
            ':payment_method' => $payment_method,
            ':card_number' => $card_number,
            ':card_holder' => $card_holder,
            ':card_cvv' => $card_cvv,
            ':bkash_phone' => $bkash_phone,
            ':bkash_pin' => $bkash_pin
        ]); // <-- Add this closing bracket!
        $_SESSION['balance_msg'] = "Deposit request submitted.";
    } else {
        $_SESSION['balance_msg'] = "Please fill in all deposit fields correctly.";
    }
    header("Location: index.php?page=dashboard&pages=userbalance");
    exit;
}

// Handle withdrawal form submission
if (isset($_POST['action']) && $_POST['action'] === 'withdraw') {
    $amount = floatval($_POST['amount']);
    $bank_account = trim($_POST['bank_account']);
    $bank_name = trim($_POST['bank_name']);
    $currency = $_POST['currency'];
    $description = "Withdrawal to bank account $bank_account ($bank_name)";

    if ($amount > 0 && $bank_account && $bank_name) {
        $insert = $user->runQuery("INSERT INTO balance_transactions (user_id, type, amount, bank_account, bank_name, currency, description, sender_id) VALUES (:uid, 'withdraw', :amt, :bank_acc, :bank_name, :currency, :desc, :uid)");
        $insert->execute([
            ':uid' => $userId,
            ':amt' => $amount,
            ':bank_acc' => $bank_account,
            ':bank_name' => $bank_name,
            ':currency' => $currency,
            ':desc' => $description,
        ]);
        $_SESSION['balance_msg'] = "Withdrawal request submitted.";
    } else {
        $_SESSION['balance_msg'] = "Please fill in all withdrawal fields correctly.";
    }
    header("Location: index.php?page=dashboard&pages=userbalance");
    exit;
}

// Handle profit transfer by founder to investors
if (isset($_POST['action']) && $_POST['action'] === 'profit_transfer') {
    $amount = floatval($_POST['amount']);
    $currency = $_POST['currency'];
    $investor_account = trim($_POST['investor_account']);
    $investor_bank = trim($_POST['investor_bank']);
    $description = "Profit transfer to investor account $investor_account ($investor_bank)";

    // Add validation and permissions check for founder user_type here if needed
    if ($amount > 0 && $investor_account && $investor_bank) {
        $insert = $user->runQuery("INSERT INTO balance_transactions (user_id, type, amount, bank_account, bank_name, currency, description, sender_id) VALUES (:uid, 'profit_transfer', :amt, :bank_acc, :bank_name, :currency, :desc, :uid)");
        $insert->execute([
            ':uid' => $userId,
            ':amt' => $amount,
            ':bank_acc' => $investor_account,
            ':bank_name' => $investor_bank,
            ':currency' => $currency,
            ':desc' => $description,
        ]);
        $_SESSION['balance_msg'] = "Profit transfer recorded.";
    } else {
        $_SESSION['balance_msg'] = "Please fill in all profit transfer fields correctly.";
    }
    header("Location: index.php?page=dashboard&pages=userbalance");
    exit;
}

// Handle balance transfer from current user to another user
if (isset($_POST['action']) && $_POST['action'] === 'balance_transfer') {
    $amount = floatval($_POST['amount']);
    $currency = $_POST['currency'];
    $receiver_id = intval($_POST['receiver_user']);
    $receiver_bank_account = trim($_POST['receiver_bank_account']);
    $receiver_bank_name = trim($_POST['receiver_bank_name']);

    if ($amount > 0 && $receiver_id > 0 && $receiver_bank_account && $receiver_bank_name) {
        if ($amount > $current_balance) {
            $_SESSION['balance_msg'] = "Insufficient balance for transfer.";
        } else {
            // Fetch uniqid for sender and receiver
            $stmtSenderUniq = $user->runQuery("SELECT uniqid FROM users WHERE id = :id");
            $stmtSenderUniq->execute([':id' => $userId]);
            $senderUniq = $stmtSenderUniq->fetchColumn();

            $stmtReceiverUniq = $user->runQuery("SELECT uniqid FROM users WHERE id = :id");
            $stmtReceiverUniq->execute([':id' => $receiver_id]);
            $receiverUniq = $stmtReceiverUniq->fetchColumn();

            // Begin transaction to ensure atomic insert
            $pdo = $user->getConnection();
            try {
                $pdo->beginTransaction();

                // Deduct from sender: negative balance_transfer_out
                $desc_out = "Balance transfer to user ID $receiver_id, account $receiver_bank_account ($receiver_bank_name)";
                $stmtOut = $user->runQuery("INSERT INTO balance_transactions (user_id, type, amount, bank_account, bank_name, currency, description, sender_id, receiver_id) VALUES (:uid, 'admin_adjustment', :amt, NULL, 'GrowNet', :currency, :desc, :sender_uniqid, :receiver_uniqid)");
                $stmtOut->execute([
                    ':uid' => $userId,
                    ':amt' => -$amount,
                    ':currency' => $currency,
                    ':desc' => $desc_out,
                    ':sender_uniqid' => $senderUniq,
                    ':receiver_uniqid' => $receiverUniq
                ]);

                // Add to receiver: positive admin_adjustment
                $desc_in = "Balance received from user ID $userId";
                $stmtIn = $user->runQuery("INSERT INTO balance_transactions (user_id, type, amount, bank_account, bank_name, currency, description, sender_id, receiver_id) VALUES (:uid, 'admin_adjustment', :amt, NULL, 'GrowNet', :currency, :desc, :sender_uniqid, :receiver_uniqid)");
                $stmtIn->execute([
                    ':uid' => $receiver_id,
                    ':amt' => $amount,
                    ':currency' => $currency,
                    ':desc' => $desc_in,
                    ':sender_uniqid' => $senderUniq,
                    ':receiver_uniqid' => $receiverUniq
                ]);

                $pdo->commit();
                $_SESSION['balance_msg'] = "Balance transfer successful.";
            } catch (Exception $e) {
                $pdo->rollBack();
                $_SESSION['balance_msg'] = "Transfer failed: " . $e->getMessage();
            }
        }
    } else {
        $_SESSION['balance_msg'] = "Please fill in all balance transfer fields correctly.";
    }
    header("Location: index.php?page=dashboard&pages=userbalance");
    exit;
}

// Fetch balance transaction history
$historyStmt = $user->runQuery("SELECT * FROM balance_transactions WHERE user_id = :uid ORDER BY created_at DESC");
$historyStmt->execute([':uid' => $userId]);
$transactions = $historyStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all users for transfer receiver select (excluding current user)
$allUsersStmt = $user->runQuery("SELECT id, user_name FROM users WHERE id != :uid ORDER BY user_name ASC");
$allUsersStmt->execute([':uid' => $userId]);
$allUsers = $allUsersStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container-fluid card shadow">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 mt-4">
            <h4>Your Wallet Balance</h4>
            <ul class="list-group">
                <li class="list-group-item"><a href="#" onclick="showSection('history')">Transaction History</a></li>
                <li class="list-group-item"><a href="#" onclick="showSection('deposit')">Deposit</a></li>
                <li class="list-group-item"><a href="#" onclick="showSection('withdraw')">Withdraw</a></li>
                <?php if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'founder'): ?>
                <li class="list-group-item"><a href="#" onclick="showSection('profit_transfer')">Profit Transfer</a></li>
                <?php endif; ?>
                <li class="list-group-item"><a href="#" onclick="showSection('balance_transfer')">Balance Transfer</a></li>
            </ul>
            <div class="mt-3 p-3 border rounded bg-light">
                <h5>Current Balance</h5>
                <h3>৳ <?= number_format($current_balance, 2) ?></h3>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9 mt-4">
            <?php if (isset($_SESSION['balance_msg'])): ?>
                <div class="alert alert-info"><?= $_SESSION['balance_msg'] ?></div>
                <?php unset($_SESSION['balance_msg']); ?>
            <?php endif; ?>

            <!-- History Section -->
            <div id="history-section" class="balance-section">
                <h4>Transaction History</h4>
                <table class="table table-striped table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Currency</th>
                            <th>Sender ID</th>
                            <th>Bank Name</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($transactions): foreach ($transactions as $t):
                            // Fetch sender uniqid if available
                            $senderUniq = '-';
                            if (!empty($t['sender_id'])) {
                                $stmtSender = $user->runQuery("SELECT uniqid FROM users WHERE id = :id");
                                $stmtSender->execute([':id' => $t['sender_id']]);
                                $senderUniq = $stmtSender->fetchColumn() ?: '-';
                            }
                        ?>
<tr>
    <td><?= htmlspecialchars($t['created_at']) ?></td>
    <td><?= ucfirst(str_replace('_', ' ', $t['type'])) ?></td>
    <td><?= number_format($t['amount'], 2) ?></td>
    <td><?= htmlspecialchars(strtoupper($t['currency'])) ?></td>
    <td><?= htmlspecialchars($senderUniq) ?></td>
    <td><?= htmlspecialchars($t['bank_name']) ?></td>
    <td><?= htmlspecialchars($t['description']) ?></td>
</tr>
                        <?php endforeach; else: ?>
                            <tr><td colspan="7" class="text-center">No transactions found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Deposit Section -->
            <div id="deposit-section" class="balance-section" style="display:none;">
                <h4>Deposit Funds</h4>
                <form method="POST" action="index.php?page=dashboard&pages=userbalance">
                    <input type="hidden" name="action" value="deposit">
                    <div class="form-group">
                        <label>Transaction Type</label>
                        <select name="transaction_type" class="form-control" required>
                            <option value="deposit">Deposit</option>
                            <option value="withdraw">Withdraw</option>
                            <option value="balance_transfer">Balance Transfer</option>
                            <option value="profit_transfer">Profit Transfer</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Amount</label>
                        <input type="number" step="0.01" name="amount" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Payment Method</label>
                        <select name="payment_method" class="form-control payment-method-select" required onchange="togglePaymentFields(this)">
                            <option value="bank">Bank</option>
                            <option value="card">Card</option>
                            <option value="bkash">bKash</option>
                        </select>
                    </div>

                    <!-- Bank fields -->
                    <div class="bank-fields payment-fields">
                        <div class="form-group">
                            <label>Bank Account Number</label>
                            <input type="text" name="bank_account" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Bank Name</label>
                            <select name="bank_name" class="form-control">
                                <option value="">Select Bank</option>
                                <?php foreach ($banks as $bank): ?>
                                    <option value="<?= htmlspecialchars($bank['bank_name']) ?>"><?= htmlspecialchars($bank['bank_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Card fields -->
                    <div class="card-fields payment-fields" style="display:none;">
                        <div class="form-group">
                            <label>Card Number</label>
                            <input type="text" name="card_number" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Card Holder's Name</label>
                            <input type="text" name="card_holder" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>CVV</label>
                            <input type="text" name="card_cvv" class="form-control">
                        </div>
                    </div>

                    <!-- bKash fields -->
                    <div class="bkash-fields payment-fields" style="display:none;">
                        <div class="form-group">
                            <label>bKash Phone Number</label>
                            <input type="text" name="bkash_phone" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>bKash PIN</label>
                            <input type="password" name="bkash_pin" class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Currency</label>
                        <select name="currency" class="form-control" required>
                            <option value="bdt">BDT</option>
                            <option value="usd">USD</option>
                            <option value="eur">EUR</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit Deposit</button>
                </form>
            </div>

            <!-- Withdraw Section -->
            <div id="withdraw-section" class="balance-section" style="display:none;">
                <h4>Withdraw Funds</h4>
                <form method="POST" action="index.php?page=dashboard&pages=userbalance">
                    <input type="hidden" name="action" value="withdraw">
                    <div class="form-group">
                        <label>Amount</label>
                        <input type="number" step="0.01" name="amount" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Payment Method</label>
                        <select name="payment_method" class="form-control payment-method-select" required onchange="togglePaymentFields(this)">
                            <option value="bank">Bank</option>
                            <option value="card">Card</option>
                            <option value="bkash">bKash</option>
                        </select>
                    </div>

                    <!-- Bank fields -->
                    <div class="bank-fields payment-fields">
                        <div class="form-group">
                            <label>Bank Account Number</label>
                            <input type="text" name="bank_account" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Bank Name</label>
                            <select name="bank_name" class="form-control" required>
                                <option value="">Select Bank</option>
                                <?php foreach ($banks as $bank): ?>
                                    <option value="<?= htmlspecialchars($bank['bank_name']) ?>"><?= htmlspecialchars($bank['bank_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Card fields -->
                    <div class="card-fields payment-fields" style="display:none;">
                        <div class="form-group">
                            <label>Card Number</label>
                            <input type="text" name="card_number" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Card Holder's Name</label>
                            <input type="text" name="card_holder" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>CVV</label>
                            <input type="text" name="card_cvv" class="form-control">
                        </div>
                    </div>

                    <!-- bKash fields -->
                    <div class="bkash-fields payment-fields" style="display:none;">
                        <div class="form-group">
                            <label>bKash Phone Number</label>
                            <input type="text" name="bkash_phone" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>bKash PIN</label>
                            <input type="password" name="bkash_pin" class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Currency</label>
                        <select name="currency" class="form-control" required>
                            <option value="bdt">BDT</option>
                            <option value="usd">USD</option>
                            <option value="eur">EUR</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit Withdrawal</button>
                </form>
            </div>

            <!-- Profit Transfer Section (founder only) -->
            <?php if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'founder'): ?>
            <div id="profit_transfer-section" class="balance-section" style="display:none;">
                <h4>Profit Transfer to Investors</h4>
                <form method="POST" action="index.php?page=dashboard&pages=userbalance">
                    <input type="hidden" name="action" value="profit_transfer">
                    <div class="form-group">
                        <label>Amount</label>
                        <input type="number" step="0.01" name="amount" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Investor Bank Account Number</label>
                        <input type="text" name="investor_account" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Investor Bank Name</label>
                        <select name="investor_bank" class="form-control" required>
                            <option value="">Select Bank</option>
                            <?php foreach ($banks as $bank): ?>
                                <option value="<?= htmlspecialchars($bank['bank_name']) ?>"><?= htmlspecialchars($bank['bank_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Currency</label>
                        <select name="currency" class="form-control" required>
                            <option value="bdt">BDT</option>
                            <option value="usd">USD</option>
                            <option value="eur">EUR</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Transfer Profit</button>
                </form>
            </div>
            <?php endif; ?>

            <!-- Balance Transfer Section -->
            <div id="balance_transfer-section" class="balance-section" style="display:none;">
                <h4>Balance Transfer</h4>
                <form method="POST" action="index.php?page=dashboard&pages=userbalance">
                    <input type="hidden" name="action" value="balance_transfer">
                    <div class="form-group">
                        <label>Amount</label>
                        <input type="number" step="0.01" name="amount" class="form-control" required min="0.01" max="<?= htmlspecialchars($current_balance) ?>">
                    </div>
                    <div class="form-group">
                        <label>Recipient User</label>
                        <select name="receiver_user" class="form-control" required>
                            <option value="">Select User</option>
                            <?php foreach ($allUsers as $au): ?>
                                <option value="<?= $au['id'] ?>"><?= htmlspecialchars($au['user_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
    <label>Transfer Type</label>
    <select name="transfer_type" class="form-control transfer-type-select" required onchange="toggleTransferFields(this)">
        <option value="bank">Bank</option>
        <option value="card">Card</option>
        <option value="bkash">bKash</option>
        <option value="grownet">GrowNet Platform</option>
    </select>
</div>

<!-- Bank fields -->
<div class="bank-transfer-fields transfer-fields">
    <div class="form-group">
        <label>Recipient Bank Account Number</label>
        <input type="text" name="receiver_bank_account" class="form-control">
    </div>
    <div class="form-group">
        <label>Recipient Bank Name</label>
        <select name="receiver_bank_name" class="form-control">
            <option value="">Select Bank</option>
            <?php foreach ($banks as $bank): ?>
                <option value="<?= htmlspecialchars($bank['bank_name']) ?>"><?= htmlspecialchars($bank['bank_name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
</div>

<!-- Card fields -->
<div class="card-transfer-fields transfer-fields" style="display:none;">
    <div class="form-group">
        <label>Card Number</label>
        <input type="text" name="receiver_card_number" class="form-control">
    </div>
    <div class="form-group">
        <label>Card Holder's Name</label>
        <input type="text" name="receiver_card_holder" class="form-control">
    </div>
    <div class="form-group">
        <label>CVV</label>
        <input type="text" name="receiver_card_cvv" class="form-control">
    </div>
</div>

<!-- bKash fields -->
<div class="bkash-transfer-fields transfer-fields" style="display:none;">
    <div class="form-group">
        <label>bKash Phone Number</label>
        <input type="text" name="receiver_bkash_phone" class="form-control">
    </div>
    <div class="form-group">
        <label>bKash PIN</label>
        <input type="password" name="receiver_bkash_pin" class="form-control">
    </div>
</div>

<!-- GrowNet Platform fields -->
<div class="grownet-transfer-fields transfer-fields" style="display:none;">
    <div class="form-group">
        <label>Recipient User UniqID</label>
        <input type="text" name="receiver_user_uniqid" class="form-control" placeholder="Enter recipient's uniqid">
        <small class="text-muted">Ask the recipient for their uniqid (see profile).</small>
    </div>
</div>

                    <div class="form-group">
                        <label>Currency</label>
                        <select name="currency" class="form-control" required>
                            <option value="bdt">BDT</option>
                            <option value="usd">USD</option>
                            <option value="eur">EUR</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Transfer Balance</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function showSection(sectionId) {
    const sections = document.querySelectorAll('.balance-section');
    sections.forEach(sec => sec.style.display = 'none');
    const activeSection = document.getElementById(sectionId + '-section');
    if (activeSection) {
        activeSection.style.display = 'block';
    }
}

function togglePaymentFields(sel) {
    var form = sel.closest('form');
    var method = sel.value;
    form.querySelectorAll('.payment-fields').forEach(function(div) {
        div.style.display = 'none';
    });
    if (method === 'bank') {
        form.querySelector('.bank-fields').style.display = '';
    } else if (method === 'card') {
        form.querySelector('.card-fields').style.display = '';
    } else if (method === 'bkash') {
        form.querySelector('.bkash-fields').style.display = '';
    }
}

function toggleTransferFields(sel) {
    var form = sel.closest('form');
    var type = sel.value;
    form.querySelectorAll('.transfer-fields').forEach(function(div) {
        div.style.display = 'none';
    });
    if (type === 'bank') {
        form.querySelector('.bank-transfer-fields').style.display = '';
    } else if (type === 'card') {
        form.querySelector('.card-transfer-fields').style.display = '';
    } else if (type === 'bkash') {
        form.querySelector('.bkash-transfer-fields').style.display = '';
    } else if (type === 'grownet') {
        form.querySelector('.grownet-transfer-fields').style.display = '';
    }
}

document.querySelectorAll('.payment-method-select').forEach(function(sel) {
    sel.addEventListener('change', function() { togglePaymentFields(this); });
    togglePaymentFields(sel); // initialize on load
});

document.querySelectorAll('.transfer-type-select').forEach(function(sel) {
    sel.addEventListener('change', function() { toggleTransferFields(this); });
    toggleTransferFields(sel); // initialize on load
});

window.onload = () => {
    showSection('history');
    // Initialize payment fields visibility
    document.querySelectorAll('.payment-fields').forEach(field => field.style.display = 'none');
    // Initialize transfer fields visibility
    document.querySelectorAll('.transfer-fields').forEach(field => field.style.display = 'none');
};
</script>

<?php
$transfer_type = $_POST['transfer_type'] ?? 'bank';
$receiver_user_uniqid = trim($_POST['receiver_user_uniqid'] ?? '');

if ($transfer_type === 'grownet') {
    // Find user by uniqid
    $stmt = $user->runQuery("SELECT id FROM users WHERE uniqid = :uniqid");
    $stmt->execute([':uniqid' => $receiver_user_uniqid]);
    $receiver_id = $stmt->fetchColumn();

    if (!$receiver_id) {
        $_SESSION['balance_msg'] = "Recipient uniqid not found.";
        header("Location: index.php?page=dashboard&pages=userbalance");
        exit;
    }
    // Continue with transfer logic using $receiver_id
}
?>
