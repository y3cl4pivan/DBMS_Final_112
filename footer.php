<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="footer.css">
    <title>Your Page Title</title>
</head>

<body>
    <!-- Your existing footer content here -->
    <div id="footer">
        <ul>
            <!-- <a href="main_page.php?member_id=".$member_id.>
            <img src="home.png" alt="Icon 1" width = 100px>
            首頁
        </a> -->
            <?php
            echo "<a href=" . "main_page.php?member_id=" . $member_id . ">";
            ?>
            <img src="home.png" alt="Icon 1" width=100px>
            首頁
            </a>

            <?php
            echo "<a href=" . "shopping_cart.php?member_id=" . $member_id . ">";
            ?>
            <img src="shopping-cart.png" alt="Icon 2" width=100px>
            購物車
            </a>

            <?php
            echo "<a href=" . "transaction_record.php?member_id=" . $member_id . ">";
            ?>
            <img src="document.png" alt="Icon 3" width=100px>
            交易紀錄
            </a>

            <?php
            // Check if the member_id exists in the admin table
            $sqlCheckAdmin = "SELECT COUNT(*) AS admin_count FROM admin WHERE member_id = :member_id";
            $stmtCheckAdmin = $pdo->prepare($sqlCheckAdmin);
            $stmtCheckAdmin->bindParam(':member_id', $member_id, PDO::PARAM_STR);
            $stmtCheckAdmin->execute();
            $adminCount = $stmtCheckAdmin->fetchColumn();

            // Generate the appropriate link based on the result
            $infoPage = ($adminCount > 0) ? "admin_info.php" : "member_info.php";
            $linkHref = $infoPage . "?member_id=" . $member_id;

            // Output the link
            echo "<a href=" . $linkHref . ">";
            ?>
            <img src="user.png" alt="Icon 4" width=100px>
            個人資料
            </a>



        </ul>
    </div>
    <!-- ... -->
</body>

</html>