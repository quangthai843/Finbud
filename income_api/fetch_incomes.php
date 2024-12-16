<?php
session_start();
include '../db.php'; // Kết nối cơ sở dữ liệu

header('Content-Type: application/json');

// Kiểm tra nếu người dùng đã đăng nhập
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user_id'];

// Truy vấn dữ liệu từ bảng income và income_category
$sql = "SELECT i.income_id, i.user_id, i.income_category_id, ic.category_name, 
               i.amount, i.income_date, i.description 
        FROM income i
        JOIN income_category ic ON i.income_category_id = ic.income_category_id
        WHERE i.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();

// Chuẩn bị dữ liệu trả về
$incomes = [];
while ($row = $result->fetch_assoc()) {
    $incomes[] = [
        'income_id' => $row['income_id'],
        'user_id' => $row['user_id'],
        'income_category_id' => $row['income_category_id'],
        'category_name' => $row['category_name'],
        'amount' => (float) $row['amount'], // Ép kiểu thành số thực
        'income_date' => $row['income_date'],
        'description' => $row['description'],
    ];
}

// Trả về dữ liệu dưới dạng JSON
echo json_encode([
    'status' => 'success',
    'incomes' => $incomes,
]);

// Đóng kết nối
$stmt->close();
$conn->close();
?>
