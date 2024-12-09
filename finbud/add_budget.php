<?php
include 'db.php'; // Kết nối cơ sở dữ liệu
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$user_id = $_SESSION['user_id'];
$amount = $_POST['amount'];
$category_id = $_POST['category_id'];
$new_category = $_POST['new_category'];
$start_date = $_POST['start_date'];
$end_date = $_POST['end_date'];

// Kiểm tra nếu người dùng nhập một Category mới
if (!empty($new_category)) {
    // Thêm Category mới vào cơ sở dữ liệu
    $sql = "INSERT INTO Categories (category_name) VALUES (?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $new_category);
    $stmt->execute();
    $category_id = $stmt->insert_id; // Lấy category_id mới vừa thêm
    $stmt->close();
} else {
    // Kiểm tra xem category_id đã được chọn chưa
    if (empty($category_id)) {
        die("Error: Please select an existing category or enter a new category.");
    }
}

// Thêm Budget vào cơ sở dữ liệu với Category ID đã có hoặc mới
$sql = "INSERT INTO Budgets (user_id, amount, category_id, start_date, end_date) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iidss", $user_id, $amount, $category_id, $start_date, $end_date);

if ($stmt->execute()) {
    echo "Budget added successfully!";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
