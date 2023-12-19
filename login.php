<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="login.css">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>登入介面</title>
    </head>
    <body>
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

        echo "<h1>Log In</h1>";
        echo "<h2>登入</h2>";
        echo "<form method='post' action='login.php'>";
        echo "會員ID:";
        echo "<input type='text' name='member_id'><br/><br/>";
        echo "帳號：";
        echo "<input type='text' name='account'><br/><br/>";
        echo "密碼：";
        echo "<input type='password' name='password'><br><br>";
        echo "<input type='submit' value='登入' name='submit'><br><br>";
        echo "<a href='forget_password.php'>忘記密碼？</a>";
        echo "<a href='register.php'>註冊</a>";
        echo "</form>";

        $member_id=$_POST["member_id"];
        $account=$_POST["account"];
        $password=$_POST["password"];
        $password_hash=password_hash($password,PASSWORD_DEFAULT);

        if($_SERVER["REQUEST_METHOD"] == "POST"){
            $sql = "SELECT * FROM member_account WHERE account = :account";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':account', $account, PDO::PARAM_STR);
            $stmt->execute();
            if($stmt->rowCount() > 0){
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                if($password==$result["password"]){
                    session_start();
                    $_SESSION["loggedin"] = true;
                    $_SESSION["member_id"] = $member_id;
                    $_SESSION["account"] = $result["account"];
                    echo "登入成功！";
                    //$href_mainpage = "main_page.php?member_id=".$member_id;
                    //echo "<a href=".$href_mainpage.">".$member_id."</a>";
                    Header('Location:main_page.php?member_id='.$member_id);
                }
                else{
                    echo "帳號或密碼錯誤"; 
                }
            }
        }
        ?>
    </body>
</html>

