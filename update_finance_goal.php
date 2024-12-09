<?php
include 'db.php'; // Database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['goal_id'], $_POST['goal_name'], $_POST['target_amount'], $_POST['current_amount'], $_POST['target_date'])) {
    $goal_id = $_POST['goal_id'];
    $goal_name = $_POST['goal_name'];
    $target_amount = $_POST['target_amount'];
    $current_amount = $_POST['current_amount'];
    $target_date = $_POST['target_date'];

    // Update the financial goal in the database
    $sql = "UPDATE financialgoals SET goal_name = ?, target_amount = ?, current_amount = ?, target_date = ? WHERE goal_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sddsi", $goal_name, $target_amount, $current_amount, $target_date, $goal_id);

    if ($stmt->execute()) {
        header("Location: finance_goal.php"); // Redirect after successful update
        exit();
    } else {
        echo "Error updating financial goal: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request.";
}
?>
