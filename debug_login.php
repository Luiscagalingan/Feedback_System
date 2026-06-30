<?php
include 'admin/dbinit.php';

echo "<pre>";
echo "CHECKING LOGIN FLOW FOR dean_CCS\n";
echo "=====================================\n\n";

$identifier = "dean_CCS";
$password = "password123";

// Step 1: Check Admin
echo "STEP 1: Checking ADMINS table\n";
$sql = "SELECT id, username FROM admins WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $identifier);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    echo "✓ Found in ADMINS\n";
} else {
    echo "✗ Not found in ADMINS\n";
}
$stmt->close();

// Step 2: Check Students  
echo "\nSTEP 2: Checking STUDENTS table\n";
$sql = "SELECT id, student_id FROM students WHERE student_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $identifier);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo "✓ Found in STUDENTS: ID=" . $row['id'] . ", student_id=" . $row['student_id'] . "\n";
} else {
    echo "✗ Not found in STUDENTS\n";
}
$stmt->close();

// Step 3: Check Academic Teachers
echo "\nSTEP 3: Checking ACADEMIC_TEACHERS table\n";
echo "Query: WHERE full_name = ? OR email = ?\n";
echo "Searching for: '$identifier'\n\n";

$sql = "SELECT id, full_name, email, password FROM academic_teachers WHERE full_name = ? OR email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $identifier, $identifier);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "✓ Found " . $result->num_rows . " match(es) in ACADEMIC_TEACHERS:\n\n";
    while($row = $result->fetch_assoc()) {
        echo "ID: " . $row['id'] . "\n";
        echo "Full Name: '" . $row['full_name'] . "'\n";
        echo "Email: '" . $row['email'] . "'\n";
        echo "Password: '" . $row['password'] . "'\n";
        
        // Check password match
        if ($password === $row['password']) {
            echo "✓ Password MATCHES\n";
        } else {
            echo "✗ Password does NOT match\n";
        }
        
        // Check if it's a dean
        $fullName = strtolower($row['full_name']);
        $deanColleges = ['ccs', 'bsa', 'cas', 'con', 'coe', 'cihm'];
        $isDean = false;
        $college = null;
        
        echo "\nDean Detection:\n";
        echo "  Lowercase: '$fullName'\n";
        echo "  Contains 'dean': " . (strpos($fullName, 'dean') !== false ? 'YES' : 'NO') . "\n";
        
        if (strpos($fullName, 'dean') !== false) {
            foreach ($deanColleges as $col) {
                if (strpos($fullName, $col) !== false) {
                    $isDean = true;
                    $college = strtoupper($col);
                    echo "  Found college: $college\n";
                    break;
                }
            }
        }
        
        if ($isDean && $college) {
            echo "  ✓ DETECTED AS DEAN - College: $college\n";
        } else {
            echo "  ✗ DETECTED AS ACADEMIC (not dean)\n";
        }
        echo "\n---\n";
    }
} else {
    echo "✗ Not found in ACADEMIC_TEACHERS\n";
}
$stmt->close();

$conn->close();
?>
