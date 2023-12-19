<!DOCTYPE html>
<html>

<head>
    <title>Transaction Record</title>
    <link rel="stylesheet" href="transaction_record.css">
</head>

<body>
    <div class="container">
        <?php
        $host = 'localhost';
        $port = 2926; // remember to replace your own connection port
        $dbname = 'NTUsed_1218'; // remember to replace your own database name 
        $user = 'postgres'; // remember to replace your own username 
        $password = trim(file_get_contents('db_password.txt')); // remember to replace your own password 

        try {
            $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Database connection failed: " . $e->getMessage();
            die();
        }
        session_start();
        $member_id = $_SESSION['member_id'];

        // Get member_id (replace this with your own logic, e.g., from session)
        // $member_id = "b10609099"; // Replace with your actual logic to get member_id
        // $member_id = "b09101025";
        // $member_id = "b09101025";
        // $member_id = $_GET['member_idx'];
        // Display member's personal information
        $query_member = "SELECT * FROM member WHERE member_id = :member_id";
        $stmt_member = $pdo->prepare($query_member);
        $stmt_member->bindParam(':member_id', $member_id, PDO::PARAM_INT);
        $stmt_member->execute();
        $member_info = $stmt_member->fetch(PDO::FETCH_ASSOC);

        echo "<h1>Transaction Record for {$member_info['name']}</h1>";
        echo "<p>Email: {$member_info['email']}</p>";
        echo "<p>Department: {$member_info['department']}</p>";

        // Filter Form
        echo "<form method='post'>";
        echo "<label for='sorting'>Sort by:</label>";
        echo "<select name='sorting' id='sorting'>";
        echo "<option value='transaction_time'>Transaction Time</option>";
        echo "<option value='transaction_status'>Transaction Status</option>";
        echo "</select>";

        echo "<label for='role'>Show transactions as:</label>";
        echo "<select name='role' id='role'>";
        echo "<option value='both'>Both Buyer and Seller</option>";
        echo "<option value='buyer'>Buyer</option>";
        echo "<option value='seller'>Seller</option>";
        echo "</select>";

        echo "<input type='submit' value='Apply Filter'>";
        echo "</form>";

        // Process Form Submission
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $selectedSorting = $_POST['sorting'];
            $selectedRole = $_POST['role'];

            // Modify your query here based on the selected sorting and role criteria
            $query_transactions = "SELECT 
                                        MAX(transaction.transaction_id) AS transaction_id,
                                        MAX(transaction.seller_id) AS seller_id,
                                        MAX(transaction.buyer_id) AS buyer_id,
                                        MAX(transaction.transaction_time) AS transaction_time,
                                        MAX(transaction.transaction_status) AS transaction_status,
                                        MAX(transaction.project_id) AS project_id,
                                        MAX(seller.name) AS seller_name,
                                        MAX(buyer.name) AS buyer_name,
                                        MAX(COALESCE(b.book_name, '')) AS book_name
                                    FROM transaction
                                    LEFT JOIN member AS seller ON transaction.seller_id = seller.member_id
                                    LEFT JOIN member AS buyer ON transaction.buyer_id = buyer.member_id
                                    LEFT JOIN project ON transaction.project_id = project.project_id
                                    LEFT JOIN project_product_info AS p_info ON p_info.project_id = project.project_id
                                    LEFT JOIN product AS pd ON pd.product_id = p_info.product_id
                                    LEFT JOIN book AS b ON b.book_id = pd.book_id
                                    LEFT JOIN book_authors AS ba ON ba.book_id = b.book_id
                                    LEFT JOIN book_classification AS bc ON bc.book_id = b.book_id
                                    WHERE (transaction.seller_id = :member_id AND :selectedRole IN ('both', 'seller')) 
                                        OR (transaction.buyer_id = :member_id AND :selectedRole IN ('both', 'buyer'))
                                    GROUP BY transaction.project_id, transaction.transaction_id
                                    ORDER BY {$selectedSorting} DESC";
            $stmt_transactions = $pdo->prepare($query_transactions);
            $stmt_transactions->bindParam(':member_id', $member_id, PDO::PARAM_INT);
            $stmt_transactions->bindParam(':selectedRole', $selectedRole, PDO::PARAM_STR);
            $stmt_transactions->execute();
            $transactions = $stmt_transactions->fetchAll(PDO::FETCH_ASSOC);

        } else {
            // Default query without sorting and role filters
            $query_transactions = "SELECT 
                                        MAX(transaction.transaction_id) AS transaction_id,
                                        MAX(transaction.seller_id) AS seller_id,
                                        MAX(transaction.buyer_id) AS buyer_id,
                                        MAX(transaction.transaction_time) AS transaction_time,
                                        MAX(transaction.transaction_status) AS transaction_status,
                                        MAX(transaction.project_id) AS project_id,
                                        MAX(seller.name) AS seller_name,
                                        MAX(buyer.name) AS buyer_name,
                                        MAX(COALESCE(b.book_name, '')) AS book_name
                                    FROM transaction
                                    LEFT JOIN member AS seller ON transaction.seller_id = seller.member_id
                                    LEFT JOIN member AS buyer ON transaction.buyer_id = buyer.member_id
                                    LEFT JOIN project ON transaction.project_id = project.project_id
                                    LEFT JOIN project_product_info AS p_info ON p_info.project_id = project.project_id
                                    LEFT JOIN product AS pd ON pd.product_id = p_info.product_id
                                    LEFT JOIN book AS b ON b.book_id = pd.book_id
                                    LEFT JOIN book_authors AS ba ON ba.book_id = b.book_id
                                    LEFT JOIN book_classification AS bc ON bc.book_id = b.book_id
                                    WHERE transaction.seller_id = :member_id OR transaction.buyer_id = :member_id
                                    GROUP BY transaction.project_id, transaction.transaction_id
                                    ORDER BY transaction.transaction_time"; // Default sorting by transaction_time

            $stmt_transactions = $pdo->prepare($query_transactions);
            $stmt_transactions->bindParam(':member_id', $member_id, PDO::PARAM_INT);
            $stmt_transactions->execute();
            $transactions = $stmt_transactions->fetchAll(PDO::FETCH_ASSOC);
        }
        
        // if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        //     // 其他代碼
        
        //     // 檢查是否有 complete_transaction 的 POST 參數
        //     if (isset($_POST['complete_transaction'])) {
        //         $completedTransactionId = $_POST['transaction_id'];
        //         // 執行更新交易狀態為 'finished' 的邏輯
        //         // 這裡是一個示例，實際情況可能需要根據你的數據庫結構進行調整
        //         $updateQuery = "UPDATE transaction SET transaction_status = 'finished' WHERE transaction_id = :transaction_id";
        //         $updateStmt = $pdo->prepare($updateQuery);
        //         $updateStmt->bindParam(':transaction_id', $completedTransactionId, PDO::PARAM_INT);
        //         $updateStmt->execute();
        //     }
        // }

        // Display transaction information
        echo "<table border='1'>";
        echo "<tr><th>Transaction ID</th><th>Seller</th><th>Buyer</th><th>Book</th><th>Transaction Time</th><th>Transaction Status</th></tr>";
        foreach ($transactions as $transaction) {
            echo "<tr>";
            echo "<td>{$transaction['transaction_id']}</td>";
            // echo "<td>{$transaction['seller_name']}</td>";
            // echo "<td>{$transaction['buyer_name']}</td>";
            // Link to seller's market
            if ($transaction['seller_id'] != $member_id) {
                echo "<td><a href='other_market.php?member_id={$transaction['seller_id']}'>{$transaction['seller_name']}</a></td>";
            } else {
                echo "<td>{$transaction['seller_name']}</td>";
            }

            // Link to buyer's market
            if ($transaction['buyer_id'] != $member_id) {
                echo "<td><a href='other_market.php?member_id={$transaction['buyer_id']}'>{$transaction['buyer_name']}</a></td>";
            } else {
                echo "<td>{$transaction['buyer_name']}</td>";
            }
            echo "<td>{$transaction['book_name']}</td>";
            echo "<td>{$transaction['transaction_time']}</td>";
            echo "<td>{$transaction['transaction_status']}</td>";
            // if ($transaction['transaction_status'] == 'in progress') {
            //     echo "<td><form method='post'>";
            //     echo "<input type='hidden' name='transaction_id' value='{$transaction['transaction_id']}'>";
            //     echo "<input type='submit' name='complete_transaction' value='Complete'>";
            //     echo "</form></td>";
            // } else {
            //     echo "<td></td>"; // Empty column if the transaction is not in progress
            // }
        
            echo "</tr>";
        }
        echo "</table>";
        ?>



    </div>
    <?php
    include('footer.php');
    ?>
</body>

</html>