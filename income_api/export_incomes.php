<?php
session_start();
include '../db.php'; // Kết nối cơ sở dữ liệu

// Kiểm tra nếu người dùng đã đăng nhập
if (!isset($_SESSION['user_id'])) {
    die('Unauthorized');
}

$user_id = $_SESSION['user_id'];

// Truy vấn dữ liệu thu nhập
$sql = "SELECT i.income_id, ic.category_name, i.amount, i.income_date, i.description
        FROM income i
        JOIN income_category ic ON i.income_category_id = ic.income_category_id
        WHERE i.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Đặt header để trình duyệt tải file CSV
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=incomes.csv');

    // Tạo file output
    $output = fopen('php://output', 'w');

    // Ghi tiêu đề cột
    fputcsv($output, ['Category', 'Amount', 'Date', 'Description']);

    // Ghi dữ liệu từ cơ sở dữ liệu vào CSV
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, [
            $row['category_name'],
            number_format($row['amount'], 2), // Format số tiền
            $row['income_date'],
            $row['description']
        ]);
    }

    fclose($output);
    exit();
} else {
    die('No income records found to export.');
}
?>
