<?php
include 'admin/dbinit.php';

echo "<pre>";
echo "ALL ACADEMIC_TEACHERS ACCOUNTS\n";
echo "=====================================\n\n";

$sql = "SELECT id, full_name, email, password FROM academic_teachers ORDER BY id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "ID: " . $row['id'] . " | Full Name: '" . $row['full_name'] . "' | Email: '" . $row['email'] . "'\n";
    }
} else {
    echo "No accounts found";
}

echo "\n\n=====================================\n";
echo "TEST QUERY: WHERE full_name = 'dean_CCS' OR email = 'dean_CCS'\n";
echo "=====================================\n\n";

$identifier = "dean_CCS";
$sql = "SELECT id, full_name, email FROM academic_teachers WHERE full_name = ? OR email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $identifier, $identifier);
$stmt->execute();
$result = $stmt->get_result();

echo "Found " . $result->num_rows . " matches:\n\n";
while($row = $result->fetch_assoc()) {
    echo "ID: " . $row['id'] . " | Full Name: '" . $row['full_name'] . "' | Email: '" . $row['email'] . "'\n";
}

$stmt->close();
$conn->close();
?>
