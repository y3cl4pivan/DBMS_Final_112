<!DOCTYPE html>
<html>

<head>
    <title>Merchandise Page</title>
    <link rel="stylesheet" href="merchandise.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script>
        function addToCart(projectId) {
            if (confirm("確定要將此項目加入購物車嗎？")) {
                $.ajax({
                    type: "POST",
                    url: "add_to_cart.php",
                    data: {
                        project_id: projectId
                    },
                    success: function(response) {
                        if (response === "success") {
                            alert("已成功加入購物車！");
                        } else {
                            alert("加入購物車失敗。請稍後再試。");
                        }
                    },
                    error: function(error) {
                        console.error("加入購物車時發生錯誤:", error);
                        alert("加入購物車失敗。請稍後再試。");
                    }
                });
            }
        }

        function purchaseProject(projectId) {
            if (confirm("確定要購買此項目嗎？")) {
                $.ajax({
                    type: "POST",
                    url: "purchase_project.php",
                    data: {
                        project_id: projectId
                    },
                    success: function(response) {
                        // if (response === "success") {
                        //     alert("已成功購買項目！");
                        // } else {
                        //     alert("購買項目失敗。請稍後再試_1。");
                        // }
                        if (response.startsWith("success")) {
                            alert("已成功購買項目！");
                        } else {
                            alert("購買項目失敗。請稍後再試。\n錯誤訊息：" + response.substring(6));
                        }

                    },
                    error: function(error) {
                        console.error("購買項目時發生錯誤:", error);
                        alert("購買項目失敗。請稍後再試_2。");
                    }
                });
            }
        }
    </script>
</head>

<body>
    <div class="container">
        <?php
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
        ?>



        <?php
        session_start();
        $currentMemberId = $_SESSION['member_id'];
        // 假設 $selectedProjectId 是你想要顯示的具體項目
        // $selectedProjectId = "10"; // 你可能需要安全地處理這個值
        $selectedProjectId = $_GET['project_id'];
        // 在 JavaScript 中使用 member_id
        echo "<script>var currentMemberId = $currentMemberId;</script>";


        $query = "SELECT b.book_name, ba.author_name, b.publisher, pd.product_sell_status, proj.project_price,
                    m.name -- 添加這一行以獲取賣家的信息
                    FROM product pd
                    JOIN project_product_info p_info ON pd.product_id = p_info.product_id
                    JOIN book b ON pd.book_id = b.book_id
                    JOIN book_authors ba ON ba.book_id = b.book_id
                    JOIN project proj ON p_info.project_id = proj.project_id
                    JOIN member m On proj.project_seller_id = m.member_id
                    WHERE proj.project_id = :project_id
                    LIMIT 1";

        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':project_id', $selectedProjectId, PDO::PARAM_INT);
        $stmt->execute();

        // Display information for the selected product
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<h2>Merchandise Information</h2>";
            echo "<p><strong>Book Name:</strong> " . $row['book_name'] . "</p>";
            echo "<p><strong>Author(s):</strong> " . $row['author_name'] . "</p>";
            echo "<p><strong>Publisher:</strong> " . $row['publisher'] . "</p>";
            echo "<p><strong>Product Status:</strong> " . $row['product_sell_status'] . "</p>";
            echo "<p><strong>Project Price:</strong> $" . $row['project_price'] . "</p>";
            echo "<p><strong>Product Seller:</strong> " . $row['name'] . "</p>";
        }

        // Query to retrieve information for remaining products in the project
        $remainingProductsQuery = "SELECT pd.product_id, b.book_name
                                   FROM product pd
                                   JOIN project_product_info p_info ON pd.product_id = p_info.product_id
                                   JOIN book b ON pd.book_id = b.book_id
                                   WHERE p_info.project_id = :project_id
                                   AND pd.product_id <> :selected_product_id
                                   LIMIT 10"; // Adjust the limit as needed

        $stmtRemaining = $pdo->prepare($remainingProductsQuery);
        $stmtRemaining->bindParam(':project_id', $selectedProjectId, PDO::PARAM_INT);
        $stmtRemaining->bindParam(':selected_product_id', $_GET['product_id'], PDO::PARAM_INT);
        $stmtRemaining->execute();

        // Display a list of remaining products
        if ($remainingProducts = $stmtRemaining->fetchAll(PDO::FETCH_ASSOC)) {
            echo "<h2>Remaining Products</h2>";
            echo "<ul>";
            foreach ($remainingProducts as $product) {
                echo "<li>
                <a href='product_details.php?product_id=" . $product['product_id'] . "'>" . $product['book_name'] . "</a>
                </li>";
            }
            echo "</ul>";
        }
        ?>

        <button onclick="addToCart(<?php echo $selectedProjectId; ?>)">加入購物車</button>
        <button onclick="purchaseProject(<?php echo $selectedProjectId; ?>)">購買</button>
    </div>
    <?php
    include('footer.php');
    ?>
</body>

</html>