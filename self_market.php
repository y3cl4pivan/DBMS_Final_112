<!DOCTYPE html>
<html>

<head>
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
    session_start();
    $member_id = $_SESSION['member_id'];
    ?>
    <title>我的賣場</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>



    <div class="container">

        <!-- $member_id = isset($_GET["member_id"]) ? $_GET["member_id"] : "b11508064";  -->

        <h1>我的賣場</h1>


        <h2> 上架商品 </h2>
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
    <div class="container">
        <h2> 出售中 </h2>
        <?php

        $sql = "SELECT project_id as pid, project_price as ppr, launch_date as ld
				FROM Project
				WHERE project_seller_id = :selected_id AND project_sell_status = 'selling'
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
                echo "無出售中商品";
            }
        } catch (PDOException $e) {
            echo "Error executing query: " . $e->getMessage();
        }
        ?>
    </div>
    <div class="container">
        <h2> 已售出 </h2>
        <?php

        $sql = "SELECT project_id as pid, project_price as ppr, launch_date as ld
				FROM Project
				WHERE project_seller_id = :selected_id AND project_sell_status = 'sold'
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
                echo "無售出商品";
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