<?php
require_once 'config/class.user.php';
$DB_con = new USER();
?>

<!-- Font Awesome CDN (add this in your <head> if not already present) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<!-- Carousel Start -->
<div id="carouselFade" class="carousel slide carousel-fade" data-ride="carousel">
  <div class="carousel-inner">
    <div class="carousel-item active">
        <img src="assets/img/startup.jpg" class="d-block w-100" alt="...">
        <div class="carousel-caption h-100">
            <h2 class="text-light font-weight-medium m-0">We Have Been Providing Support For</h2>
            <h1 class="display-1 text-white m-0">STARTUP PROJECTS</h1>
            <h2 class="text-white m-0">* Since 2020 *</h2>
        </div>
    </div>
    <div class="carousel-item">
        <img src="assets/img/invest.jpg" class="d-block w-100" alt="...">
        <div class="carousel-caption h-100">
            <h2 class="text-light font-weight-medium m-0">We Have Been Connecting With</h2>
            <h1 class="display-1 text-white m-0">MICROINVESTORS</h1>
            <h2 class="text-white m-0">* Since 2020 *</h2>
        </div>
    </div>
    <div class="carousel-item">
        <img src="assets/img/fund.jpg" class="d-block w-100" alt="...">
        <div class="carousel-caption h-100">
            <h2 class="text-light font-weight-medium m-0">We Have Been Growing Through</h2>
            <h1 class="display-1 text-white m-0">CROWDFUNDING</h1>
            <h2 class="text-white m-0">* Since 2020 *</h2>
        </div>
    </div>
  </div>
  <button class="carousel-control-prev" type="button" data-target="#carouselFade" data-slide="prev">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="sr-only">Previous</span>
  </button>
  <button class="carousel-control-next" type="button" data-target="#carouselFade" data-slide="next">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="sr-only">Next</span>
  </button>
</div>
<!-- Carousel End -->


<!-- Current Active Projects Start -->
<?php
$user = new USER();
$activeProjects = $user->getActiveProjects();
?>

<div class="container-fluid mt-2 custom-section">
    <div class="section-header">
        <span class="section-bg-icon"><i class="fa-solid fa-lightbulb"></i></span>
        <h2 class="display-4 font-weight-bold">Our Active Projects</h2>
        <p class="lead text-muted">Discover the innovative projects we're currently supporting</p>
    </div>
    <div class="row p-2">
        <?php if ($activeProjects): ?>
            <?php foreach ($activeProjects as $project): ?>
                <div class="col-md-4 mb-2 p-1 d-flex justify-content-center">
                    <div class="card shadow floating-card h-100 d-flex flex-column project-card-85">
                        <img src="admin/assets/projectimg/<?= htmlspecialchars($project['project_img']) ?>" class="card-img-top project-card-img" alt="Project Image">
                        <div class="card-body d-flex flex-column flex-grow-1">
                            <h5 class="card-title"><?= htmlspecialchars($project['project_name']) ?></h5>
                            <p class="card-text project-desc mb-2" style="min-height:3em;">
                                <?= htmlspecialchars(mb_strimwidth($project['description'], 0, 120, '...')) ?>
                                <?php if (mb_strlen($project['description']) > 120): ?>
                                    <span class="see-more text-success" style="cursor:pointer;" onclick="this.previousSibling.textContent = '<?= htmlspecialchars($project['description'], ENT_QUOTES) ?>'; this.style.display='none';">See more</span>
                                <?php endif; ?>
                            </p>
                        </div>
                        <div class="card-footer bg-white border-top pt-2">
                            <div class="row text-center">
                                <div class="col-4 px-1">
                                    <div class="text-muted small">Shares</div>
                                    <div class="font-weight-bold"><?= (int)$project['shares'] ?></div>
                                </div>
                                <div class="col-4 px-1">
                                    <div class="text-muted small">Price/Share</div>
                                    <div class="font-weight-bold"><?= number_format($project['price_per_share'], 2) ?></div>
                                </div>
                                <div class="col-4 px-1">
                                    <div class="text-muted small">Type</div>
                                    <div class="font-weight-bold"><?= htmlspecialchars($project['project_type']) ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class='text-muted'>No active projects found.</p>
        <?php endif; ?>
    </div>
</div>
<!-- Current Active Projects End -->


