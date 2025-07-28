
<!-- <footer class="bg-dark col-md-12 site-footer py-2 mt-2 text-muted custom-footer">
    <div class="container pt-2 mt-1 text-center">
        <span class="text-muted">© <?php echo date("Y"); ?> GrowNet Admin Panel. All rights reserved.</span>
    </div>
</footer> -->


<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Custom Scripts -->


<script>

// Fade-in effect for floating cards

  document.addEventListener("DOMContentLoaded", function () {
    const cards = document.querySelectorAll('.floating-card');

    const observer = new IntersectionObserver(entries => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('fade-in');
          observer.unobserve(entry.target); // Fade-in only once
        }
      });
    }, {
      threshold: 0.1
    });

    cards.forEach(card => {
      observer.observe(card);
    });
  });
</script>


<script>

//project rejection script

$(document).ready(function() {
    // Handle reject button clicks
    $('.reject-btn').click(function() {
        var projectId = $(this).data('id');
        $('#reject_project_id').val(projectId);
        $('#rejectModal').modal('show');
    });
});
</script>


</body>
</html>