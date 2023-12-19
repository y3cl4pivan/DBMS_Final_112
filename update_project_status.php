<?php
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
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $project_id = $_POST['project_id'];
    $new_project_sell_status = $_POST['new_status'];

    // Validate and sanitize user input
    $validStatuses = ['available', 'selling', 'removed', 'unavailable'];
    if (!in_array($new_project_sell_status, $validStatuses)) {
        die("Invalid project sell status selected.");
    }

    // Begin a transaction
    $pdo->beginTransaction();

    try {
        // Update project_sell_status in the PROJECT table
        $updateProjectSql = "UPDATE public.PROJECT SET project_sell_status = :new_project_sell_status WHERE project_id = :project_id";
        $updateProjectStatement = $pdo->prepare($updateProjectSql);
        $updateProjectStatement->bindParam(':new_project_sell_status', $new_project_sell_status, PDO::PARAM_STR);
        $updateProjectStatement->bindParam(':project_id', $project_id, PDO::PARAM_INT);
        $updateProjectStatement->execute();

        // Retrieve product_id from project_product_info based on project_id
        $productInfoSql = "SELECT product_id FROM public.project_product_info WHERE project_id = :project_id";
        $productInfoStatement = $pdo->prepare($productInfoSql);
        $productInfoStatement->bindParam(':project_id', $project_id, PDO::PARAM_INT);
        $productInfoStatement->execute();
        $productInfo = $productInfoStatement->fetch(PDO::FETCH_ASSOC);

        if ($productInfo) {
            $product_id = $productInfo['product_id'];

            // Update product_sell_status in the PRODUCT table
            $updateProductSql = "UPDATE public.PRODUCT SET product_sell_status = :new_product_sell_status WHERE product_id = :product_id";
            $new_product_sell_status = mapProjectStatusToProductStatus($new_project_sell_status);
            $updateProductStatement = $pdo->prepare($updateProductSql);
            $updateProductStatement->bindParam(':new_product_sell_status', $new_product_sell_status, PDO::PARAM_STR);
            $updateProductStatement->bindParam(':product_id', $product_id, PDO::PARAM_INT);
            $updateProductStatement->execute();
        }

        // If project_sell_status is 'removed', insert data into REMOVED_LIST
        if ($new_project_sell_status == 'removed') {
            $insertRemovedListSql = "INSERT INTO public.REMOVED_LIST (be_reported_project_id, report_time) VALUES (:be_reported_project_id, NOW())";
            $insertRemovedListStatement = $pdo->prepare($insertRemovedListSql);
            $insertRemovedListStatement->bindParam(':be_reported_project_id', $project_id, PDO::PARAM_INT);
            $insertRemovedListStatement->execute();
        }

        // Check if there is an active transaction before committing
        if ($pdo->inTransaction()) {
            $pdo->commit();
        }

        // Redirect back to the previous page after the update
        header("Location: {$_SERVER['HTTP_REFERER']}");
        exit();
    } catch (PDOException $e) {
        // Rollback the transaction on error
        $pdo->rollBack();
        echo "Error updating project and product sell status: " . $e->getMessage();
    }
} else {
    // Redirect to the home page if accessed directly without a POST request
    header("Location: admin_projectinfo.php");
    exit();
}

// Map project status to product status (adjust this function based on your logic)
function mapProjectStatusToProductStatus($projectStatus)
{
    // Add your logic to map project status to corresponding product status
    // For example, you might have a direct mapping or a more complex logic
    // Return the mapped product status
    return $projectStatus;
}
?>
