<?php
session_start();
include 'db.php'; // Kết nối cơ sở dữ liệu

// Kiểm tra nếu người dùng đã đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Budget</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="#">FinBud Dashboard</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link" href="dashboard.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="expensepage.php">Expenses</a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="budget.php">Budgets</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="finance_goalpage.php">Goals</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="add_income.php">Income</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="reportpage.php">Reports</a>
                </li>
            </ul>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </nav>
    
    <div class="container">
        <!-- Form để thêm ngân sách và thể loại -->
        <div class="row form-container mt-4">
            <div class="col-md-6">
                <h3>Add Budget with Category</h3>
                <form action="add_budget.php" method="post" class="mb-4">
                    <div class="form-group">
                        <label for="budget-amount">Amount:</label>
                        <input type="number" name="amount" id="budget-amount" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="category-id">Choose Category:</label>
                        <select name="category_id" id="category-id" class="form-control">
                            <!-- Categories sẽ được tải vào đây bằng AJAX -->
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="new-category">Or Add New Category:</label>
                        <input type="text" name="new_category" id="new-category" class="form-control" placeholder="Enter new category name">
                    </div>
                    <div class="form-group">
                        <label for="start-date">Start Date:</label>
                        <input type="date" name="start_date" id="start-date" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="end-date">End Date:</label>
                        <input type="date" name="end_date" id="end-date" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Budget</button>
                </form>
            </div>
        </div>

        <!-- Hiển thị danh sách Budgets với Category dưới dạng table -->
        <div class="row form-container mt-4">
            <div class="col-md-12">
                <h3>Your Budgets with Categories</h3>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered" id="budget-table">
                        <thead class="thead-dark">
                            <tr>
                                <th>Amount</th>
                                <th>Category</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Dữ liệu sẽ được tải vào đây từ AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.7/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    
    <!-- AJAX để tải Budgets và Categories -->
    <script>
        $(document).ready(function() {
            // Lấy danh sách Budgets
            $.ajax({
                url: 'fetch_budgets.php',
                method: 'GET',
                success: function(data) {
                    $('#budget-table tbody').html(data);
                }
            });
            
            // Lấy danh sách Categories
            $.ajax({
                url: 'fetch_categories.php',
                method: 'GET',
                success: function(data) {
                    $('#category-id').html(data);
                }
            });
        });
    </script>
</body>
</html>
