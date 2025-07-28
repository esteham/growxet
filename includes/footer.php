
<footer class="bg-light col-md-12 site-footer py-2 mt-2 text-muted custom-footer">
  <div class="container">
    <?php if (!$is_logged_in): ?>
    <div class="row">
      <div class="col-md-6">
        <div class="row">
          <div class="col-md-7">
            <h2 class="footer-heading mb-4" style="font-size: 1.5rem; color: #212529;">About Us</h2>
            <p style="font-size: 1rem; color: #212529;">
              GrowNet is a crowdfunding platform dedicated to supporting innovative ideas and creative projects. Our mission is to empower individuals to bring their projects to life through the support of a global community.
            </p>
          </div>
          <div class="col-md-4 ml-auto">
            <h2 class="footer-heading mb-4" style="font-size: 1.5rem; color: #212529;">Features</h2>
            <ul class="list-unstyled" style="font-size: 1rem;">
              <li><a href="#" class="text-dark">About Us</a></li>
              <li><a href="#" class="text-dark">Testimonials</a></li>
              <li><a href="#" class="text-dark">Terms of Service</a></li>
              <li><a href="#" class="text-dark">Privacy</a></li>
              <li><a href="#" class="text-dark">Contact Us</a></li>
            </ul>
          </div>
        </div>
      </div>
      <div class="col-md-4 ml-auto">
        <div class="mb-5">
          <h2 class="footer-heading mb-4" style="font-size: 1.5rem; color: #212529;">Subscribe to Newsletter</h2>
          <form action="#" method="post" class="footer-suscribe-form">
            <div class="input-group mb-3">
              <input type="text" class="form-control rounded-2 border-secondary" placeholder="Enter Email" aria-label="Enter Email" aria-describedby="button-addon2" style="font-size: 1rem; color: #212529; background-color: #fff;">
              <div class="input-group-append">
                <button class="btn btn-success" type="button" id="button-addon2" style="font-size: 1rem;">Subscribe</button>
              </div>
            </div>
          </form>
        </div>

        <h2 class="footer-heading mb-4" style="font-size: 1.5rem; color: #212529;">Follow Us</h2>
        <a href="#about-section" class="smoothscroll pl-0 pr-3 text-dark"><span class="icon-facebook"></span></a>
        <a href="#" class="pl-3 pr-3 text-dark"><span class="icon-twitter"></span></a>
        <a href="#" class="pl-3 pr-3 text-dark"><span class="icon-instagram"></span></a>
        <a href="#" class="pl-3 pr-3 text-dark"><span class="icon-linkedin"></span></a>
      </div>
      <?php endif; ?>
    </div>

    <div class="row pt-2 mt-1 text-center">
      <div class="col-md-12">

        <!-- Live Location -->
        <div id="livelocation" class="text-center"></div>

        <div class="pt-2">
          <p class="mb-1" style="font-size: 1rem; color: #212529;">&copy; <?php echo date("Y"); ?> All Rights Reserved by GrowNet</p>
        </div>
      </div>
    </div>
  </div>
</footer>

<!-- jQuery core -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

<!-- Plugin (MUST come after jQuery) -->
<script src="assets/js/jquery.livelocation.js"></script>

<!-- Your custom script that uses .livelocation() -->
<script>
  jQuery(document).ready(function() {
    jQuery('#livelocation').livelocation();
  });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous"></script>

<script>
function confirmInvest(form) {
    return confirm('Are you sure you want to invest in this project?');
}
</script>

</body>
</html>