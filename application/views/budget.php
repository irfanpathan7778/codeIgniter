<?php 
date_default_timezone_set('Asia/Kolkata');  // Change to your required timezone  

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Budgets</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-4">
    <h2>Budget List</h2>

    <!-- Button to Open the Form -->
    <button class="btn btn-primary" id="addBudgetBtn">Add Budget</button>

    <!-- Budget Form (Hidden by Default) -->
    <div id="budgetForm" class="mt-3" style="display: none;">
        <select id="budget-category" class="form-control">
            <option value="">Select Category</option>
        </select>
        <input type="number" id="budgetLimit" class="form-control mt-2" placeholder="Enter Budget Limit">
        <input type="number" id="budgetMonth" class="form-control mt-2" placeholder="Enter Month (01-12)">
        <input type="number" id="budgetYear" class="form-control mt-2" placeholder="Enter Year">
        <button class="btn btn-success mt-2" id="saveBudgetBtn">Save Budget</button>
    </div>

    <!-- Table to display budgets -->
    <table class="table table-bordered mt-3" id="budgets-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Category</th>
                <th>Limit</th>
                <th>Month</th>
                <th>Year</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>

    <a href="<?= base_url('dashboard'); ?>" class="btn btn-secondary">Back to Home</a>


    <!-- Edit Budget Modal -->
<!-- Edit Budget Modal -->
<div class="modal fade" id="editBudgetModal" tabindex="-1" aria-labelledby="editBudgetModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editBudgetModalLabel">Edit Budget</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editBudgetForm">
                    <input type="hidden" id="edit-budget-id">
                    
                    <div class="mb-3">
                        <label for="edit-budget-category" class="form-label">Category</label>
                        <select class="form-control" id="edit-budget-category">
                            <!-- Categories will be loaded dynamically -->
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="edit-budget-limit" class="form-label">Budget Limit</label>
                        <input type="number" class="form-control" id="edit-budget-limit" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit-budget-month" class="form-label">Month</label>
                        <select class="form-control" id="edit-budget-month">
                            <option value="01">January</option>
                            <option value="02">February</option>
                            <option value="03">March</option>
                            <option value="04">April</option>
                            <option value="05">May</option>
                            <option value="06">June</option>
                            <option value="07">July</option>
                            <option value="08">August</option>
                            <option value="09">September</option>
                            <option value="10">October</option>
                            <option value="11">November</option>
                            <option value="12">December</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit-budget-year" class="form-label">Year</label>
                        <input type="number" class="form-control" id="edit-budget-year" required>
                    </div>
                    
                    <button type="submit" class="btn btn-success">Update Budget</button>
                </form>
            </div>
        </div>
    </div>
</div>


