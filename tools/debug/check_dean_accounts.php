<?php
include 'admin/dbinit.php';

$sql = "SELECT id, full_name, email FROM academic_teachers WHERE full_name LIKE '%dean%' OR full_name LIKE '%CCS%' OR full_name LIKE '%BSA%' OR full_name LIKE '%CAS%' OR full_name LIKE '%CON%' OR full_name LIKE '%COE%' OR full_name LIKE '%CIHM%'";
$result = $conn->query($sql);

echo "<pre>";
echo "Dean Accounts in Database:\n";
echo "================================\n";

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "ID: " . $row["id"] . "\n";
        echo "Full Name: '" . $row["full_name"] . "'\n";
        echo "Email: " . $row["email"] . "\n";
        echo "---\n";
    }
} else {
    echo "No dean accounts found.\n";
}

echo "\n================================\n";
echo "Testing detection logic:\n";
echo "================================\n";

$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $fullName = strtolower($row['full_name']);
        $deanColleges = ['ccs', 'bsa', 'cas', 'con', 'coe', 'cihm'];
        $isDean = false;
        $college = null;
        
        echo "\nTesting: '" . $row['full_name'] . "'\n";
        echo "Lowercase: '$fullName'\n";
        
        if (strpos($fullName, 'dean') !== false) {
            echo "✓ Contains 'dean'\n";
            foreach ($deanColleges as $col) {
                if (strpos($fullName, $col) !== false) {
                    $isDean = true;
                    $college = strtoupper($col);
                    echo "✓ Found college: $college\n";
                    break;
                }
            }
        } else {
            echo "✗ Does NOT contain 'dean'\n";
        }
        
        if ($isDean && $college) {
            echo "Result: DEAN - College: $college\n";
        } else {
            echo "Result: ACADEMIC\n";
        }
    }
}

$conn->close();
?>
