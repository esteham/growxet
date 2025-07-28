<?php
// Loan statistics
$stmt = $user->runQuery("SELECT status, COUNT(*) as count FROM loans GROUP BY status");
$stmt->execute();
$loanStats = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $loanStats[$row['status']] = $row['count'];
}

// User type distribution
$stmt = $user->runQuery("
    SELECT ut.type_name, COUNT(utm.user_id) as count 
    FROM user_types ut
    LEFT JOIN user_type_map utm ON ut.id = utm.type_id
    GROUP BY ut.type_name
    ORDER BY count DESC
");
$stmt->execute();
$userTypeData = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="container mt-4">
    <h2 class="mb-4">Admin Dashboard</h2>
    
    <!-- First Row - Summary Cards -->
    <div class="row">
        <div class="col-md-3 mb-2">
            <div class="card text-dark h-100" style="background: #b3e5fc; min-height:120px;">
                <div class="card-body py-2 px-2">
                    <h6 class="card-title mb-1">Total Users</h6>
                    <p class="card-text h3 mb-0"><?= $totalUsers ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-2">
            <div class="card text-dark h-100" style="background: #fff9c4; min-height:120px;">
                <div class="card-body py-2 px-2">
                    <h6 class="card-title mb-1">Active Projects</h6>
                    <p class="card-text h3 mb-0"><?= $projectStats['active'] ?? 0 ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-2">
            <div class="card text-dark h-100" style="background: #c8e6c9; min-height:120px;">
                <div class="card-body py-2 px-2">
                    <h6 class="card-title mb-1">Pending Projects</h6>
                    <p class="card-text h3 mb-0"><?= $projectStats['pending'] ?? 0 ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-2">
            <div class="card text-dark h-100" style="background: #ffe0b2; min-height:120px;">
                <div class="card-body py-2 px-2">
                    <h6 class="card-title mb-1">Closed Projects</h6>
                    <p class="card-text h3 mb-0"><?= $projectStats['closed'] ?? 0 ?></p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Second Row - Financial Metrics -->
    <div class="row">
        <div class="col-md-3 mb-2">
            <div class="card h-100" style="background:#e1bee7; min-height:100px;">
                <div class="card-body py-2 px-2">
                    <h6 class="card-title mb-1">Total Investments</h6>
                    <p class="card-text h4 text-success mb-0"><?= number_format($totalInvestments, 2) ?> BDT</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-2">
            <div class="card h-100" style="background: #bbdefb; min-height:100px;">
                <div class="card-body py-2 px-2">
                    <h6 class="card-title mb-1">Admin Balance</h6>
                    <p class="card-text h4 text-primary mb-0"><?= number_format($totalBalance, 2) ?> BDT</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-2">
            <div class="card h-100" style="background: #c5cae9; min-height:100px;">
                <div class="card-body py-2 px-2">
                    <h6 class="card-title mb-1">Active Loans</h6>
                    <p class="card-text h4 mb-0"><?= $loanStats['active'] ?? 0 ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-2">
            <div class="card h-100" style="background: #d7ccc8; min-height:100px;">
                <div class="card-body py-2 px-2">
                    <h6 class="card-title mb-1">Pending Loans</h6>
                    <p class="card-text h4 mb-0"><?= $loanStats['pending'] ?? 0 ?></p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Third Row - Charts -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-light">
                    <h5>User Types Distribution</h5>
                </div>
                <div class="card-body">
                    <canvas id="userTypeChart" height="250"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-light">
                    <h5>Loan Status Overview</h5>
                </div>
                <div class="card-body">
                    <canvas id="loanStatusChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Fourth Row - Tables -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-light">
                    <h5>Recent Projects</h5>
                </div>
                <div class="card-body">
                    <table class="table table-striped table-sm">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Status</th>
                                <th>Owner</th>
                                <th>Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentProjects as $proj): 
                                $owner = $user->getUserById($proj['owner_id']);
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($proj['project_name']) ?></td>
                                <td><?= ucfirst($proj['project_status']) ?></td>
                                <td><?= htmlspecialchars($owner['f_name'] ?? 'N/A') ?></td>
                                <td><?= date('M d, Y', strtotime($proj['created_at'])) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-light">
                    <h5>Recent Users</h5>
                </div>
                <div class="card-body">
                    <table class="table table-striped table-sm">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>User Name</th>
                                <th>Email</th>
                                <th>Joined</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentUsers as $usr): ?>
                            <tr>
                                <td><?= htmlspecialchars($usr['f_name']) ?></td>
                                <td><?= htmlspecialchars($usr['user_name']) ?></td>
                                <td><?= htmlspecialchars($usr['user_email']) ?></td>
                                <td><?= date('M d, Y', strtotime($usr['created_at'])) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// User Type Distribution Chart
const userTypeCtx = document.getElementById('userTypeChart').getContext('2d');
const userTypeChart = new Chart(userTypeCtx, {
    type: 'bar',
    data: {
        labels: <?= json_encode(array_column($userTypeData, 'type_name')) ?>,
        datasets: [{
            label: 'Number of Users',
            data: <?= json_encode(array_column($userTypeData, 'count')) ?>,
            backgroundColor: [
                'rgba(179, 229, 252, 0.7)',
                'rgba(200, 230, 201, 0.7)',
                'rgba(255, 224, 178, 0.7)',
                'rgba(225, 190, 231, 0.7)',
                'rgba(197, 202, 233, 0.7)',
                'rgba(187, 222, 251, 0.7)',
                'rgba(215, 204, 200, 0.7)'
            ],
            borderColor: [
                'rgba(179, 229, 252, 1)',
                'rgba(200, 230, 201, 1)',
                'rgba(255, 224, 178, 1)',
                'rgba(225, 190, 231, 1)',
                'rgba(197, 202, 233, 1)',
                'rgba(187, 222, 251, 1)',
                'rgba(215, 204, 200, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});

// Loan Status Chart
const loanStatusCtx = document.getElementById('loanStatusChart').getContext('2d');
const loanStatusChart = new Chart(loanStatusCtx, {
    type: 'doughnut',
    data: {
        labels: <?= json_encode(array_keys($loanStats)) ?>,
        datasets: [{
            data: <?= json_encode(array_values($loanStats)) ?>,
            backgroundColor: [
                'rgba(255, 206, 86, 0.7)',  // pending (yellow)
                'rgba(75, 192, 192, 0.7)',   // approved (teal)
                'rgba(255, 99, 132, 0.7)',   // rejected (red)
                'rgba(153, 102, 255, 0.7)',  // cancelled (purple)
                'rgba(54, 162, 235, 0.7)',   // paid (blue)
                'rgba(255, 159, 64, 0.7)'    // due (orange)
            ],
            borderColor: [
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(255, 99, 132, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 159, 64, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'right'
            }
        }
    }
});
</script>