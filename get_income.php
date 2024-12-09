<?php
include 'db.php'; // Connect to the database

if (isset($_GET['income_id'])) {
    $income_id = $_GET['income_id'];

    // Fetch income details
    $sql = "SELECT income_id, income_category_id, amount, income_date, description FROM income WHERE income_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $income_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $income = $result->fetch_assoc();
        echo json_encode($income);
    } else {
        echo json_encode(["error" => "Income not found"]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["error" => "Invalid request"]);
}
?>
