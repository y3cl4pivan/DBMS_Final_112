<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="register.css">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>會員註冊</title>
        <script>    
            function validateForm(){
                var x = document.forms["registerForm"]["password"].value;
                var y = document.forms["registerForm"]["password_check"].value;
                if(x.length < 4){
                    alert("密碼長度不足");
                    return false;
                }
                if (x != y){
                    alert("請確認密碼是否輸入正確");
                    return false;
                }
            }
        </script>
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

            echo "<h1>註冊頁面</h1>";
            echo "<form name='registerForm' method='post' action='register.php' onsubmit='return validateForm()'>";
            echo "會員ID:";
            echo "<input type='text' name='member_id'><br/><br/>";
            echo "帳  號：";
            echo "<input type='text' name='account'><br/><br/>";
            echo "密  碼：";
            echo "<input type='password' name='password'><br/><br/>";
            echo "確認密碼：";
            echo "<input type='password' name='password_check'><br/><br/>";
            echo "姓名：";
            echo "<input type='text' name='name'><br/><br/>";
            echo "系別：";
            echo "<input type='text' name='department'><br/><br/>";
            echo "Email：";
            echo "<input type='text' name='email'><br/><br/>";
            echo "喜好交易地點：";
            echo "<input type='text' name='preferred_transaction_place'><br/><br/>";

            echo "<input type='submit' value='註冊' name='submit'>";
            echo "<input type='reset' value='重設' name='submit'>";

            if ($_SERVER["REQUEST_METHOD"] == "POST"){
                $member_id=$_POST["member_id"];
                $account=$_POST["account"];
                $password=$_POST["password"];
                $name=$_POST["name"];
                $department=$_POST["department"];
                $email=$_POST["email"];
                $preferred_transaction_place=$_POST["preferred_transaction_place"];

                // Check if the account is already registered
                $checkAccountQuery = "SELECT COUNT(*) FROM MEMBER_ACCOUNT WHERE account = :account";
                $checkAccountStatement = $pdo->prepare($checkAccountQuery);
                $checkAccountStatement->bindParam(':account', $account, PDO::PARAM_STR);
                $checkAccountStatement->execute();

                if($checkAccountStatement->fetchColumn() > 0){ 
                    echo "該帳號已有人使用!";
                } else {
                    // Insert new member_account information into the database
                    $insertAccountQuery = "INSERT INTO MEMBER_ACCOUNT(member_id, account, password)
                        VALUES(:member_id, :account, :password)";
                    $insertAccountStatement = $pdo->prepare($insertAccountQuery);
                    $insertAccountStatement->bindParam(':member_id', $member_id, PDO::PARAM_STR);
                    $insertAccountStatement->bindParam(':account', $account, PDO::PARAM_STR);
                    $insertAccountStatement->bindParam(':password', $password, PDO::PARAM_STR);

                    // Insert new member information into the database
                    $insertMemberQuery = "INSERT INTO MEMBER(member_id, name, department, email, preferred_transaction_place, status)
                        VALUES(:member_id, :name, :department, :email, :preferred_transaction_place, 'active')";
                    $insertMemberStatement = $pdo->prepare($insertMemberQuery);
                    $insertMemberStatement->bindParam(':member_id', $member_id, PDO::PARAM_STR);
                    $insertMemberStatement->bindParam(':name', $name, PDO::PARAM_STR);
                    $insertMemberStatement->bindParam(':department', $department, PDO::PARAM_STR);
                    $insertMemberStatement->bindParam(':email', $email, PDO::PARAM_STR);
                    $insertMemberStatement->bindParam(':preferred_transaction_place', $preferred_transaction_place, PDO::PARAM_STR);

                    try {
                        // Begin the transaction
                        $pdo->beginTransaction();

                        // Execute both statements
                        $insertAccountStatement->execute();
                        $insertMemberStatement->execute();

                        // Commit the transaction
                        $pdo->commit();

                        echo "註冊成功!";
                        header('Location: login.php');
                    } catch (Exception $e) {
                        // An error occurred, rollback the transaction
                        $pdo->rollBack();
                        echo "註冊失敗：" . $e->getMessage();
                    }
                }
            }

            echo "</form>";
            ?>
        </div>
    </body>
</html>
