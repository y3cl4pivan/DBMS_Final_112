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
    $buyerId = $_SESSION['member_id'];

    // Retrieve project_seller_id based on $projectId
    $sellerQuery = "SELECT project_seller_id FROM project WHERE project_id = :project_id";
    $stmtSeller = $pdo->prepare($sellerQuery);
    $stmtSeller->bindParam(':project_id', $projectId, PDO::PARAM_INT);
    $stmtSeller->execute();
    $sellerId = $stmtSeller->fetchColumn();

    // Insert a record into the transaction table
    $insertTransactionQuery = "INSERT INTO transaction (transaction_id, seller_id, buyer_id, transaction_time, transaction_status, project_id) 
                              VALUES (nextval('transaction_id_seq'), :seller_id, :buyer_id, NOW(), 'in progress', :project_id)";
    $stmtTransaction = $pdo->prepare($insertTransactionQuery);
    $stmtTransaction->bindParam(':seller_id', $sellerId, PDO::PARAM_INT);
    $stmtTransaction->bindParam(':buyer_id', $buyerId, PDO::PARAM_INT);
    $stmtTransaction->bindParam(':project_id', $projectId, PDO::PARAM_INT);
    
    try {
        $pdo->beginTransaction(); // Start a transaction
        $stmtTransaction->execute();
        
        // Update project_sell_status to 'sold' (assuming this is the correct column)
        $updateProjectStatusQuery = "UPDATE project SET project_sell_status = 'sold' WHERE project_id = :project_id";
        $stmtUpdateStatus = $pdo->prepare($updateProjectStatusQuery);
        $stmtUpdateStatus->bindParam(':project_id', $projectId, PDO::PARAM_INT);
        $stmtUpdateStatus->execute();

        $pdo->commit(); // Commit the transaction
        echo "success"; // Success
    } catch (PDOException $ex) {
        $pdo->rollBack(); // Roll back the transaction in case of an error
        echo "error: " . $ex->getMessage(); // Failure with error message
    }
} else {
    echo "error"; // Parameters missing
}
