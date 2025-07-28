

<div class="container-fluid mt-5 pt-5">
    <div class="row">
        <div class="col-md-3 bg-light">
            <?php include('includes/sidebar.php'); ?>
        </div>

        <!--Main Content-->

        <div class="col-md-9">
            <div class="row p-3">
                <?php

                    $query = "SELECT * FROM projects";
                    if(isset($_GET['cat_id']))
                    {
                        $cat_id = intval($_GET['cat_id']);
                        $query .= " WHERE categoryId = :cat_id";                       
                    }

                    $query .= " ORDER BY id DESC";

                    $stmt = $DB_con->runQuery($query);

                    if(isset($cat_id))
                    {
                        $stmt->bindParam(':cat_id', $cat_id, PDO::PARAM_INT);
                    }

                    $stmt->execute();
                    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    if(count($projects) > 0)
                    {
                        foreach ( $projects as $project) 
                        {
                            $imagePath = "admin/pages/uploads/".$project['project_img'];

                            $stock_amnt = (int)$project['stock_amount'];

                            if($share_amnt == 0)
                            {
                                $shareBadge = '<span class="badge badge-danger">Out of Stock</span>';
                            }

                            elseif ($stock_amnt < 5) 
                            {
                                $shareBadge = '<span class="badge badge-warning">Low Stock</span>';
                            }

                            else 
                            {
                                $shareBadge = '<span class="badge badge-success">In Stock</span>';
                            }

                            echo '

                                    <div class="col-md-4 mb-4">
                                        <div class="card h-100 shadow-sm">
                                            <img src="'.$imagePath.'" class="card-top-img" alt="Image Not Found" style="height: 200px;">
                                            <div class="card-body">
                                                <h5 class="card-title d-flex justify-content-between align-content-center">'.htmlspecialchars($project['project_name']).''.$shareBadge.'</h5>

                                                <p class="card-text">'.htmlspecialchars($project['description']).'</p>

                                                <a href="#" class="btn btn-primary btn-block">Order Now</a>
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
