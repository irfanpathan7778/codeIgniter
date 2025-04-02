<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expenses</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>



    <div class="container mt-4">
        <h2>Expense List</h2>


        <button id="exportCsvBtn" class="btn btn-success">Export CSV</button>
<button id="exportPdfBtn" class="btn btn-danger">Export PDF</button>

<input type="file" id="csvFileInput" style="display: none;">
<button id="importCsvBtn" class="btn btn-primary">Import CSV</button>

        <button class="btn btn-primary mb-3" onclick="showExpenseForm()">Add Expense</button>

        <div id="expense-form" style="display: none;">
            <div class="mb-3">
                <label>Amount</label>
                <input type="number" id="expense-amount" class="form-control">
            </div>
            <div class="mb-3">
                <label>Category</label>
                <select id="expense-category" class="form-control">
                </select>
            </div>
            <div class="mb-3">
                <label>Description</label>
                <input type="text" id="expense-description" class="form-control">
            </div>
            <div class="mb-3">
                <label>Date</label>
                <input type="date" id="expense-date" class="form-control">
            </div>
            <button class="btn btn-success" onclick="addExpense()">Save Expense</button>
            <button class="btn btn-secondary" onclick="hideExpenseForm()">Cancel</button>
        </div>

        <table class="table table-bordered" id="expenses-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Amount</th>
                    <th>Category</th>
                    <th>Description</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>

        <a href="<?= base_url(); ?>" class="btn btn-secondary">Back to Home</a>



<div class="modal fade" id="editExpenseModal" tabindex="-1" aria-labelledby="editExpenseModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editExpenseModalLabel">Edit Expense</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="expense-id">
                
                <div class="mb-3">
                    <label for="edit-amount" class="form-label">Amount</label>
                    <input type="number" class="form-control" id="edit-amount">
                </div>

                <div class="mb-3">
                    <label for="edit-category" class="form-label">Category</label>
                    <select class="form-control" id="edit-category"></select>
                </div>

                <div class="mb-3">
                    <label for="edit-description" class="form-label">Description</label>
                    <input type="text" class="form-control" id="edit-description">
                </div>

                <div class="mb-3">
                    <label for="edit-date" class="form-label">Date</label>
                    <input type="date" class="form-control" id="edit-date">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="updateExpense()">Save Changes</button>
            </div>
        </div>
    </div>
</div>

    </div>
