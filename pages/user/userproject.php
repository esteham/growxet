<?php 
require_once 'config/class.user.php';
$user = new USER();

// Ensure user is logged in
if (!isset($_SESSION['userSession'])) {
    $user->redirect('index.php?page=relog&msg=signin_required');
    exit();
}

$userId = $_SESSION['userSession'];

// Get user type(s)
$typeStmt = $user->runQuery("
    SELECT ut.type_name 
    FROM user_types ut
    INNER JOIN user_type_map utm ON ut.id = utm.type_id
    WHERE utm.user_id = :uid
");
$typeStmt->execute([':uid' => $userId]);
$userTypes = $typeStmt->fetchAll(PDO::FETCH_COLUMN);
$isFounder = in_array('founder', $userTypes);
$isInvestor = in_array('investor', $userTypes);
$isPartner = in_array('partner', $userTypes);
$isDonator = in_array('donator', $userTypes);

// Project type filter
$typeFilter = isset($_GET['project_type']) ? $_GET['project_type'] : '';
$typeList = ['long_term', 'short_term'];

// Build WHERE clause for project type filter
$typeWhere = '';
$typeParams = [];
if ($typeFilter && in_array($typeFilter, $typeList)) {
    $typeWhere = " AND project_type = :ptype ";
    $typeParams[':ptype'] = $typeFilter;
}

// Fetch all active projects for all users (for all user types)
$activeStmt = $user->runQuery("SELECT * FROM projects WHERE project_status = 'active' $typeWhere ORDER BY created_at DESC");
$activeStmt->execute($typeParams);
$allActiveProjects = $activeStmt->fetchAll(PDO::FETCH_ASSOC);

// For founder: fetch own projects by status
$founderProjects = [
    'active' => [],
    'closed' => [],
    'pending' => [],
    'declined' => [],
];
if ($isFounder) {
    $founderStmt = $user->runQuery("SELECT * FROM projects WHERE owner_id = :uid $typeWhere ORDER BY created_at DESC");
    $founderStmt->execute(array_merge([':uid' => $userId], $typeParams));
    foreach ($founderStmt->fetchAll(PDO::FETCH_ASSOC) as $p) {
        $status = strtolower($p['project_status']);
        if (isset($founderProjects[$status])) {
            $founderProjects[$status][] = $p;
        }
    }
}

// For investor/partner/donator: fetch invested projects (active/closed)
$investedProjects = [
    'active' => [],
    'closed' => [],
];
if ($isInvestor || $isPartner || $isDonator) {
    $investedStmt = $user->runQuery("
        SELECT p.* FROM projects p
        INNER JOIN share_orders so ON so.project_id = p.id
        WHERE so.user_id = :uid $typeWhere
        GROUP BY p.id
        ORDER BY p.created_at DESC
    ");
    $investedStmt->execute(array_merge([':uid' => $userId], $typeParams));
    foreach ($investedStmt->fetchAll(PDO::FETCH_ASSOC) as $p) {
        $status = strtolower($p['project_status']);
        if (isset($investedProjects[$status])) {
            $investedProjects[$status][] = $p;
        }
    }
}
?>

<div class="container-fluid card shadow">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 mt-4">
            <h4>Your Projects</h4>
            <ul class="list-group mb-3">
                <li class="list-group-item"><a href="?page=dashboard&pages=userproject">All Active Projects</a></li>
                <?php if ($isFounder): ?>
                    <li class="list-group-item"><a href="#" onclick="showSection('founder-active')">My Active Projects</a></li>
                    <li class="list-group-item"><a href="#" onclick="showSection('founder-closed')">My Closed Projects</a></li>
                    <li class="list-group-item"><a href="#" onclick="showSection('founder-pending')">My Pending Projects</a></li>
                    <li class="list-group-item"><a href="#" onclick="showSection('founder-declined')">My Declined Projects</a></li>
                <?php endif; ?>
                <?php if ($isInvestor || $isPartner || $isDonator): ?>
                    <li class="list-group-item"><a href="#" onclick="showSection('invested-active')">Invested Active Projects</a></li>
                    <li class="list-group-item"><a href="#" onclick="showSection('invested-closed')">Invested Closed Projects</a></li>
                <?php endif; ?>
            </ul>
            <?php if ($isFounder): ?>
                <a href="index.php?page=dashboard&pages=userproject&action=apply" class="btn btn-success w-100 mb-2">+ Apply New Project</a>
            <?php endif; ?>
        </div>

        <!-- Main Content -->
        <div class="col-md-9 mt-4">
            <!-- Project Type Filter Bar -->
            <form method="get" class="mb-3 d-flex align-items-center gap-2">
                <input type="hidden" name="page" value="dashboard">
                <input type="hidden" name="pages" value="userproject">
                <label class="mr-2"><strong>Project Type:</strong></label>
                <select name="project_type" class="form-control w-auto" onchange="this.form.submit()">
                    <option value="">All</option>
                    <?php foreach ($typeList as $type): ?>
                        <option value="<?= $type ?>" <?= $typeFilter === $type ? 'selected' : '' ?>>
                            <?= ucfirst(str_replace('-', ' ', $type)) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>

            <!-- All Active Projects (for all users) -->
            <div id="all-active-section" class="project-section">
                <h5>All Active Projects</h5>
                <div class="row">
                <?php foreach ($allActiveProjects as $p): 
                    $imagePath = "admin/assets/projectimg/" . $p['project_img'];
                    $project_id = $p['id'];
                    $total_shares = (int)$p['shares'];
                    $orderStmt = $user->runQuery("SELECT SUM(shares_bought) AS total_sold FROM share_orders WHERE project_id = :pid");
                    $orderStmt->execute([':pid' => $project_id]);
                    $orderData = $orderStmt->fetch(PDO::FETCH_ASSOC);
                    $sold_shares = (int)($orderData['total_sold'] ?? 0);
                    $left_shares = $total_shares - $sold_shares;
                    $shareBadge = ($left_shares <= 0) 
                        ? '<span class="badge bg-secondary">No Shares Left</span>' 
                        : '<span class="badge bg-warning text-dark">'.$left_shares.' / '.$total_shares.' shares left</span>';
                    $percentage = ($total_shares > 0) ? round(($sold_shares / $total_shares) * 100) : 0;
                    $campaignStart = $p['campaign_start'] ? date('M d, Y', strtotime($p['campaign_start'])) : 'N/A';
                    $campaignEnd = $p['campaign_end'] ? date('M d, Y', strtotime($p['campaign_end'])) : 'N/A';
                    $durationText = ($p['campaign_start'] && $p['campaign_end'])
                        ? "<strong>Duration:</strong> $campaignStart - $campaignEnd"
                        : "<strong>Duration:</strong> N/A";
                ?>
                    <div class="col-md-6 mb-4">
                        <div class="card h-100 shadow-sm">
                            <img src="<?= $imagePath ?>" class="card-img-top" style="height: 180px; object-fit: cover;">
                            <div class="card-body">
                                <h5 class="card-title d-flex justify-content-between align-items-start">
                                    <div>
                                        <?= htmlspecialchars($p['project_name']) ?><br>
                                        <small class="text-muted">#<?= htmlspecialchars($p['uniqid']) ?></small>
                                    </div>
                                    <?= $shareBadge ?>
                                </h5>
                                <p class="card-text"><?= substr(strip_tags($p['description']), 0, 100) ?>...</p>
                                <div class="progress mb-2" style="height: 20px;">
                                    <div class="progress-bar bg-dark" role="progressbar" style="width: <?= $percentage ?>%;" aria-valuenow="<?= $percentage ?>" aria-valuemin="0" aria-valuemax="100">
                                        <?= $percentage ?>%
                                    </div>
                                </div>
                                <p><strong>Price per Share:</strong> ৳<?= number_format($p['price_per_share'], 2) ?></p>
                                <p><strong>Total Amount:</strong> ৳<?= number_format($p['total_amount'], 2) ?></p>
                                <p><?= $durationText ?></p>
                                <span class="badge bg-dark text-light"><?= ucfirst($p['project_status']) ?></span>
                                
                                <?php if ($left_shares > 0): ?>
                                    <form method="post" action="pages/user/investProject.php" class="mt-2 d-flex align-items-center gap-2" onsubmit="return confirmInvest(this);">
                                        <input type="hidden" name="project_id" value="<?= $project_id ?>">
                                        <input type="number" name="shares" min="1" max="<?= $left_shares ?>" value="1" class="form-control w-25" style="min-width:80px;" required>
                                        <button type="submit" class="btn btn-success btn-sm ml-2">Invest</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                </div>
            </div>

            <!-- Founder Sections -->
            <?php if ($isFounder): ?>
                <?php foreach (['active','closed','pending','declined'] as $status): ?>
                <div id="founder-<?= $status ?>-section" class="project-section" style="display:none;">
                    <h5>My <?= ucfirst($status) ?> Projects</h5>
                    <div class="row">
                    <?php foreach ($founderProjects[$status] as $p): 
                        $imagePath = "admin/assets/projectimg/" . $p['project_img'];
                        $project_id = $p['id'];
                        $total_shares = (int)$p['shares'];
                        $orderStmt = $user->runQuery("SELECT SUM(shares_bought) AS total_sold FROM share_orders WHERE project_id = :pid");
                        $orderStmt->execute([':pid' => $project_id]);
                        $orderData = $orderStmt->fetch(PDO::FETCH_ASSOC);
                        $sold_shares = (int)($orderData['total_sold'] ?? 0);
                        $left_shares = $total_shares - $sold_shares;
                        $shareBadge = ($left_shares <= 0) 
                            ? '<span class="badge bg-secondary">No Shares Left</span>' 
                            : '<span class="badge bg-warning text-dark">'.$left_shares.' / '.$total_shares.' shares left</span>';
                        $percentage = ($total_shares > 0) ? round(($sold_shares / $total_shares) * 100) : 0;
                    ?>
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 shadow-sm">
                                <img src="<?= $imagePath ?>" class="card-img-top" style="height: 180px; object-fit: cover;">
                                <div class="card-body">
                                    <h5 class="card-title d-flex justify-content-between align-items-start">
                                        <div>
                                            <?= htmlspecialchars($p['project_name']) ?><br>
                                            <small class="text-muted">#<?= htmlspecialchars($p['uniqid']) ?></small>
                                        </div>
                                        <?= $shareBadge ?>
                                    </h5>
                                    <p class="card-text"><?= substr(strip_tags($p['description']), 0, 100) ?>...</p>
                                    <div class="progress mb-2" style="height: 20px;">
                                        <div class="progress-bar bg-dark" role="progressbar" style="width: <?= $percentage ?>%;" aria-valuenow="<?= $percentage ?>" aria-valuemin="0" aria-valuemax="100">
                                            <?= $percentage ?>%
                                        </div>
                                    </div>
                                    <p><strong>Price per Share:</strong> ৳<?= number_format($p['price_per_share'], 2) ?></p>
                                    <p><strong>Total Amount:</strong> ৳<?= number_format($p['total_amount'], 2) ?></p>
                                    <span class="badge bg-dark text-light"><?= ucfirst($p['project_status']) ?></span>
                                    <?php if ($status === 'declined' && !empty($p['rejection_reason'])): ?>
                                        <div class="alert alert-danger mt-2 mb-0 p-2">
                                            <strong>Declined Reason:</strong> <?= htmlspecialchars($p['rejection_reason']) ?>
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($status === 'pending'): ?>
                                        <div class="d-flex gap-2 mt-2">
                                            <form method="POST" action="index.php?page=dashboard&pages=userproject&action=delete" onsubmit="return confirm('Are you sure you want to delete this project?');">
                                                <input type="hidden" name="project_id" value="<?= $project_id ?>">
                                                <button type="submit" class="btn btn-sm btn-warning mr-2">Delete</button>
                                            </form>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <!-- Investor/Partner/Donator Sections -->
            <?php if ($isInvestor || $isPartner || $isDonator): ?>
                <?php foreach (['active','closed'] as $status): ?>
                <div id="invested-<?= $status ?>-section" class="project-section" style="display:none;">
                    <h5>My Invested <?= ucfirst($status) ?> Projects</h5>
                    <div class="row">
                    <?php foreach ($investedProjects[$status] as $p): 
                        $imagePath = "admin/assets/projectimg/" . $p['project_img'];
                        $project_id = $p['id'];
                        $total_shares = (int)$p['shares'];
                        $orderStmt = $user->runQuery("SELECT SUM(shares_bought) AS total_sold FROM share_orders WHERE project_id = :pid");
                        $orderStmt->execute([':pid' => $project_id]);
                        $orderData = $orderStmt->fetch(PDO::FETCH_ASSOC);
                        $sold_shares = (int)($orderData['total_sold'] ?? 0);
                        $left_shares = $total_shares - $sold_shares;
                        $shareBadge = ($left_shares <= 0) 
                            ? '<span class="badge bg-secondary">No Shares Left</span>' 
                            : '<span class="badge bg-warning text-dark">'.$left_shares.' / '.$total_shares.' shares left</span>';
                        $percentage = ($total_shares > 0) ? round(($sold_shares / $total_shares) * 100) : 0;
                    ?>
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 shadow-sm">
                                <img src="<?= $imagePath ?>" class="card-img-top" style="height: 180px; object-fit: cover;">
                                <div class="card-body">
                                    <h5 class="card-title d-flex justify-content-between align-items-start">
                                        <div>
                                            <?= htmlspecialchars($p['project_name']) ?><br>
                                            <small class="text-muted">#<?= htmlspecialchars($p['uniqid']) ?></small>
                                        </div>
                                        <?= $shareBadge ?>
                                    </h5>
                                    <p class="card-text"><?= substr(strip_tags($p['description']), 0, 100) ?>...</p>
                                    <div class="progress mb-2" style="height: 20px;">
                                        <div class="progress-bar bg-dark" role="progressbar" style="width: <?= $percentage ?>%;" aria-valuenow="<?= $percentage ?>" aria-valuemin="0" aria-valuemax="100">
                                            <?= $percentage ?>%
                                        </div>
                                    </div>
                                    <p><strong>Price per Share:</strong> ৳<?= number_format($p['price_per_share'], 2) ?></p>
                                    <p><strong>Total Amount:</strong> ৳<?= number_format($p['total_amount'], 2) ?></p>
                                    <span class="badge bg-dark text-light"><?= ucfirst($p['project_status']) ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function showSection(sectionId) {
    const sections = document.querySelectorAll('.project-section');
    sections.forEach(sec => sec.style.display = 'none');
    const activeSection = document.getElementById(sectionId + '-section');
    if (activeSection) {
        activeSection.style.display = 'block';
    } else {
        // fallback to all active
        document.getElementById('all-active-section').style.display = 'block';
    }
}
window.onload = () => showSection('all-active');
</script>

<?php
// Handle invest action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'invest') {
    $projectId = (int)$_POST['project_id'];
    $shares = (int)$_POST['shares'];
    $result = $user->investInProject($userId, $projectId, $shares);
    echo '<script>alert("'.htmlspecialchars($result['message']).'");window.location.href=window.location.href;</script>';
}
?>
