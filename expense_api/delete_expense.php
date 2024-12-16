<?php
session_start();
header('Content-Type: application/json'); // Đặt kiểu phản hồi là JSON
include '../db.php'; // Kết nối cơ sở dữ liệu

$response = [
    "status" => "error",
    "message" => "Invalid request"
];

// Kiểm tra xem người dùng đã đăng nhập hay chưa
if (!isset($_SESSION['user_id'])) {
    http_response_code(401); // Unauthorized
    $response["message"] = "You must be logged in to perform this action.";
    echo json_encode($response);
    exit();
}

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $expense_id = intval($_GET['id']); // Đảm bảo là số nguyên
    $user_id = $_SESSION['user_id'];

    // Chuẩn bị câu lệnh xóa
    $delete_sql = "DELETE FROM expenses_transaction WHERE expense_transaction_id = ? AND user_id = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("ii", $expense_id, $user_id);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            // Xóa thành công
            $response["status"] = "success";
            $response["message"] = "Expense deleted successfully.";
            header("Location: ../expensepage.php");
        } else {
            // Không tìm thấy giao dịch để xóa
            http_response_code(404); // Not Found
            $response["message"] = "Expense not found or you do not have permission to delete it.";
        }
    } else {
        // Lỗi khi thực hiện câu lệnh SQL
        http_response_code(500); // Internal Server Error
        $response["message"] = "Failed to delete expense. Please try again later.";
    }
    $stmt->close();
} else {
    // Nếu không có ID hoặc ID không hợp lệ
    http_response_code(400); // Bad Request
    $response["message"] = "Invalid expense ID.";
}

$conn->close();
echo json_encode($response);
?>
