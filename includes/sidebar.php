<?php

require_once 'config/class.user.php';
$DB_con = new USER();

$stmt = $DB_con->runQuery("SELECT id, category_name FROM category ORDER BY category_name");
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

$current_cat_id = isset($_GET['cat_id']) ? intval($_GET['cat_id']) : 0;

?>
<!-- Sidebar -->
<div class="bg-light border-right" id="sidebar-wrapper">
	<div class="sidebar-heading font-weight-bold text-center py-3">
		<a href="index.php?page=project" class="text-dark text-decoration-none">Categories</a>
	</div>

	<div class="list-group list-group-flush">
		<?php

			if($categories):?>
				<?php

					foreach($categories as $category):?>

						<?php

							$active = ($category['id'] == $current_cat_id) ? 'active' : '';
						?>

						<a href="index.php?page=project&cat_id=<?= urlencode($category['id']); ?>" class="list-group-item list-group-item-action bg-light text-info <?= $active; ?>">
							<?= htmlspecialchars($category['category_name']); ?>
						</a>

					<?php endforeach; ?>

					<?php else: ?>
						<div class="text-muted text-center p-3">No Categories Found</div>
					<?php endif; ?>
	</div>
</div>