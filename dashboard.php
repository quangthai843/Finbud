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
        .main-card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-top: 50px;
        }
        .project-code {
            font-weight: bold;
            color: #333;
            font-size: 0.9em;
            margin-bottom: 5px;
        }
        .feature-container {
            margin-top: 20px;
        }
        .card {
            border: none;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            border-radius: 8px;
        }
        .card h5 {
            font-weight: bold;
            color: #333;
        }
        .card-image img {
            width: 100%;
            height: auto;
            border-radius: 8px;
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
    
    <div class="container main-card">
        <div class="row">
            <div class="col-md-8">
                <p class="project-code">Project Code: i05</p>
                <h1>Personal Finance Tracker (FinBud)</h1>
                <p class="text-primary">FinBud is a comprehensive personal finance tracker designed to help users manage their finances effortlessly.</p>
            </div>
            <div class="col-md-4">
                <div class="card-image">
                    <img src="savingtracker.png" alt="Savings Tracker Image">
                </div>
            </div>
        </div>
    </div>
    
    <!-- Feature Containers -->
    <div class="container feature-container">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Expense Tracking</h5>
                <p class="card-text">Easily log every transaction, categorize your spending, and keep track of where your money is going. This helps you identify unnecessary expenses and make adjustments to stay within your budget.</p>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Budget Creation</h5>
                <p class="card-text">Create personalized budgets for various expense categories. FinBud allows you to set monthly or weekly limits, helping you prevent overspending and encouraging smart saving habits.</p>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Financial Goal Setting</h5>
                <p class="card-text">Define and monitor your financial goals, whether saving for a vacation, a new gadget, or an emergency fund. With FinBud, track progress over time and stay motivated to reach your targets.</p>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Spending Pattern Visualization</h5>
                <p class="card-text">Gain insights into your spending behavior through visual charts and graphs. FinBud helps you understand patterns, so you can adjust your habits and make better financial decisions.</p>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Income Tracking</h5>
                <p class="card-text">Record all your income sources, including salary, side jobs, or passive income, to have a clear view of your cash flow and make adjustments where necessary.</p>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Detailed Financial Reports</h5>
                <p class="card-text">Generate monthly or quarterly reports to review your overall financial health. These reports make it easier to identify areas for improvement and to celebrate your achievements.</p>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.7/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
