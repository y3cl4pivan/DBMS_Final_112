<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>忘記密碼</title>
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

        echo "<h1>忘記密碼</h1>";
        echo "<form method='post' action='forget_password.php'>";
        echo "請輸入信箱：";
        echo "<input type='text' name='email'><br/><br/>";
        echo "<input type='submit' value='送出' name='submit'><br><br>";
        echo "</form>";
    
        if($_SERVER["REQUEST_METHOD"] == "POST"){
            $email=$_POST["email"];
            $sql = "SELECT * FROM member_account AS ma JOIN member AS m ON m.member_id = ma.member_id WHERE m.email = :email";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            if($stmt->rowCount() > 0){
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                session_start();
                $_SESSION["password"]=$result["password"];
                header("location: send_success.php");
            }else{
                echo "信箱錯誤或不存在"; 
            }
        }
        ?>
    </body>
</html>

