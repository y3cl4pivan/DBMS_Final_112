<?php
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Get the project_id from the form submission
    $project_id = $_POST['project_id'];
    
    // Perform your database operations to remove the project from the shopping cart
    $host = 'localhost';
    $port = 2926; // replace with your own connection port
    $dbname = 'NTUsed_1218'; // replace with your own database name 
    $user = 'postgres'; // replace with your own username 
    $password = trim(file_get_contents('db_password.txt')); // replace with your own password 

    try {
        $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        echo "Database connection failed: " . $e->getMessage();
        die();
    }

    session_start();
    $member_id = $_SESSION['member_id'];

    // Remove the project from the member's shopping cart
    $query_remove = "DELETE FROM member_shopping_cart WHERE member_id = :member_id AND project_id = :project_id";
    $stmt_remove = $pdo->prepare($query_remove);
    $stmt_remove->bindParam(':member_id', $member_id, PDO::PARAM_INT);
    $stmt_remove->bindParam(':project_id', $project_id, PDO::PARAM_INT);
    
    try {
        $stmt_remove->execute();
        echo "Project removed from the shopping cart successfully.";
    } catch (PDOException $e) {
        echo "Error removing project from the shopping cart: " . $e->getMessage();
    }

    // Close the database connection
    $pdo = null;
    header("Location: shopping_cart.php");
    exit();
} else {
    // If the form is not submitted, redirect to the shopping cart page
    header("Location: shopping_cart.php");
    exit();
}
?>
