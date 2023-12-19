<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link rel="stylesheet" href="style.css">
    <?php
    $host = 'localhost';
    $port = 2926; // remember to replace your own connection port
    $dbname = 'NTUsed_1218'; // remember to replace your own database name 
    $user = 'postgres'; // remember to replace your own username 
    $password = trim(file_get_contents('db_password.txt')); // remember to replace your own password 

    $pdo = null;
    try {
        $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        echo "Database connection failed: " . $e->getMessage();
    }
    ?>

    <?php
    $member_id = $_GET['member_id'];
    $sql1 = "SELECT * FROM member WHERE member_id = :selected_id";
    $statement1 = $pdo->prepare($sql1);
    $statement1->bindParam(':selected_id', $member_id);
    $statement1->execute();
    $result1 = $statement1->fetch(PDO::FETCH_ASSOC);
    if ($result1) {
        $dynamicTitle = $result1['name'] . "的賣場";
    } else {
        echo "Query failed.";
    }
    ?>
    <title><?php echo $dynamicTitle; ?></title>
</head>

<body>

    <div class="container">
        <h1> <?php echo $dynamicTitle; ?> </h1>

        <!-- Display member information above the "檢舉用戶" button -->
        <p>系名: <?php echo $result1['department']; ?></p>
        <p>Email: <?php echo $result1['email']; ?></p>
        <p>喜好交易地點: <?php echo $result1['preferred_transaction_place']; ?></p>
        <p>用戶狀態: <?php echo $result1['status']; ?></p>

        <a href=<?php echo "reporting_account.php?member_id=" . $member_id ?>>
            <button>檢舉用戶</button>
        </a>

        <h2> 商品列表 </h2>

        <?php


        $sql = "SELECT project_id as pid, project_price as ppr, launch_date as ld
        FROM Project
        WHERE project_seller_id = :selected_id AND project_sell_status = 'available'
        ORDER BY launch_date DESC
        ";

        try {
            $statement = $pdo->prepare($sql);
            $statement->bindParam(':selected_id', $member_id);
            $statement->execute();
            $result = $statement->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                echo "<table>";
                echo "<tr><th>編號</th><th>價錢</th><th>上架日期</th></tr>";
                $href1 = "merchandise.php?project_id=" . $result['pid'];
                echo "<tr><td>" . "<a href = " . $href1 . ">" . $result['pid'] . "</a>" . "</td><td>" . $result['ppr'] . "</td><td>" . $result['ld'] . "</td></tr>";
                $results = $statement->fetchAll(PDO::FETCH_ASSOC);
                if ($results) {
                    foreach ($results as $row) {
                        $href2 = "merchandise.php?project_id=" . $row['pid'];
                        echo "<tr><td>" . "<a href = " . $href2 . ">" . $row['pid'] . "</a>" . "</td><td>" . $row['ppr'] . "</td><td>" . $row['ld'] . "</td></tr>";
                    }
                }
                echo "</table>";
            } else {
                echo "無上架商品";
            }
        } catch (PDOException $e) {
            echo "Error executing query: " . $e->getMessage();
        }
        ?>
    </div>
    <?php
    include('footer.php');
    ?>
</body>

</html>