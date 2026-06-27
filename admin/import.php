<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include 'dbinit.php'; // Database connection

if (isset($_FILES["csv_file"])) {
    $file = $_FILES["csv_file"]["tmp_name"];

    if (($handle = fopen($file, "r")) !== FALSE) {

        $header = fgetcsv($handle); // Skip header row

        while (($data = fgetcsv($handle)) !== FALSE) {
            $student_id   = $conn->real_escape_string($data[0]);
            $student_name = $conn->real_escape_string($data[1]);
            $program      = $conn->real_escape_string($data[2]);
            $section      = $conn->real_escape_string($data[3]);
            $email        = $conn->real_escape_string($data[4]);
            $password     = $conn->real_escape_string($data[5]);

            $sql = "INSERT INTO students (student_id, student_name, program, section, email, password)
                    VALUES ('$student_id', '$student_name', '$program', '$section', '$email', '$password')";
            $conn->query($sql);
        }

        fclose($handle);
        echo "CSV Imported Successfully!";
    } else {
        echo "Could not open the CSV file.";
    }
} else {
    echo "No file uploaded.";
}
$conn->close();
?>
