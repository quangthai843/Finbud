<?php
include 'db.php'; // Database connection

if (isset($_GET['goal_id'])) {
    $goal_id = $_GET['goal_id'];

    // Prepare SQL to fetch the financial goal
    $sql = "SELECT goal_id, goal_name, target_amount, current_amount, target_date FROM financialgoals WHERE goal_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $goal_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the goal was found
    if ($result->num_rows > 0) {
        $goal = $result->fetch_assoc();
        echo json_encode($goal);
    } else {
        echo json_encode(["error" => "Goal not found"]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["error" => "Invalid request"]);
}
?>
