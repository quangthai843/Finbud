<?php
session_start();
include '../db.php'; // Kết nối CSDL

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Truy vấn tất cả Expense Transactions của user
$sql = "SELECT et.expense_transaction_id, et.amount, et.expense_date, et.description, c.category_name, sc.sub_category_name 
        FROM `expenses_transaction` et
        JOIN Categories c ON et.category_id = c.category_id
        LEFT JOIN sub_category sc ON et.sub_category_id = sc.sub_category_id
        WHERE et.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Thiết lập header để xuất file CSV
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="expenses.csv"');

// Mở output stream
$output = fopen('php://output', 'w');

// Ghi dòng tiêu đề cột
fputcsv($output, array('Amount', 'Date', 'Category', 'Sub-Category', 'Description'));

// Ghi dữ liệu từng dòng
while ($row = $result->fetch_assoc()) {
    fputcsv($output, array(
        $row['amount'],
        $row['expense_date'],
        $row['category_name'],
        $row['sub_category_name'],
        $row['description']
    ));
}

// Đóng kết nối
fclose($output);
$stmt->close();
$conn->close();
exit();
