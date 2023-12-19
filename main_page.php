<!DOCTYPE html>
<html lang="zh-Hant-TW">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NTUsed</title>
    <style>
        .result-box {
            border: 4px solid #ccc;
            padding: 30px;
            margin-bottom: 30px;
        }
    </style>
</head>

<body>
    <!-- 主頁按鈕 -->
    <div>
        <?php
        session_start();
        $member_id = $_SESSION['member_id'];

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
        <input type="button" value="NTUsed" onclick="location.href='main_page.php'">

        <form method="post">
            <label for="search_term">搜尋關鍵字：</label>
            <input type="text" id="search_term" name="search_term" placeholder="輸入搜尋關鍵字">
            <button type="submit">搜尋</button>

            <input type="radio" id="search_by_title" name="search_by" value="title" checked>
            <label for="search_by_title">書名</label>

            <input type="radio" id="search_by_author" name="search_by" value="author">
            <label for="search_by_author">作者</label>

            <p>排序方式：
                <select name="order_by">
                    <option value="popularity">熱度</option>
                    <option value="price">價格</option>
                    <option value="launch_date">上架日期</option>
                </select>
            </p>

            <p>書本分類：
                <select name="book_category">
                    <option value="">所有分類</option>
                    <option value="世界古典文學">世界古典文學</option>
                    <option value="中國古典文學">中國古典文學</option>
                    <option value="人文社科">人文社科</option>
                    <option value="哲學思潮">哲學思潮</option>
                    <option value="圖畫書 / 繪本">圖畫書 / 繪本</option>
                    <option value="宗教">宗教</option>
                    <option value="家庭親子">家庭親子</option>
                    <option value="家庭／兩性">家庭／兩性</option>
                    <option value="寵物">寵物</option>
                    <option value="居家 / 花藝">居家 / 花藝</option>
                    <option value="建築">建築</option>
                    <option value="心理勵志">心理勵志</option>
                    <option value="恐怖 / 驚悚小說">恐怖 / 驚悚小說</option>
                    <option value="懸疑 / 推理小說">懸疑 / 推理小說</option>
                    <option value="戶外活動 / 運動">戶外活動 / 運動</option>
                    <option value="手工藝">手工藝</option>
                    <option value="文學">文學</option>
                    <option value="文學理論">文學理論</option>
                    <option value="日本文學">日本文學</option>
                    <option value="時尚美妝 / 保養">時尚美妝 / 保養</option>
                    <option value="歐美文學">歐美文學</option>
                    <option value="歷史 / 武俠小說">歷史 / 武俠小說</option>
                    <option value="生活">生活</option>
                    <option value="療癒小說">療癒小說</option>
                    <option value="社會史地">社會史地</option>
                    <option value="科學百科">科學百科</option>
                    <option value="科幻 / 奇幻小說">科幻 / 奇幻小說</option>
                    <option value="童書">童書</option>
                    <option value="經貿理財">經貿理財</option>
                    <option value="華文創作">華文創作</option>
                    <option value="行銷企管">行銷企管</option>
                    <option value="親子">親子</option>
                    <option value="言情小說">言情小說</option>
                    <option value="親子">親子</option>
                    <option value="設計美術">設計美術</option>
                    <option value="醫療保健">醫療保健</option>
                    <option value="青少年文學">青少年文學</option>
                    <option value="飲食生活">飲食生活</option>
                    <option value="其他地區翻譯文學">其他地區翻譯文學</option>
                    <option value="其它">其它</option>
                </select>
            </p>

        </form>

        <?php


        // echo $member_id;
        // 用户输入的搜索条件
        $searchBy = isset($_GET['search_by']) ? $_GET['search_by'] : "title";
        $searchTerm = isset($_POST['searchTerm']) ? $_POST['searchTerm'] : "";
        // 检查是否有输入搜索条件，如果没有则使用默认排序方式
        $orderBy = isset($_GET['order_by']) ? $_GET['order_by'] : "popularity";

        // 现在$orderBy包含要使用的排序方式，要么是用户指定的搜索条件，要么是默认值



        // 檢查是否有表單提交
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // 獲取表單資料
            $searchBy = $_POST["search_by"];
            $searchTerm = $_POST["search_term"];
            $orderBy = $_POST["order_by"];
            $bookCategory = $_POST["book_category"];
        }

        // 初始化 SQL 查詢
        $sql = "SELECT BOOK.book_id, BOOK.book_name, BOOK_AUTHORS.author_name, PROJECT.project_price, PROJECT.project_sell_status, PROJECT.launch_date, PROJECT.project_id
        FROM BOOK
        JOIN BOOK_AUTHORS ON BOOK.book_id = BOOK_AUTHORS.book_id
        JOIN PRODUCT ON BOOK.book_id = PRODUCT.book_id
        JOIN PROJECT_PRODUCT_INFO ON PRODUCT.product_id = PROJECT_PRODUCT_INFO .product_id
        JOIN PROJECT ON PROJECT_PRODUCT_INFO .project_id = PROJECT.project_id
        JOIN BOOK_CLASSIFICATION ON BOOK.book_id = BOOK_CLASSIFICATION.book_id
        WHERE 1=1"; // 1=1 是為了簡化後續的條件拼接

        // 依書名或依作者的搜尋方式不同，構建不同的 SQL 查詢
        if ($searchBy === "title") {
            $sql .= " AND BOOK.book_name ILIKE '%$searchTerm%'";
        } elseif ($searchBy === "author") {
            $sql .= " AND BOOK_AUTHORS.author_name ILIKE '%$searchTerm%'";
        }

        // 選擇特定書本分類
        if (!empty($bookCategory)) {
            $sql .= " AND BOOK_CLASSIFICATION.classification_name = '$bookCategory'";
        }

        // 排序方式是先依據status,project_id,再依使用者選擇的排序方式進行排序
        switch ($orderBy) {
            case "price":
                $sql .= " ORDER BY PROJECT.project_sell_status, BOOK.book_id ASC, PROJECT.project_price ASC";
                break;
            case "launch_date":
                $sql .= " ORDER BY PROJECT.project_sell_status, BOOK.book_id ASC, PROJECT.launch_date DESC";
                break;
            case "popularity":
            default:
                $sql .= " ORDER BY PROJECT.project_sell_status, BOOK.book_id ASC, BOOK.popularity DESC";
                break;
        }

        //計算資料總數用
        $sql2 = $sql;
        $stmt2 = $pdo->prepare($sql2);
        $stmt2->execute();

        // 設定每頁顯示的資料筆數
        $perPage = 20;
        // 獲取當前頁碼，如果未設定，預設為第1頁
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        // 計算 OFFSET
        $offset = ($page - 1) * $perPage;
        // 加入 LIMIT 和 OFFSET 到 SQL 查詢
        $sql .= " LIMIT $perPage OFFSET $offset";

        // 執行查詢
        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        // 計算總頁數
        $totalPages = ceil($stmt2->rowCount() / $perPage);

        // 顯示結果
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<div class='result-box'>";
            echo "<input type='hidden' name='book_id' value='" . $row['book_id'] . "'>";
            echo "<input type='hidden' name='project_id' value='" . $row['project_id'] . "'>";
            $href1 = "merchandise.php?project_id={$row['project_id']}";
            echo "<a href= " . $href1 . ">{$row['book_name']}</a><br>";
            echo "<input type='hidden' name='book_id' value='" . $row['author_name'] . "'>";
            echo "\${$row['project_price']}<br>";
            echo "狀態：{$row['project_sell_status']}<br>";
            echo "<input type='hidden' name='book_id' value='" . $row['launch_date'] . "'>";
            echo "</div>";
        }

        // 關閉資料庫連線
        // $pdo = null;

        // 計算上一頁和下一頁的頁碼
        $prevPage = ($page > 1) ? $page - 1 : 1;
        $nextPage = ($page < $totalPages) ? $page + 1 : $totalPages;

        // 附加查询参数到分页链接
        $prevPageLink = "main_page.php?page=$prevPage&order_by=$orderBy&search_by=$searchBy&search_term=$searchTerm";
        $currentPageLink = "main_page.php?page=$page&order_by=$orderBy&search_by=$searchBy&search_term=$searchTerm";
        $nextPageLink = "main_page.php?page=$nextPage&order_by=$orderBy&search_by=$searchBy&search_term=$searchTerm";

        // 顯示分頁連結
        echo "<div class='pagination'>";
        echo "<a href='$prevPageLink'>上一頁</a>";
        echo "\t";
        echo "<a href='$currentPageLink'>$page</a>";
        echo "\t";
        echo "<a href='$nextPageLink'>下一頁</a> ";
        echo "</div>";
        ?>
    </div>
    <?php
    include('footer.php');
    ?>
</body>

</html>