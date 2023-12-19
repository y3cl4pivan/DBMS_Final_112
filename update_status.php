<?php
// update_status.php

$host = 'localhost';
$port = 2926;
$dbname = 'NTUsed_1218';
$user = 'postgres';
$password = trim(file_get_contents('db_password.txt'));
$pdo = null;

try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Database connection failed: " . $e->getMessage();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $member_id = $_POST['member_id'];
    $new_status = $_POST['new_status'];

    // Update the status in the database
    $updateStatusQuery = "UPDATE public.MEMBER SET status = :new_status WHERE member_id = :member_id";

    try {
        $updateStatusStatement = $pdo->prepare($updateStatusQuery);
        $updateStatusStatement->bindParam(':new_status', $new_status, PDO::PARAM_STR);
        $updateStatusStatement->bindParam(':member_id', $member_id, PDO::PARAM_INT);
        $updateStatusStatement->execute();

        // Update project_sell_status based on member's status
        if ($new_status == 'suspended') {
            // Update project_sell_status to 'unavailable' for projects belonging to the member
            $updateProjectStatusQuery = "UPDATE public.PROJECT
                                         SET project_sell_status = 'unavailable'
                                         WHERE project_seller_id = :member_id
                                           AND project_sell_status NOT IN ('selling', 'sold', 'removed')";

            $updateProjectStatusStatement = $pdo->prepare($updateProjectStatusQuery);
            $updateProjectStatusStatement->bindParam(':member_id', $member_id, PDO::PARAM_INT);
            $updateProjectStatusStatement->execute();
        } elseif ($new_status == 'active') {
            // Update project_sell_status to 'available' for projects belonging to the member
            $updateProjectStatusQuery = "UPDATE public.PROJECT
                                         SET project_sell_status = 'available'
                                         WHERE project_seller_id = :member_id
                                           AND project_sell_status NOT IN ('selling', 'sold', 'removed')";

            $updateProjectStatusStatement = $pdo->prepare($updateProjectStatusQuery);
            $updateProjectStatusStatement->bindParam(':member_id', $member_id, PDO::PARAM_INT);
            $updateProjectStatusStatement->execute();
        }

        // Redirect back to the page after updating
        header("Location: admin_memberinfo.php");
        exit();
    } catch (PDOException $e) {
        echo "Error updating status: " . $e->getMessage();
    }
}
?>
