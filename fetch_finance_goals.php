<?php
session_start();
header('Content-Type: application/json');
include 'db.php'; // Kết nối cơ sở dữ liệu

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ID người dùng (ví dụ lấy từ session)
$user_id = 3;

// Câu SQL lấy financial goals
$sqlGoals = "SELECT goal_id, goal_name, target_amount, current_amount, target_date 
             FROM financialgoals 
             WHERE user_id = ?";
$stmt = $conn->prepare($sqlGoals);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Dữ liệu trả về
$goals = [];
while ($row = $result->fetch_assoc()) {
    $goals[] = $row;
}

// Trả dữ liệu JSON
header('Content-Type: application/json');
echo json_encode(['status' => 'success', 'data' => $goals]);
?>
