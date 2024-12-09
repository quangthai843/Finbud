<?php
session_start();
include 'db.php'; // Connect to the database

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch income entries for the logged-in user
$incomes = [];
$sql = "SELECT income_id, income_category_id, amount, income_date, description FROM income WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $incomes[] = $row;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Income</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
</head>
<body>
    <div class="container">
        <h1>Your Income Entries</h1>
        
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Amount</th>
                    <th>Date</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($incomes as $income): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($income['income_category_id']); ?></td>
                        <td><?php echo number_format($income['amount'], 2); ?></td>
                        <td><?php echo htmlspecialchars($income['income_date']); ?></td>
                        <td><?php echo htmlspecialchars($income['description']); ?></td>
                        <td>
                            <button class="btn btn-primary" onclick="editIncome(<?php echo $income['income_id']; ?>)">Edit</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Edit Modal -->
        <div id="editModal" style="display: none;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Income</h5>
                        <button type="button" class="close" onclick="closeModal()">&times;</button>
                    </div>
                    <div class="modal-body">
                        <form id="editForm" action="update_income.php" method="post">
                            <input type="hidden" name="income_id" id="income_id">
                            <div class="form-group">
                                <label for="income_category_id">Category:</label>
                                <select name="income_category_id" id="income_category_id" class="form-control" required>
                                    <!-- Categories fetched dynamically -->
                                    <?php
                                    $sql = "SELECT income_category_id, category_name FROM income_category";
                                    $categories = $conn->query($sql);
                                    while ($category = $categories->fetch_assoc()) {
                                        echo '<option value="' . $category['income_category_id'] . '">' . $category['category_name'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="amount">Amount:</label>
                                <input type="number" name="amount" id="amount" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="income_date">Date:</label>
                                <input type="date" name="income_date" id="income_date" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="description">Description:</label>
                                <input type="text" name="description" id="description" class="form-control">
                            </div>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <script>
            function editIncome(incomeId) {
                $.ajax({
                    url: 'get_income.php',
                    type: 'GET',
                    data: { income_id: incomeId },
                    success: function(response) {
                        let income = JSON.parse(response);
                        $('#income_id').val(income.income_id);
                        $('#income_category_id').val(income.income_category_id);
                        $('#amount').val(income.amount);
                        $('#income_date').val(income.income_date);
                        $('#description').val(income.description);
                        $('#editModal').show();
                    }
                });
            }

            function closeModal() {
                $('#editModal').hide();
            }
        </script>
    </div>
</body>
</html>
