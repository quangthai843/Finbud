<?php
session_start();
include 'db.php'; // Kết nối cơ sở dữ liệu

if (!isset($_SESSION['user_id'])) {
    exit();
}

$user_id = $_SESSION['user_id'];

// Truy vấn để lấy Budget và Category tương ứng
$sql = "SELECT b.amount, b.start_date, b.end_date, c.category_name
        FROM Budgets b
        JOIN Categories c ON b.category_id = c.category_id
        WHERE b.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$budgets = '';
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $budgets .= "<tr>
                        <td>" . number_format($row['amount'], 2) . "</td>
                        <td>" . htmlspecialchars($row['category_name']) . "</td>
                        <td>" . htmlspecialchars($row['start_date']) . "</td>
                        <td>" . htmlspecialchars($row['end_date']) . "</td>
                     </tr>";
    }
} else {
    $budgets = "<tr><td colspan='4' class='text-center'>No budgets found</td></tr>";
}

echo $budgets;

$stmt->close();
$conn->close();
?>
