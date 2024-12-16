<?php
session_start();
include '../db.php';

// Kiểm tra nếu người dùng đã đăng nhập
if (!isset($_SESSION['user_id'])) {
    echo "Error: User not logged in.";
    exit();
}

// Kiểm tra nếu budget_id được gửi qua GET
if (!isset($_GET['id'])) {
    echo "Error: No budget ID provided.";
    exit();
}

// Gán giá trị budget_id và user_id
$budget_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Chuẩn bị câu truy vấn DELETE
$delete_sql = "DELETE FROM Budgets WHERE budget_id = ? AND user_id = ?";
$stmt = $conn->prepare($delete_sql);

// Kiểm tra lỗi khi chuẩn bị truy vấn
if (!$stmt) {
    echo "Error: Unable to prepare SQL statement.";
    exit();
}

// Gán giá trị vào câu truy vấn
$stmt->bind_param("ii", $budget_id, $user_id);

// Thực thi câu truy vấn
if ($stmt->execute()) {
    echo "success";
} else {
    echo "Error: Unable to delete budget.";
}

// Đóng statement và kết nối
$stmt->close();
$conn->close();
?>
