<?php
session_start();
header('Content-Type: application/json'); // Đảm bảo nội dung trả về là JSON
include '../db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user_id'];

// Lấy tháng từ request hoặc mặc định là tháng hiện tại
$currentMonth = isset($_GET['month']) ? $_GET['month'] : date('Y-m');

// Truy vấn dữ liệu tổng số tiền theo danh mục
$sql = "SELECT c.category_name, SUM(et.amount) AS total_amount
        FROM expenses_transaction et
        JOIN Categories c ON et.category_id = c.category_id
        WHERE et.user_id = ? AND DATE_FORMAT(et.expense_date, '%Y-%m') = ?
        GROUP BY c.category_name";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['status' => 'error', 'message' => $conn->error]);
    exit();
}
$stmt->bind_param("is", $user_id, $currentMonth);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
$totalExpense = 0;

// Tính tổng và lưu dữ liệu
while ($row = $result->fetch_assoc()) {
    $totalExpense += $row['total_amount'];
    $data[] = $row;
}

// Tính tỷ lệ phần trăm
if ($totalExpense > 0) {
    foreach ($data as &$category) {
        $category['percentage'] = round(($category['total_amount'] / $totalExpense) * 100, 2);
    }
}

echo json_encode(['status' => 'success', 'data' => $data]);
$stmt->close();
$conn->close();
?>
