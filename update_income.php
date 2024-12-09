<?php
include 'db.php'; // Connect to the database

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['income_id'], $_POST['income_category_id'], $_POST['amount'], $_POST['income_date'], $_POST['description'])) {
    $income_id = $_POST['income_id'];
    $income_category_id = $_POST['income_category_id'];
    $amount = $_POST['amount'];
    $income_date = $_POST['income_date'];
    $description = $_POST['description'];

    // Update income record
    $sql = "UPDATE income SET income_category_id = ?, amount = ?, income_date = ?, description = ? WHERE income_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("idssi", $income_category_id, $amount, $income_date, $description, $income_id);

    if ($stmt->execute()) {
        header("Location: add_income.php"); // Redirect after successful update
        exit();
    } else {
        echo "Error updating income: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request.";
}
?>
