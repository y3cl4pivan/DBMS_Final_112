<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link rel="stylesheet" href="admin_info.css">
    <title>個人資訊</title>
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
        // Display member's personal information
        $query_member = "SELECT * FROM member WHERE member_id = :member_id";
        $stmt_member = $pdo->prepare($query_member);
        $stmt_member->bindParam(':member_id', $member_id, PDO::PARAM_INT);
        $stmt_member->execute();
        $member_info = $stmt_member->fetch(PDO::FETCH_ASSOC);

        echo "<h1>個人資訊</h1>";
        echo "<p>名字： {$member_info['name']}</p>";
        echo "<p>系名: {$member_info['department']}</p>";
        echo "<p>Email: {$member_info['email']}</p>";
        echo "<p>喜好交易地點: {$member_info['preferred_transaction_place']}</p>";
        echo "<p>用戶狀態: {$member_info['status']}</p>";

        echo "<a href='self_market.php'>個人賣場</a>";//
        echo "<a href='admin_memberinfo.php'>會員編輯資訊</a>";//admin_memberinfo
        echo "<a href='admin_projectinfo.php'>商品編輯查詢</a>";//admin_projectinfo.php
        echo "<a href='admin_transactioninfo.php'>交易紀錄查詢</a>";//admin_transactioninfo
        echo "<a href='admin_reportaccountinfo.php'>帳號被檢舉清單</a>";//admin_reportaccountinfo.php
        echo "<a href='admin_reportprojectinfo.php'>商品被檢舉清單</a>";//admin_reportprojectinfo.php
        echo "<a href='admin_blocklistinfo.php'>黑名單</a>";//admin_blocklistinfo.php
        echo "<a href='admin_removedlistinfo.php'>被移除商品清單</a>";//admin_removedlistinfo.php

        ?>
        <?php
        include 'footer.php';
        ?>
    </div>

</body>

</html>