<div class="container-fluid mt-5">
    <div class="row">
        <div class="col-md-3 bg-light">
            <?php include('includes/sidebar.php'); ?>
        </div>

        <!--Main Content-->
        <div class="col-md-9">
            <div class="row p-3">
                <?php
                if (!isset($DB_con) || !$DB_con instanceof USER) {
    require_once 'config/class.user.php';
    $DB_con = new USER();
}

// Fetch active projects, with optional category filter
if (isset($_GET['cat_id'])) {
    $cat_id = intval($_GET['cat_id']);
    $stmt = $DB_con->runQuery("SELECT * FROM projects WHERE project_status = 'active' AND categoryId = :cat_id ORDER BY id DESC");
    $stmt->bindParam(':cat_id', $cat_id, PDO::PARAM_INT);
    $stmt->execute();
    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $projects = $DB_con->getActiveProjects();
}

                    if(count($projects) > 0)
                    {
                        foreach ($projects as $project) 
                        {
                            $imagePath = "admin/assets/projectimg/".$project['project_img'];
                            $project_id = $project['id'];
                            $total_shares = (int)$project['shares'];

                            // Get sold shares
                            $orderStmt = $DB_con->runQuery("SELECT SUM(shares_bought) as total_sold FROM share_orders WHERE project_id = :pid");
                            $orderStmt->bindParam(':pid', $project_id, PDO::PARAM_INT);
                            $orderStmt->execute();
                            $orderData = $orderStmt->fetch(PDO::FETCH_ASSOC);
                            $sold_shares = (int)$orderData['total_sold'];
                            $left_shares = $total_shares - $sold_shares;

                            $shareBadge = ($left_shares <= 0) 
                                ? '<span class="badge badge-secondary">No Shares Left</span>' 
                                : '<span class="badge badge-warning">'.$left_shares.' / '.$total_shares.' shares left</span>';

                            // Calculate percentage sold
                            $percentage = ($total_shares > 0) ? round(($sold_shares / $total_shares) * 100) : 0;

                            // Order button or disabled state
                            if ($left_shares <= 0) 
                            {
                                $button = '<button class="btn btn-secondary btn-block" disabled>No Shares Left</button>';
                            } 
                            else 
                            {
                                if (isset($_SESSION['userSession'])) {
                                $button = '<a href="index.php?page=invest&project_id='.$project_id.'" class="btn btn-success btn-block">Invest In This Project</a>
';
                                } 
                                else 
                                {
                                $button = '<a href="index.php?page=relog&msg=signin_required" class="btn btn-success btn-block">Invest In This Project</a>';
                                }
                            }

                            echo '
                                <div class="col-md-4 mb-4">
                                    <div class="card h-100 shadow-sm card-hover">
                                        <img src="'.$imagePath.'" class="card-top-img" alt="Image Not Found" style="height: 200px;">
                                        <div class="card-body">
                                            <h5 class="card-title d-flex justify-content-between align-items-center">
                                                '.htmlspecialchars($project['project_name']).'
                                                '.$shareBadge.'
                                            </h5>
                                            <p class="card-text">'.htmlspecialchars($project['description']).'</p>
                                            
                                            <!-- Progress Bar -->
                                            <div class="progress mb-2" style="height: 20px;">
                                                <div class="progress-bar bg-dark" role="progressbar" style="width: '.$percentage.'%;" aria-valuenow="'.$percentage.'" aria-valuemin="0" aria-valuemax="100">
                                                    '.$percentage.'%
                                                </div>
                                            </div>

                                            '.$button.'
                                        </div>
                                    </div>
                                </div>';
                        }
                    }
                    else
                    {
                        echo "<div class='col-12'><div class='alert alert-warning'>No projects found in this category.</div></div>";
                    }
                ?>
            </div>
        </div>
    </div>
</div>
