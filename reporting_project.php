<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="style.css">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>檢舉</title>
    </head>
    <body>
        <?php
        $host = 'localhost';
        $port = 2926; // remember to replace your own connection port
        $dbname = 'NTUsed'; // remember to replace your own database name 
        $user = 'postgres'; // remember to replace your own username 
        $password = trim(file_get_contents('db_password.txt')); // remember to replace your own password 

        try {
            $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Database connection failed: " . $e->getMessage();
            die();
        }

        echo "<h1>檢舉</h1>";
        echo "<form method='post' action='reporting.php'>";
        echo "欲檢舉帳號id：";
        echo "<input type='text' name='be_reported_project_id'><br/><br/>";
        echo "檢舉理由：";
        echo "<input type='text' name='reason'><br><br>";
        echo "<input type='submit' value='送出' name='submit'><br><br>";
        echo "</form>";

        $reporter_id = $_GET["member_id"];
        if($_SERVER["REQUEST_METHOD"]=="POST"){
            $be_reported_project_id=$_POST["be_reported_project_id"];
            $reason=$_POST["reason"];
            $sql="SELECT NOW()::TIMESTAMP";
            $stmt = $pdo->prepare($sql);
            if($stmt->execute()){
                $report_time=$stmt->fetch(PDO::FETCH_ASSOC);
                $sql="Insert Into REPORT_PROJECT(reporter_id, be_reported_project_id, report_time, reason)
                VALUES(:reporter_id, :be_reported_project_id, :report_time, :reason)";
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(':reporter_id', $reporter_id, PDO::PARAM_STR);
                $stmt->bindValue(':be_reported_project_id', $be_reported_project_id, PDO::PARAM_STR);
                $stmt->bindValue(':report_time', $report_time, PDO::PARAM_STR);
                $stmt->bindValue(':reason', $reason, PDO::PARAM_STR);
                if($stmt->execute()){
                    echo "檢舉已送出!";
                    echo "<a href='main_page.php'>回首頁</a>";
                }
                else{
                    echo "檢舉送出失敗";
                }
            }
        }
        ?>
    </body>
</html>
