<?php 
date_default_timezone_set('Asia/Kolkata');  // Change to your required timezone  

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expenses</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body></body>
<div class="container mt-4">
    <h2>Category List</h2>

    <button class="btn btn-primary" id="addCategoryBtn">Add Category</button>

    <div id="categoryForm" class="mt-3" style="display: none;">
        <input type="text" id="categoryName" class="form-control" placeholder="Enter Category Name">
        <button class="btn btn-success mt-2" id="saveCategoryBtn">Save Category</button>
    </div>

    <table class="table table-bordered mt-3" id="categories-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Category Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>

    <a href="<?= base_url('dashboard'); ?>" class="btn btn-secondary">Back to Home</a>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const jwtToken = localStorage.getItem("jwt_token");

    document.getElementById("addCategoryBtn").addEventListener("click", function() {
        document.getElementById("categoryForm").style.display = "block";
    });

    document.getElementById("saveCategoryBtn").addEventListener("click", function() {
        const categoryName = document.getElementById("categoryName").value;

        if (!categoryName) {
            alert("Please enter a category name.");
            return;
        }

        fetch("<?= base_url('api/categories/add'); ?>", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Authorization": "Bearer " + jwtToken
            },
            body: JSON.stringify({ category_name: categoryName })
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            location.reload();
        })
        .catch(error => console.error("Error adding category:", error));
    });

    function fetchCategories() {
        fetch("<?= base_url('api/categories'); ?>", {
            method: "GET",
            headers: { "Authorization": "Bearer " + jwtToken }
        })
        .then(response => response.json())
        .then(data => {
            let rows = "";
            data.forEach(category => {
                rows += `<tr id="row-${category.id}">
    <td>${category.id}</td>
    <td>
        <!-- Display category name and hidden input for editing -->
        <span id="category-name-${category.id}">${category.category_name}</span>
        <input type="text" id="edit-category-${category.id}" value="${category.category_name}" class="form-control" style="display: none;">
    </td>
    <td>
        <!-- Edit Button -->
        <button class="btn btn-warning btn-sm" id="edit-btn-${category.id}" onclick="editCategory(${category.id})">Edit</button>
        
        <!-- Save Button -->
        <button class="btn btn-success btn-sm" style="display: none;" id="save-btn-${category.id}" onclick="updateCategory(${category.id})">Save</button>
        
        <!-- Delete Button -->
        <button class="btn btn-danger btn-sm" onclick="deleteCategory(${category.id})">Delete</button>
    </td>
</tr>`;

            });
            document.querySelector("#categories-table tbody").innerHTML = rows;
        })
        .catch(error => console.error("Error fetching categories:", error));
    }

    window.editCategory = function(categoryId) {
    let categoryNameSpan = document.getElementById(`category-name-${categoryId}`);
    let categoryEditInput = document.getElementById(`edit-category-${categoryId}`);
    let saveBtn = document.getElementById(`save-btn-${categoryId}`);
    let editBtn = document.getElementById(`edit-btn-${categoryId}`);

    if (categoryNameSpan && categoryEditInput && saveBtn && editBtn) {
        categoryNameSpan.style.display = "none";
        categoryEditInput.style.display = "inline";
        saveBtn.style.display = "inline";
        editBtn.style.display = "none";
    } else {
        console.error(`Error: One or more elements not found for category ID: ${categoryId}`);
    }
};



window.updateCategory = function(categoryId) {
    const newCategoryName = document.getElementById(`edit-category-${categoryId}`).value;

    fetch("<?= base_url('api/categories/update/'); ?>" + categoryId, {
        method: "PUT",
        headers: {
            "Content-Type": "application/json",
            "Authorization": "Bearer " + jwtToken
        },
        body: JSON.stringify({ category_name: newCategoryName })
    })
    .then(response => response.json())
    .then(data => {
        if (data.message) {
            alert(data.message);

            document.getElementById(`category-name-${categoryId}`).innerText = newCategoryName;
            document.getElementById(`category-name-${categoryId}`).style.display = "inline";
            document.getElementById(`edit-category-${categoryId}`).style.display = "none";
            document.getElementById(`save-btn-${categoryId}`).style.display = "none";
            document.getElementById(`edit-btn-${categoryId}`).style.display = "inline";
        } else {
            alert("Failed to update category");
        }
    })
    .catch(error => console.error("Error updating category:", error));
};


window.deleteCategory = function(categoryId) {
    if (!confirm("Are you sure you want to delete this category?")) return;

    fetch("<?= base_url('api/categories/delete/'); ?>" + categoryId, {
        method: "DELETE",
        headers: {
            "Authorization": "Bearer " + jwtToken
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.message) {
            alert(data.message);
            document.getElementById(`row-${categoryId}`).remove();
        } else {
            alert("Failed to delete category");
        }
    })
    .catch(error => console.error("Error deleting category:", error));
};


    fetchCategories();
});
</script>
</body>
</html>