<?php
/***********************************************
 * Attendance CSV Importer (UPSERT Version)
 * Workflow:
 * 1. Check if CSV file exists — die if not.
 * 2. Hardcode username.
 * 3. Get user_id from database — die if not found.
 * 4. Read CSV + upsert attendance in one query.
 ***********************************************/

// -------- SETTINGS --------

// Hardcoded username
$hardcoded_username = "vivek";  // <-- CHANGE THIS

// CSV uploaded via FTP
$csv_file_path = __DIR__ . "/attendance.csv";

// DB connection
require_once "../../db.php";    // ← as you requested


// -------- STEP 1: Check CSV exists --------

if (!file_exists($csv_file_path)) {
    die("ERROR: CSV file not found at: " . $csv_file_path);
}

echo "<h3>CSV file found. Starting import…</h3>";


// -------- STEP 2 & 3: Get user_id from username --------

$stmt = $conn->prepare("SELECT id FROM users WHERE username=? LIMIT 1");
$stmt->bind_param("s", $hardcoded_username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("ERROR: Username '$hardcoded_username' does NOT exist.");
}

$user_id = $result->fetch_assoc()['id'];

echo "User '$hardcoded_username' found. user_id = $user_id<br><br>";


// -------- Prepare UPSERT statement --------

// Uses UNIQUE(user_id, date) to update instead of insert
$upsert = $conn->prepare("
    INSERT INTO attendance (user_id, date, code)
    VALUES (?, ?, ?)
    ON DUPLICATE KEY UPDATE code = VALUES(code)
");

$allowedCodes = ['A','B','C','G','O','L','TR','HL'];


// -------- STEP 4: Process CSV --------

$handle = fopen($csv_file_path, "r");
$header = fgetcsv($handle); // skip header

$count = 0;

while (($row = fgetcsv($handle)) !== false) {

    if (count($row) < 2) continue;

    $date = trim($row[0]);
    $code = trim($row[1]);

    // Validate date format
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
        echo "Skipping invalid date: $date<br>";
        continue;
    }

    // Validate code
    if (!in_array($code, $allowedCodes)) {
        echo "Skipping invalid code '$code' for date $date<br>";
        continue;
    }

    // Perform UPSERT
    $upsert->bind_param("iss", $user_id, $date, $code);
    $upsert->execute();
    $count++;
}

fclose($handle);


// -------- OUTPUT --------

echo "<hr>";
echo "<b>CSV Import Completed Successfully.</b><br>";
echo "Rows inserted/updated: <b>$count</b><br>";
echo "<hr>";
echo "Done.";
?>
