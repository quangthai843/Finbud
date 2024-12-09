<?php
// Start PHP code
session_start();
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
    <title>Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        body {
            background-color: #f7f5f2;
            font-family: 'Arial', sans-serif;
        }
        .dashboard-card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            display: flex;
            flex-wrap: wrap;
            padding: 20px;
            margin-top: 50px;
        }
        .dashboard-card h1 {
            font-size: 2em;
            font-weight: bold;
            color: #333;
        }
        .project-code {
            font-weight: bold;
            color: #333;
            font-size: 0.9em;
            margin-bottom: 5px;
        }
        .card-content {
            flex: 1;
        }
        .card-image {
            max-width: 300px;
            margin-left: auto;
            margin-right: 0;
        }
        .card-image img {
            width: 100%;
            height: auto;
            border-radius: 8px;
        }
        .text-primary {
            color: #e68a00;
        }
        .form-container {
            margin-top: 30px;
        }
        .form-container h3 {
            color: #333;
        }
    </style>
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
                <li class="nav-item active">
                    <a class="nav-link" href="dashboard.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="expensepage.php">Expenses</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="budget.php">Budgets</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="finance_goal.php">Goals</a>
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
        <div class="dashboard-card row">
            <div class="card-content col-md-8">
                <p class="project-code">Project Code: i05</p>
                <h1>Personal Finance Tracker<br>(FinBud)</h1>
                <p class="text-primary">FinBud is a comprehensive personal finance tracker designed to help users manage their finances effortlessly.</p>
                <p>Users can track their expenses, create budgets and set financial goals.</p>
                <p>They can also categorize expenses, visualize spending patterns, and receive insights to improve financial health.</p>
                <p>FinBud is perfect for individuals seeking to gain control over their finances and achieve their financial goals efficiently.</p>
            </div>
            <div class="card-image col-md-4">
                <img src="savingtracker.png" alt="Savings Tracker Image">
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.7/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    
    <!-- AJAX to fetch Budgets and Categories -->
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
