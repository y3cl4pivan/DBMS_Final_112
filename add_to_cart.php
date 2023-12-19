<?php
session_start();

$host = 'localhost';
$port = 2926; // replace with your connection port
$dbname = 'NTUsed_1218'; // replace with your database name 
$user = 'postgres'; // replace with your username 
$password = trim(file_get_contents('db_password.txt')); // replace with your password

$pdo = null;
try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Database connection failed: " . $e->getMessage();
    exit();
}

if (isset($_POST['project_id']) && isset($_SESSION['member_id'])) {
    $projectId = $_POST['project_id'];
    $memberId = $_SESSION['member_id'];

    // Insert a record into the member_shopping_cart table
    $insertQuery = "INSERT INTO member_shopping_cart (member_id, project_id) VALUES (:member_id, :project_id)";
    $stmt = $pdo->prepare($insertQuery);
    $stmt->bindParam(':member_id', $memberId, PDO::PARAM_INT);
    $stmt->bindParam(':project_id', $projectId, PDO::PARAM_INT);

    try {
        $stmt->execute();
        echo "success"; // Success
    } catch (PDOException $ex) {
        echo "error"; // Failure
    }
} else {
    echo "error"; // Parameters missing
}
?>
