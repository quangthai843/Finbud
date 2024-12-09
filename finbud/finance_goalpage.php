<?php
session_start();
include 'db.php'; // Kết nối cơ sở dữ liệu

// Kiểm tra nếu người dùng đã đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Process Financial Goals form
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_goal'])) {
    $goal_name = $_POST['goal_name'];
    $target_amount = $_POST['target_amount'];
    $target_date = $_POST['target_date'];

    $sql = "INSERT INTO financialgoals (user_id, goal_name, target_amount, current_amount, target_date) VALUES (?, ?, ?, 0, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isds", $user_id, $goal_name, $target_amount, $target_date);

    if ($stmt->execute()) {
        $goal_success = "Financial goal added successfully!";
    } else {
        $goal_error = "Error: " . $stmt->error;
    }

    $stmt->close();
}


// Process saving transaction form
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_saving'])) {
    $goal_id = $_POST['goal_id'];
    $amount = $_POST['amount'];
    $transaction_date = $_POST['transaction_date'];
    $description = $_POST['description'];

    // Insert saving transaction
    $sql = "INSERT INTO saving_transaction (user_id, goal_id, amount, transaction_date, description) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iidss", $user_id, $goal_id, $amount, $transaction_date, $description);

    if ($stmt->execute()) {
        // Update current_amount in FinancialGoals
        $update_goal_sql = "UPDATE financialgoals SET current_amount = current_amount + ? WHERE goal_id = ?";
        $update_stmt = $conn->prepare($update_goal_sql);
        $update_stmt->bind_param("di", $amount, $goal_id);
        $update_stmt->execute();
        $update_stmt->close();

        $saving_success = "Saving transaction added successfully!";
    } else {
        $saving_error = "Error: " . $stmt->error;
    }

    $stmt->close();
}


// Lấy danh sách FinancialGoals cho user_id để chọn trong saving Transaction và hiển thị bảng
$goals = [];
$sql = "SELECT goal_id, goal_name, target_amount, current_amount, target_date FROM financialgoals WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $goals[] = $row;
}
$stmt->close();
// Lấy danh sách Saving Transactions của người dùng hiện tại
$saving_transactions = [];
$sql = "SELECT st.saving_transaction_id, fg.goal_name, st.amount, st.transaction_date, st.description 
        FROM saving_transaction AS st 
        JOIN financialgoals AS fg ON st.goal_id = fg.goal_id 
        WHERE st.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $saving_transactions[] = $row;
}
$stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finance Goal</title>
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
                <li class="nav-item">
                    <a class="nav-link" href="expensepage.php">Expenses</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="budget.php">Budgets</a>
                </li>
                <li class="nav-item active">
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
<div class="container mt-5">
    <h2>Finance Goal</h2>

    <!-- Thông báo thành công hoặc lỗi khi thêm FinancialGoal -->
    <?php if (isset($goal_success)) echo "<div class='alert alert-success'>$goal_success</div>"; ?>
    <?php if (isset($goal_error)) echo "<div class='alert alert-danger'>$goal_error</div>"; ?>

    <!-- Form thêm Financial Goal -->
    <form action="finance_goalpage.php" method="post" class="mb-5">
        <h3>Add Financial Goal</h3>
        <input type="hidden" name="add_goal" value="1">
        <div class="form-group">
            <label for="goal_name">Goal Name:</label>
            <input type="text" name="goal_name" id="goal_name" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="target_amount">Target Amount:</label>
            <input type="number" name="target_amount" id="target_amount" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="target_date">Target Date:</label>
            <input type="date" name="target_date" id="target_date" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Add Goal</button>
    </form>

    <!-- Hiển thị bảng Financial Goals -->
    <h3>Your Financial Goals</h3>
    <table class="table table-bordered">
        <thead class="thead-dark">
            <tr>
                <th>Goal Name</th>
                <th>Target Amount</th>
                <th>Current Amount</th>
                <th>Target Date</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($goals) > 0): ?>
                <?php foreach ($goals as $goal): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($goal['goal_name']); ?></td>
                        <td><?php echo number_format($goal['target_amount'], 2); ?></td>
                        <td><?php echo number_format($goal['current_amount'], 2); ?></td>
                        <td><?php echo htmlspecialchars($goal['target_date']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" class="text-center">No financial goals found</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Thông báo thành công hoặc lỗi khi thêm saving Transaction -->
    <?php if (isset($saving_success)) echo "<div class='alert alert-success'>$saving_success</div>"; ?>
    <?php if (isset($saving_error)) echo "<div class='alert alert-danger'>$saving_error</div>"; ?>

    <!-- Form thêm saving Transaction -->
    <form action="finance_goalpage.php" method="post">
        <h3>Add Saving Transaction</h3>
        <input type="hidden" name="add_saving" value="1">
        <div class="form-group">
            <label for="goal_id">Select Goal:</label>
            <select name="goal_id" id="goal_id" class="form-control" required>
                <?php 
                foreach ($goals as $goal) {
                    echo '<option value="' . htmlspecialchars($goal['goal_id']) . '">' . htmlspecialchars($goal['goal_name']) . '</option>';
                }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="amount">Amount:</label>
            <input type="number" name="amount" id="amount" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="transaction_date">Transaction Date:</label>
            <input type="date" name="transaction_date" id="transaction_date" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="description">Description:</label>
            <input type="text" name="description" id="description" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Add Saving Transaction</button>
    </form>
    <h2>Your Saving Transactions</h2>
    <!-- Hiển thị bảng Saving Transactions -->
    <table class="table table-bordered mt-4">
        <thead class="thead-dark">
            <tr>
                <th>Transaction ID</th>
                <th>Goal Name</th>
                <th>Amount</th>
                <th>Transaction Date</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($saving_transactions) > 0): ?>
                <?php foreach ($saving_transactions as $transaction): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($transaction['saving_transaction_id']); ?></td>
                        <td><?php echo htmlspecialchars($transaction['goal_name']); ?></td>
                        <td><?php echo number_format($transaction['amount'], 2); ?></td>
                        <td><?php echo htmlspecialchars($transaction['transaction_date']); ?></td>
                        <td><?php echo htmlspecialchars($transaction['description']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center">No saving transactions found</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