<!-- Bootstrap JS (Popper.js is required for modals) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            fetchExpenses();
            fetchCategories();
        });

        function fetchExpenses() {
            fetch("<?= base_url('api/expenses'); ?>", {
                method: "GET",
                headers: {
                    "Authorization": "Bearer " + localStorage.getItem("jwt_token"),
                    "Content-Type": "application/json"
                }
            })
            .then(response => response.json())
            .then(data => {
                const tbody = document.querySelector("#expenses-table tbody");
                tbody.innerHTML = "";

                data.forEach(expense => {
                    const row = document.createElement("tr");
                    row.innerHTML = `
                        <td>${expense.id}</td>
                        <td>${expense.amount}</td>
                        <td>${expense.category}</td>
                        <td>${expense.description}</td>
                        <td>${expense.date}</td>
                        <td>
                            <button class="btn btn-warning btn-sm" onclick="editExpense(${expense.id})">Edit</button>
                            <button class="btn btn-danger btn-sm" onclick="deleteExpense(${expense.id})">Delete</button>
                        </td>
                    `;
                    tbody.appendChild(row);
                });
            })
            .catch(error => console.error("Error fetching expenses:", error));
        }

        function fetchCategories() {
            fetch("<?= base_url('api/categories'); ?>", {
                method: "GET",
                headers: {
                    "Authorization": "Bearer " + localStorage.getItem("jwt_token"),
                    "Content-Type": "application/json"
                }
            })
            .then(response => response.json())
            .then(data => {
                const categorySelect = document.getElementById("expense-category");
                categorySelect.innerHTML = "";
                data.forEach(category => {
                    categorySelect.innerHTML += `<option value="${category.id}">${category.category_name}</option>`;
                });
            })
            .catch(error => console.error("Error fetching categories:", error));
        }

        function addExpense() {
            const amount = document.getElementById("expense-amount").value;
            const category_id = document.getElementById("expense-category").value;
            const description = document.getElementById("expense-description").value;
            const date = document.getElementById("expense-date").value;

            if (!amount || !category_id || !description || !date) {
                alert("Please fill all fields.");
                return;
            }

            fetch("<?= base_url('api/expenses/add'); ?>", {
                method: "POST",
                headers: {
                    "Authorization": "Bearer " + localStorage.getItem("jwt_token"),
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({ amount, category_id, description, date })
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                hideExpenseForm();
                fetchExpenses();
            })
            .catch(error => console.error("Error adding expense:", error));
        }

        function deleteExpense(expenseId) {
            if (!confirm("Are you sure you want to delete this expense?")) return;

            fetch("<?= base_url('api/expenses/delete/'); ?>" + expenseId, {
                method: "DELETE",
                headers: {
                    "Authorization": "Bearer " + localStorage.getItem("jwt_token"),
                    "Content-Type": "application/json"
                }
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                fetchExpenses();
            })
            .catch(error => console.error("Error deleting expense:", error));
        }

        function editExpense(expenseId) {
    fetch("<?= base_url('api/expenses/'); ?>" + expenseId, {
        method: "GET",
        headers: {
            "Authorization": "Bearer " + localStorage.getItem("jwt_token"),
            "Content-Type": "application/json"
        }
    })
    .then(response => response.json())
    .then(expense => {
        if (!expense) {
            alert("Expense not found");
            return;
        }

        document.getElementById("expense-id").value = expense.id;
        document.getElementById("edit-amount").value = expense.amount;
        document.getElementById("edit-description").value = expense.description;
        document.getElementById("edit-date").value = expense.date;

        fetchCategories(expense.category_id);

        var editModal = new bootstrap.Modal(document.getElementById("editExpenseModal"));
        editModal.show();
    })
    .catch(error => console.error("Error fetching expense details:", error));
}

        function fetchCategories(selectedCategoryId) {
            fetch("<?= base_url('api/categories'); ?>", {
                method: "GET",
                headers: {
                    "Authorization": "Bearer " + localStorage.getItem("jwt_token"),
                    "Content-Type": "application/json"
                }
            })
            .then(response => response.json())
            .then(categories => {
                let categorySelect = document.getElementById("edit-category");
                let categorySelectforaddform = document.getElementById("expense-category");

                categorySelect.innerHTML = "";
                categorySelectforaddform.innerHTML = "";

                categories.forEach(category => {
                    let option = `<option value="${category.id}">${category.category_name}</option>`;
                    
                    categorySelect.innerHTML += option;
                    categorySelectforaddform.innerHTML += option;
                });

                if (selectedCategoryId) {
                    categorySelect.value = selectedCategoryId;
                }
            })

            .catch(error => console.error("Error fetching categories:", error));
        }

        function updateExpense() {
            const expenseId = document.getElementById("expense-id").value;
            const updatedData = {
                amount: document.getElementById("edit-amount").value,
                category_id: document.getElementById("edit-category").value,
                description: document.getElementById("edit-description").value,
                date: document.getElementById("edit-date").value
            };

            fetch("<?= base_url('api/expenses/update/'); ?>" + expenseId, {
                method: "PUT",
                headers: {
                    "Authorization": "Bearer " + localStorage.getItem("jwt_token"),
                    "Content-Type": "application/json"
                },
                body: JSON.stringify(updatedData)
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                fetchExpenses();
                var editModal = bootstrap.Modal.getInstance(document.getElementById("editExpenseModal"));
                editModal.hide();
            })
            .catch(error => console.error("Error updating expense:", error));
        }


        function showExpenseForm() {
            document.getElementById("expense-form").style.display = "block";
        }

        function hideExpenseForm() {
            document.getElementById("expense-form").style.display = "none";
        }
    </script>


<script>
    document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("exportCsvBtn").addEventListener("click", function () {
        exportExpenses("csv");
    });

    document.getElementById("exportPdfBtn").addEventListener("click", function () {
        exportExpenses("pdf");
    });

    document.getElementById("importCsvBtn").addEventListener("click", function () {
        document.getElementById("csvFileInput").click();
    });

    document.getElementById("csvFileInput").addEventListener("change", function () {
        importExpenses();
    });
});

const jwtToken = localStorage.getItem("jwt_token");

async function exportExpenses(format) {
    const year = new Date().getFullYear();
    const month = ("0" + (new Date().getMonth() + 1)).slice(-2);

    const url = `<?= base_url('api/export/'); ?>${format}?year=${year}&month=${month}`;

    try {
        const response = await fetch(url, {
            method: "GET",
            headers: {
                "Authorization": "Bearer " + jwtToken
            }
        });

        if (!response.ok) {
            throw new Error("Failed to export expenses");
        }

        const blob = await response.blob();
        const filename = `expenses_${year}_${month}.${format}`;
        const link = document.createElement("a");
        link.href = URL.createObjectURL(blob);
        link.download = filename;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    } catch (error) {
        alert("Error exporting expenses: " + error.message);
    }
}

async function importExpenses() {
    const fileInput = document.getElementById("csvFileInput");
    const file = fileInput.files[0];

    if (!file) {
        alert("Please select a CSV file.");
        return;
    }

    const formData = new FormData();
    formData.append("file", file);

    try {
        const response = await fetch("<?= base_url('api/import/csv'); ?>", {
            method: "POST",
            headers: {
                "Authorization": "Bearer " + jwtToken
            },
            body: formData
        });

        const result = await response.json();

        if (!response.ok) {
            throw new Error(result.error || "Failed to import expenses");
        }

        alert(result.message);
        fetchExpenses();
    } catch (error) {
        alert("Error importing expenses: " + error.message);
    }
}

</script>
</body>
</html>
