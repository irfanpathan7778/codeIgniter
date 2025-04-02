<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard Summary</title>
  <!-- Bootstrap CSS CDN (Bootstrap 5) -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
    }
    .card {
      margin-bottom: 20px;
    }
    .nav-link.active {
      font-weight: bold;
    }
  </style>
</head>
<body>
  <!-- Navigation Bar -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
      <a class="navbar-brand" href="#">My Dashboard</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
              aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
          <a class="nav-link active" href="<?= base_url('expenses'); ?>">Expenses</a>
          </li>
          <li class="nav-item">
          <a class="nav-link active" href="<?= base_url('categories'); ?>">Category</a>

          </li>
          <li class="nav-item">
          <a class="nav-link active" href="<?= base_url('budget'); ?>">Budget</a>
          </li>

          <li class="nav-item">
          <a class="nav-link active" href="<?= base_url('monthly_report'); ?>">Monthly Report</a>
          </li>

          <li class="nav-item">
              <a class="nav-link active" href="javascript:void(0);" onclick="logoutUser()">Logout</a>
          </li>

        </ul>
      </div>
    </div>
  </nav>

  <div class="container my-4">
    <div class="row">
      <div class="col-md-6 col-lg-4">
        <div class="card text-white bg-danger">
          <div class="card-body">
            <h5 class="card-title">Total Spending</h5>
            <p class="card-text display-4">₹<?= number_format($total_spending, 2); ?></p>
          </div>
        </div>
      </div>
      <div class="col-md-6 col-lg-4">
        <div class="card text-white bg-success">
          <div class="card-body">
            <h5 class="card-title">Remaining Budget</h5>
            <p class="card-text display-4">₹<?= number_format($remaining_budget, 2); ?></p>
          </div>
        </div>
      </div>

      
      <div class="col-md-12 col-lg-4">
        <div class="card">
          <div class="card-header">
            Top Spending Categories
          </div>
          <ul class="list-group list-group-flush">

          <?php foreach ($top_categories as $category): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center"><?= $category['category_name']; ?> - ₹<?= number_format($category['total_spent'], 2); ?></li>
                    <?php endforeach; ?>


            
          </ul>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS and dependencies -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  

  <script>
    function logoutUser() {
    localStorage.removeItem("jwt_token");
    fetch("<?= base_url('users/logout'); ?>", { method: "POST" })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            window.location.href = "<?= base_url('show_login_form'); ?>";
        })
        .catch(error => console.error("Error logging out:", error));
  logoutUser}

  </script>
</body>
</html>
