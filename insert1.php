<?php
session_start();
date_default_timezone_set('Asia/Manila'); // Ensure PHP uses Philippine time

$server = "localhost";
$username = "root";
$password = "";
$dbname = "qrcodedb";

$conn = new mysqli($server, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['text'])) {
    $studentID = $_POST['text'];
    $dateToday = date('Y-m-d'); // Get today's date
    $timeNow = date('Y-m-d H:i:s'); // Get real-time PHT timestamp

    // Check if the student has already timed in today
    $sql = "SELECT * FROM table_attendance WHERE STUDENTID = '$studentID' AND LOGDATE = '$dateToday'";
    $result = $conn->query($sql);

    if ($result->num_rows == 0) {
        // First scan: Time-in
        $insertSQL = "INSERT INTO table_attendance (STUDENTID, TIMEIN, LOGDATE, STATUS) 
                      VALUES ('$studentID', '$timeNow', '$dateToday', 0)";
        if ($conn->query($insertSQL) === TRUE) {
            $_SESSION['success'] = "Successfully timed in at " . date('h:i:s A', strtotime($timeNow)) . "!";
        } else {
            $_SESSION['error'] = "Error in timing in.";
        }
    } else {
        // Second scan: Time-out
        $updateSQL = "UPDATE table_attendance SET TIMEOUT = '$timeNow', STATUS = 1 
                      WHERE STUDENTID = '$studentID' AND LOGDATE = '$dateToday'";
        if ($conn->query($updateSQL) === TRUE) {
            $_SESSION['success'] = "Successfully timed out at " . date('h:i:s A', strtotime($timeNow)) . "!";
        } else {
            $_SESSION['error'] = "Error in timing out.";
        }
    }
}

$conn->close();
header("Location: index.php");
exit();
?>
