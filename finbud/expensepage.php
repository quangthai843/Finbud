<?php
session_start();
include 'db.php'; // Kết nối cơ sở dữ liệu

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch categories from the database
$sql = "SELECT category_id, category_name FROM Categories";
$result = $conn->query($sql);

// Fetch remaining budgets and corresponding category names for the user
$remaining_budget_sql = "SELECT rb.remaining_budget, c.category_name
                         FROM remaining_budget rb
                         JOIN Budgets b ON rb.budget_id = b.budget_id
                         JOIN Categories c ON b.category_id = c.category_id
                         WHERE b.user_id = ?";
$remaining_budget_stmt = $conn->prepare($remaining_budget_sql);
$remaining_budget_stmt->bind_param("i", $user_id);
$remaining_budget_stmt->execute();
$remaining_budget_result = $remaining_budget_stmt->get_result();

// Fetch expense transactions for the user
$expense_sql = "SELECT et.expense_transaction_id, et.amount, et.expense_date, et.description, c.category_name
                FROM `expenses_Transaction` et
                JOIN Categories c ON et.category_id = c.category_id
                WHERE et.user_id = ?";
$expense_stmt = $conn->prepare($expense_sql);
$expense_stmt->bind_param("i", $user_id);
$expense_stmt->execute();
$expense_result = $expense_stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Expense</title>
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
                <li class="nav-item ">
                    <a class="nav-link" href="dashboard.php">Home</a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="expensepage.php">Expenses</a>
                </li>
                <li class="nav-item">
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

            <!-- Display Remaining Budget Table -->
            <h4>Remaining Budget</h4>
            <div class="table-responsive container">
            <table class="table table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>Category Name</th>
                        <th>Remaining Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($remaining_budget_result->num_rows > 0) {
                        while ($row = $remaining_budget_result->fetch_assoc()) {
                            echo "<tr>
                                    <td>" . htmlspecialchars($row['category_name']) . "</td>
                                    <td>" . number_format($row['remaining_budget'], 2) . "</td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='2' class='text-center'>No remaining budget found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

    <!-- Display Expense Transactions Table -->
    <h4>Expense Transactions</h4>
        <div class="table-responsive container">
            <table class="table table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>Transaction ID</th>
                        <th>Amount</th>
                        <th>Date</th>
                        <th>Category</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($expense_result->num_rows > 0) {
                        while ($row = $expense_result->fetch_assoc()) {
                            echo "<tr>
                                    <td>" . htmlspecialchars($row['expense_transaction_id']) . "</td>
                                    <td>" . number_format($row['amount'], 2) . "</td>
                                    <td>" . htmlspecialchars($row['expense_date']) . "</td>
                                    <td>" . htmlspecialchars($row['category_name']) . "</td>
                                    <td>" . htmlspecialchars($row['description']) . "</td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5' class='text-center'>No expense transactions found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

    <div class="container mt-5">
        <h2>Add Expense Transaction</h2>
        <form action="process_expense.php" method="post">
            <div class="form-group">
                <label for="amount">Amount:</label>
                <input type="number" step="0.01" name="amount" id="amount" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="expense_date">Expense Date:</label>
                <input type="date" name="expense_date" id="expense_date" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="category_id">Category:</label>
                <select name="category_id" id="category_id" class="form-control" required>
                    <option value="">Select Category</option>
                    <?php
                    // Populate the dropdown with categories
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='" . $row['category_id'] . "'>" . htmlspecialchars($row['category_name']) . "</option>";
                        }
                    } else {
                        echo "<option value=''>No categories available</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea name="description" id="description" class="form-control" rows="3"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Add Expense</button>
        </form>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.7/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php
// Close database connections
$remaining_budget_stmt->close();
$expense_stmt->close();
$conn->close();
?>