<!-- Partners Start -->
<div class="container-fluid my-5 custom-section" id="partners">
    <div class="section-header">
        <span class="section-bg-icon"><i class="fa-solid fa-handshake"></i></span>
        <h2 class="display-4 font-weight-bold">Trusted Partners</h2>
        <p class="lead text-muted">Organizations that help us make a difference</p>
    </div>
    <div class="row justify-content-center">
    <?php
    $stmt = $DB_con->runQuery("
        SELECT u.id, u.user_name, u.f_name, u.user_img, u.description, u.created_at
        FROM users u
        INNER JOIN user_type_map utm ON u.id = utm.user_id
        INNER JOIN user_types ut ON utm.type_id = ut.id
        WHERE ut.type_name = 'partner'
        ORDER BY u.created_at DESC
    ");
    $stmt->execute();
    $partners = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($partners):
        foreach ($partners as $partner):
            $imgPath = !empty($partner['user_img']) 
                       ? htmlspecialchars($partner['user_img']) 
                       : 'assets/img/default.jpg';
            $partnerName = htmlspecialchars($partner['f_name'] ?: $partner['user_name']);
            $since = date('F Y', strtotime($partner['created_at']));
            $url = filter_var(trim($partner['description']), FILTER_VALIDATE_URL) ? trim($partner['description']) : '#';
    ?>
        <div class="col-md-2 col-6 mb-4 text-center partner-animated">
            <div class="d-flex flex-column align-items-center">
                <a href="<?= $url ?>" target="_blank" rel="noopener noreferrer">
                    <img src="<?= $imgPath ?>" alt="<?= $partnerName ?>" class="partner-logo-circle mb-2" />
                </a>
                <div>
                    <h6 class="mb-1 mt-2"><?= $partnerName ?></h6>
                    <small class="text-muted d-block mb-1">Partner since <?= $since ?></small>
                </div>
            </div>
        </div>
    <?php
        endforeach;
    else:
    ?>
        <div class="col-12 text-center">
            <p class="text-muted">No partners found.</p>
        </div>
    <?php endif; ?>
    </div>
</div>
<!-- Partners End -->


<!-- Get Involved Section -->
<div class="container-fluid my-5 bg-light py-5 custom-section" id="get-involved">
  <div class="section-header">
        <span class="section-bg-icon"><i class="fa-solid fa-users"></i></span>
        <h2 class="display-4 font-weight-bold">Get Involved</h2>
        <p class="lead text-muted">Multiple ways to participate in our mission</p>
    </div>
  <div class="row text-center px-3 justify-content-center align-items-stretch">

    <!-- Invest -->
    <div class="col-md-4 col-lg-2 mb-4 d-flex">
      <div class="grow-card p-4 rounded shadow-sm text-center d-flex flex-column justify-content-between w-100 position-relative">
        <div class="icon-circle-wrapper mx-auto">
          <span class="icon-circle bg-success text-white">
            <i class="fa-solid fa-sack-dollar"></i>
          </span>
        </div>
        <div class="mt-3">
          <h6 class="mb-2">Invest</h6>
          <p class="text-muted small">Support vetted projects and grow together.</p>
        </div>
        <a href="invest.php" class="btn btn-sm btn-success mt-3">Start Investing</a>
      </div>
    </div>

    <!-- Loan -->
    <div class="col-md-4 col-lg-2 mb-4 d-flex">
      <div class="grow-card p-4 rounded shadow-sm text-center d-flex flex-column justify-content-between w-100 position-relative">
        <div class="icon-circle-wrapper mx-auto">
          <span class="icon-circle bg-primary text-white">
            <i class="fa-solid fa-hand-holding-dollar"></i>
          </span>
        </div>
        <div class="mt-3">
          <h6 class="mb-2">Request Loan</h6>
          <p class="text-muted small">Have an idea? Let us help fund it.</p>
        </div>
        <a href="loan.php" class="btn btn-sm btn-success mt-3">Apply Now</a>
      </div>
    </div>

    <!-- Partner -->
    <div class="col-md-4 col-lg-2 mb-4 d-flex">
      <div class="grow-card p-4 rounded shadow-sm text-center d-flex flex-column justify-content-between w-100 position-relative">
        <div class="icon-circle-wrapper mx-auto">
          <span class="icon-circle bg-warning text-white">
            <i class="fa-solid fa-handshake"></i>
          </span>
        </div>
        <div class="mt-3">
          <h6 class="mb-2">Become a Partner</h6>
          <p class="text-muted small">Collaborate to scale social impact.</p>
        </div>
        <a href="#partners" class="btn btn-sm btn-success mt-3">Partner With Us</a>
      </div>
    </div>

    <!-- Volunteer -->
    <div class="col-md-4 col-lg-2 mb-4 d-flex">
      <div class="grow-card p-4 rounded shadow-sm text-center d-flex flex-column justify-content-between w-100 position-relative">
        <div class="icon-circle-wrapper mx-auto">
          <span class="icon-circle bg-info text-white">
            <i class="fa-solid fa-people-group"></i>
          </span>
        </div>
        <div class="mt-3">
          <h6 class="mb-2">Volunteer</h6>
          <p class="text-muted small">Join our mission. Make a difference.</p>
        </div>
        <a href="volunteer.php" class="btn btn-sm btn-success mt-3">Join Us</a>
      </div>
    </div>

    <!-- Donate -->
    <div class="col-md-4 col-lg-2 mb-4 d-flex">
      <div class="grow-card p-4 rounded shadow-sm text-center d-flex flex-column justify-content-between w-100 position-relative">
        <div class="icon-circle-wrapper mx-auto">
          <span class="icon-circle bg-danger text-white">
            <i class="fa-solid fa-heart"></i>
          </span>
        </div>
        <div class="mt-3">
          <h6 class="mb-2">Donate</h6>
          <p class="text-muted small">Every bit helps us empower more people.</p>
        </div>
        <a href="donate.php" class="btn btn-sm btn-success mt-3">Donate Now</a>
      </div>
    </div>

  </div>
</div>
<!-- Get Involved Section End -->

<!-- Dynamic Impact Metrics -->
<?php $metrics = $user->getImpactMetrics(); ?>
<div class="container-fluid my-5 bg-light py-5 custom-section" id="impact">
  <div class="section-header">
        <span class="section-bg-icon"><i class="fa-solid fa-chart-line"></i></span>
        <h2 class="display-4 font-weight-bold">GrowNet Impact</h2>
        <p class="lead text-muted">Tracking our progress to empower more changemakers every day</p>
    </div>
  <div class="metrics-container">
    <div class="metric-card">
        <div class="metric-value" data-target="<?= $metrics['total_active_projects'] ?>">0</div>
        <div class="metric-label">Active Projects</div>
    </div>
    <div class="metric-card">
        <div class="metric-value" data-target="<?= $metrics['total_investment'] ?>">0</div>
        <div class="metric-label">Total Investment (৳)</div>
    </div>
    <div class="metric-card">
        <div class="metric-value" data-target="<?= $metrics['total_investors'] ?>">0</div>
        <div class="metric-label">Unique Investors</div>
    </div>
    <div class="metric-card">
        <div class="metric-value" data-target="<?= $metrics['total_loans_approved'] ?>">0</div>
        <div class="metric-label">Loans Approved</div>
    </div>
  </div>
</div>
<!-- End of Dynamic Impact Metrics -->

<!-- News and Updates Section -->
<div class="container-fluid my-5 bg-light py-5 custom-section" id="news">
  <div class="section-header">
    <span class="section-bg-icon"><i class="fa-solid fa-newspaper"></i></span>
    <h2 class="display-4 font-weight-bold">News & Updates</h2>
    <p class="lead text-muted">Stay informed with our latest stories, milestones, and opportunities</p>
  </div>
  <div class="row">
    <div class="col-md-4 mb-4">
      <div class="card news-card">
        <div class="card-body">
          <h5 class="card-title">Exciting Partnership Announced</h5>
          <p class="card-text">GrowNet partners with XYZ Foundation to empower more startups in 2025.</p>
        </div>
      </div>
    </div>
    <div class="col-md-4 mb-4">
      <div class="card news-card">
        <div class="card-body">
          <h5 class="card-title">Milestone Achieved</h5>
          <p class="card-text">We have reached 100 active projects! Thank you for your support.</p>
        </div>
      </div>
    </div>
    <div class="col-md-4 mb-4">
      <div class="card news-card">
        <div class="card-body">
          <h5 class="card-title">Upcoming Event</h5>
          <p class="card-text">Join our annual GrowNet Summit this August. Registration is open now!</p>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- News and Updates End -->



<!-- Our Team Section -->
<div class="container-fluid my-5 bg-light py-5 custom-section" id="our-team">
  <div class="section-header">
    <span class="section-bg-icon"><i class="fa-solid fa-people-group"></i></span>
    <h2 class="display-4 font-weight-bold">Our Team</h2>
    <p class="lead text-muted">Meet the people driving GrowNet's mission forward</p>
  </div>
  <div class="row justify-content-center">
    <?php
    $stmt = $DB_con->runQuery("
      SELECT u.f_name, u.user_name, u.user_img, u.description, ut.type_name
      FROM users u
      INNER JOIN user_type_map utm ON u.id = utm.user_id
      INNER JOIN user_types ut ON utm.type_id = ut.id
      WHERE ut.type_name IN ('admin', 'manager')
      GROUP BY u.id
      ORDER BY u.f_name
    ");
    $stmt->execute();
    $team = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($team):
      foreach ($team as $member):
        $imgPath = !empty($member['user_img']) ? htmlspecialchars($member['user_img']) : 'assets/img/default.jpg';
        $name = htmlspecialchars($member['f_name'] ?: $member['user_name']);
        $type = ucfirst(htmlspecialchars($member['type_name']));
        $speech = htmlspecialchars($member['description'] ?: 'Let\'s grow together!');
    ?>
    <div class="col-md-3 col-sm-6 mb-4 d-flex justify-content-center">
      <?php
        $cardClass = '';
        if ($member['type_name'] === 'admin') {
          $cardClass = 'team-admin-card';
        } elseif ($member['type_name'] === 'manager') {
          $cardClass = 'team-manager-card';
        }
      ?>
      <div class="card team-card shadow floating-card h-100 d-flex flex-column align-items-center text-center p-3 <?= $cardClass ?>">
        <img src="<?= $imgPath ?>" alt="<?= $name ?>" class="rounded-circle mb-3" style="width:90px;height:90px;object-fit:cover;box-shadow:0 2px 8px rgba(40,167,69,0.08);">
        <h5 class="mb-1"><?= $name ?></h5>
        <div class="text-success small mb-2"><?= $type ?></div>
        <p class="text-muted small flex-grow-1"><?= $speech ?></p>
      </div>
    </div>
    <?php
      endforeach;
    else:
    ?>
      <div class="col-12 text-center">
        <p class="text-muted">No team members found.</p>
      </div>
    <?php endif; ?>
  </div>
</div>

<!-- Section Header Icon CSS (add to your style.css if not already present) -->
<style>
.section-header {
  position: relative;
  overflow: visible;
}
.section-header .section-bg-icon {
  position: absolute;
  top: 50%;
  left: 50%;
  font-size: 7rem;
  color: #e3eaf3;
  opacity: 0.25;
  transform: translate(-50%, -50%);
  pointer-events: none;
  z-index: 0;
  user-select: none;
}
.section-header h2,
.section-header p {
  position: relative;
  z-index: 1;
}
.team-admin-card {
  background: #e3f2fd !important;
  border: 1.5px solid #90caf9;
}
.team-manager-card {
  background: #fffde7 !important;
  border: 1.5px solid #ffe082;
}
</style>

<!-- JS for scroll-in, news float, and metric count-up effects (already present in your code) -->
<script>
  document.addEventListener("DOMContentLoaded", function () {
    const sections = document.querySelectorAll('.custom-section');
    const observer = new IntersectionObserver(entries => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('fade-in');
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.1 });
    sections.forEach(section => observer.observe(section));
  });

  document.addEventListener("DOMContentLoaded", function () {
    const newsCards = document.querySelectorAll('.news-card');
    const observer = new IntersectionObserver(entries => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('news-float-in');
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.15 });
    newsCards.forEach(card => observer.observe(card));
  });

  document.addEventListener("DOMContentLoaded", function () {
    const metricSection = document.getElementById('impact');
    let counted = false;
    if (metricSection) {
      const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
          if (entry.isIntersecting && !counted) {
            counted = true;
            document.querySelectorAll('.metric-value').forEach(el => {
              const target = parseInt(el.getAttribute('data-target').replace(/,/g, ''), 10) || 0;
              animateCount(el, target, 1200);
            });
            observer.unobserve(metricSection);
          }
        });
      }, { threshold: 0.3 });
      observer.observe(metricSection);
    }
    function animateCount(el, target, duration) {
      let start = 0;
      const isMoney = el.parentElement.querySelector('.metric-label')?.textContent.includes('Investment');
      const stepTime = Math.abs(Math.floor(duration / target));
      const startTime = performance.now();
      function update(currentTime) {
        const elapsed = currentTime - startTime;
        let progress = Math.min(elapsed / duration, 1);
        let value = Math.floor(progress * target);
        if (isMoney) {
          el.textContent = value.toLocaleString();
        } else {
          el.textContent = value;
        }
        if (progress < 1) {
          requestAnimationFrame(update);
        } else {
          if (isMoney) {
            el.textContent = target.toLocaleString();
          } else {
            el.textContent = target;
          }
        }
      }
      requestAnimationFrame(update);
    }
  });
</script>