</div>
<!-- Bootstrap JS (Popper.js is required for modals) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const jwtToken = localStorage.getItem("jwt_token");

    document.getElementById("addBudgetBtn").addEventListener("click", function() {
        fetchCategories();
        document.getElementById("budgetForm").style.display = "block";
    });

    document.getElementById("saveBudgetBtn").addEventListener("click", function() {
        const categoryId = document.getElementById("budget-category").value;
        const budgetLimit = document.getElementById("budgetLimit").value;
        const budgetMonth = document.getElementById("budgetMonth").value;
        const budgetYear = document.getElementById("budgetYear").value;

        if (!categoryId || !budgetLimit || !budgetMonth || !budgetYear) {
            alert("Please fill all fields.");
            return;
        }

        fetch("<?= base_url('api/budget/add'); ?>" , {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Authorization": "Bearer " + jwtToken
            },
            body: JSON.stringify({ category_id: categoryId, limit: budgetLimit, month: budgetMonth, year: budgetYear })
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            location.reload();
        })
        .catch(error => console.error("Error adding budget:", error));
    });

    function fetchBudgets() {
        fetch("<?= base_url('api/budget'); ?>", {
            method: "GET",
            headers: { "Authorization": "Bearer " + jwtToken }
        })
        .then(response => response.json())
        .then(data => {
            let rows = "";
            data.forEach(budget => {
                rows += `<tr id="row-${budget.id}">
                    <td>${budget.id}</td>
                    <td>${budget.category_name}</td>
                    <td>${budget.limit}</td>
                    <td>${budget.month}</td>
                    <td>${budget.year}</td>
                    <td>
                        <button class="btn btn-warning btn-sm" onclick="editBudget(${budget.id})">Edit</button>
                        <button class="btn btn-danger btn-sm" onclick="deleteBudget(${budget.id})">Delete</button>
                    </td>
                </tr>`;
            });
            document.querySelector("#budgets-table tbody").innerHTML = rows;
        })
        .catch(error => console.error("Error fetching budgets:", error));
    }

    function fetchCategories() {
        fetch("<?= base_url('api/categories'); ?>", {
            method: "GET",
            headers: { "Authorization": "Bearer " + jwtToken }
        })
        .then(response => response.json())
        .then(categories => {
            let categorySelect = document.getElementById("budget-category");
            categorySelect.innerHTML = `<option value="">Select Category</option>`;
            categories.forEach(category => {
                categorySelect.innerHTML += `<option value="${category.id}">${category.category_name}</option>`;
            });
        })
        .catch(error => console.error("Error fetching categories:", error));
    }

    window.editBudget = function(budgetId) {
    fetch("<?= base_url('api/budget/'); ?>" + budgetId, {
        method: "GET",
        headers: { "Authorization": "Bearer " + jwtToken }
    })
    .then(response => response.json())
    .then(budget => {
        if (!budget) {
            alert("Budget not found");
            return;
        }

        fetch("<?= base_url('api/categories'); ?>", {
            method: "GET",
            headers: { "Authorization": "Bearer " + jwtToken }
        })
        .then(response => response.json())
        .then(categories => {
            let categorySelect = document.getElementById("edit-budget-category");
            categorySelect.innerHTML = "";

            categories.forEach(category => {
                let option = document.createElement("option");
                option.value = category.id;
                option.textContent = category.category_name;
                if (category.id == budget.category_id) {
                    option.selected = true;
                }
                categorySelect.appendChild(option);
            });
        })
        .catch(error => console.error("Error fetching categories:", error));

        document.getElementById("edit-budget-id").value = budgetId;
        document.getElementById("edit-budget-limit").value = budget.limit;
        document.getElementById("edit-budget-month").value = budget.month;
        document.getElementById("edit-budget-year").value = budget.year;

        var editBudgetModal = new bootstrap.Modal(document.getElementById("editBudgetModal"));
        editBudgetModal.show();
    })
    .catch(error => console.error("Error fetching budget details:", error));
};


document.getElementById("editBudgetForm").addEventListener("submit", function(event) {
    event.preventDefault();

    const budgetId = document.getElementById("edit-budget-id").value;
    const updatedBudget = {
        category_id: document.getElementById("edit-budget-category").value,
        limit: document.getElementById("edit-budget-limit").value,
        month: document.getElementById("edit-budget-month").value,
        year: document.getElementById("edit-budget-year").value
    };

    fetch("<?= base_url('api/budget/update/'); ?>" + budgetId, {
        method: "PUT",
        headers: {
            "Content-Type": "application/json",
            "Authorization": "Bearer " + jwtToken
        },
        body: JSON.stringify(updatedBudget)
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        fetchBudgets();

        var editBudgetModal = bootstrap.Modal.getInstance(document.getElementById("editBudgetModal"));
        editBudgetModal.hide();
    })
    .catch(error => console.error("Error updating budget:", error));
});


    window.deleteBudget = function(budgetId) {
        if (!confirm("Are you sure you want to delete this budget?")) return;

        fetch("<?= base_url('api/budget/delete/'); ?>" + budgetId, {
            method: "DELETE",
            headers: { "Authorization": "Bearer " + jwtToken }
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            document.getElementById(`row-${budgetId}`).remove();
        })
        .catch(error => console.error("Error deleting budget:", error));
    };

    fetchBudgets();
});
</script>

</body>
</html>
