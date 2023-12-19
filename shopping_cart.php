<!DOCTYPE html>
<html>

<head>
    <title>Shopping Cart</title>
    <link rel="stylesheet" href="shopping_cart.css">
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

        // Get member_id (replace this with your own logic, e.g., from session)
        // $member_id = "b09406069"; // Replace with your actual logic to get member_id
        session_start();
        $member_id = $_SESSION['member_id'];
        // echo "$member_id";
        // $member_id = $_GET['member_id'];
        // echo "$member_id";
        // Display member's personal information
        $query_member = "SELECT * FROM member
                         WHERE member_id = :member_id";
        $stmt_member = $pdo->prepare($query_member);
        $stmt_member->bindParam(':member_id', $member_id, PDO::PARAM_INT);
        $stmt_member->execute();
        $member_info = $stmt_member->fetch(PDO::FETCH_ASSOC);

        echo "<h1>{$member_info['name']}'s Shopping Cart!</h1>";
        echo "<p>Email: {$member_info['email']}</p>";
        echo "<p>Department: {$member_info['department']}</p>";

        // Display member's shopping cart projects
        $query_cart = "SELECT project.*, product.*, book.*, m.* 
                        FROM member_shopping_cart 
                        JOIN project ON member_shopping_cart.project_id = project.project_id
                        JOIN project_product_info ON project.project_id = project_product_info.project_id
                        JOIN product ON project_product_info.product_id = product.product_id
                        JOIN book ON product.book_id = book.book_id
                        JOIN member m On m.member_id = project.project_seller_id
                        WHERE member_shopping_cart.member_id = :member_id";
        $stmt_cart = $pdo->prepare($query_cart);
        $stmt_cart->bindParam(':member_id', $member_id, PDO::PARAM_INT);
        $stmt_cart->execute();
        $cart_items = $stmt_cart->fetchAll(PDO::FETCH_ASSOC);

        // Display projects and related products and books
        foreach ($cart_items as $item) {
            echo "<div class='project'>";
            
            $href1 = "merchandise.php?project_id=" . $item['project_id'];
            echo "<h2> Project ID : " . "<a href = " . $href1 . ">" . $item['project_id'] . "</a></h2>";
            
            // Add a remove button next to each project
            echo "<form method='post' action='remove_from_cart.php'>";
            echo "<input type='hidden' name='project_id' value='{$item['project_id']}'>";
            echo "<input type='submit' value='Remove from Cart'>";
            echo "</form>";


            echo "<p>Project Price: {$item['project_price']}</p>";
            echo "<p>Launch Date: {$item['launch_date']}</p>";
            echo "<p>Book Title: {$item['book_name']}</p>";
            echo "<p>Book Condition: {$item['book_condition']}</p>";

            $href2 = "other_market.php?member_id=" . $item['project_seller_id'];
            echo "<p>Project Seller : " . "<a href = " . $href2 . ">" . $item['name'] . "</a>" . "</p>";

            // Add more information as needed
            echo "</div>";
        }
        ?>
    </div>
    <?php
    include('footer.php');
    ?>
</body>
</body>

</html>