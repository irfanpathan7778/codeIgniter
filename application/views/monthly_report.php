<?php 
date_default_timezone_set('Asia/Kolkata');  // Change to your required timezone  

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
<div class="container mt-4">
    <h2>Monthly Report</h2>

    <div class="row">
        <div class="col-md-4">
            <label for="year">Year:</label>
            <select id="year" class="form-control">
                <?php for ($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
                    <option value="<?= $y; ?>"><?= $y; ?></option>
                <?php endfor; ?>
            </select>
        </div>
        <div class="col-md-4">
            <label for="month">Month:</label>
            <select id="month" class="form-control">
                <?php for ($m = 1; $m <= 12; $m++): ?>
                    <option value="<?= sprintf('%02d', $m); ?>"><?= date("F", mktime(0, 0, 0, $m, 1)); ?></option>
                <?php endfor; ?>
            </select>
        </div>
        <div class="col-md-4">
            <button id="fetchReport" class="btn btn-primary mt-4">Get Report</button>
        </div>
    </div>

    <div class="mt-4">
        <h4>Total Spent: ₹<span id="totalSpent">0</span></h4>
        <h5>Category Breakdown:</h5>
        <ul id="categoryBreakdown"></ul>
    </div>
    <a href="<?= base_url('dashboard'); ?>" class="btn btn-secondary">Back to Home</a>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("fetchReport").addEventListener("click", function () {
        let year = document.getElementById("year").value;
        let month = document.getElementById("month").value;
        let jwtToken = localStorage.getItem("jwt_token");

        fetch("<?= base_url('api/reports/monthly'); ?>?year=" + year + "&month=" + month, {
            method: "GET",
            headers: {
                "Authorization": "Bearer " + jwtToken
            }
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById("totalSpent").textContent = data.total_spent;
            let categoryBreakdown = document.getElementById("categoryBreakdown");
            categoryBreakdown.innerHTML = "";

            data.category_breakdown.forEach(category => {
                let listItem = document.createElement("li");
                listItem.textContent = `${category.category} - ₹${category.amount}`;
                categoryBreakdown.appendChild(listItem);
            });
        })
        .catch(error => {
            alert("Error fetching report: " + error.message);
        });
    });
});
</script>

</body>
</html>


