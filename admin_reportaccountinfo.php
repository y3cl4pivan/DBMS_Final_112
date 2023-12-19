<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Report Account Info Search</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <?php
        $host = 'localhost';
        $port = 2926;
        $dbname = 'NTUsed_1218';
        $user = 'postgres';
        $password = trim(file_get_contents('db_password.txt'));
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

        <h1>Search for Report Account Information</h1>
        <form action="admin_reportaccountinfo.php" method="post">
            <div>
                <label for="category">Category:</label>
                <select id="category" name="category">
                    <option value="reporter_id" <?php echo ($_POST['category'] ?? '') == 'reporter_id' ? 'selected' : ''; ?>>Reporter ID</option>
                    <option value="be_reported_id" <?php echo ($_POST['category'] ?? '') == 'be_reported_id' ? 'selected' : ''; ?>>Reported Account ID</option>
                    <option value="report_time" <?php echo ($_POST['category'] ?? '') == 'report_time' ? 'selected' : ''; ?>>Report Time</option>
                    <option value="reason" <?php echo ($_POST['category'] ?? '') == 'reason' ? 'selected' : ''; ?>>Reason</option>
                    <!-- Add more options for other categories as needed -->
                </select>
            </div>
            <div>
                <label for="search_query">Search:</label>
                <input type="text" id="search_query" name="search_query" value="<?php echo $_POST['search_query'] ?? ''; ?>">
            </div>
            <input type="submit" value="Search">
        </form>

        <?php
        // ...

        if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "POST") {
            $category = $_POST['category'] ?? 'reporter_id';
            $search_query = $_POST['search_query'] ?? '';

            // Validate and sanitize user input
            $validCategories = ['reporter_id', 'be_reported_id', 'report_time', 'reason'];
            if (!in_array($category, $validCategories)) {
                die("Invalid category selected.");
            }

            // Modify your SQL query based on the selected category
            if ($category == 'report_time') {
                // If searching for 'report_time', extract date part and search
                $sql = "SELECT * FROM public.REPORT_ACCOUNT WHERE DATE(report_time) = :search_query ORDER BY report_time DESC";
            } else {
                $sql = "SELECT * FROM public.REPORT_ACCOUNT WHERE $category ILIKE :search_query ORDER BY report_time DESC";
            }

            try {
                $statement = $pdo->prepare($sql);

                // If the selected category is 'report_time', append % to the search query
                if ($category == 'report_time') {
                    $search_query .= '%';
                }

                $statement->bindParam(':search_query', $search_query, PDO::PARAM_STR);
                $statement->execute();
                $results = $statement->fetchAll(PDO::FETCH_ASSOC);

                if ($results) {
                    echo "<h2>Search Results</h2>";
                    echo "<table>";
                    echo "<tr><th>Reporter ID</th><th>Reported Account ID</th><th>Report Time</th><th>Reason</th></tr>";
                    foreach ($results as $row) {
                        echo "<tr><td>" . $row['reporter_id'] . "</td><td>" . $row['be_reported_id'] . "</td><td>" . $row['report_time'] . "</td><td>" . $row['reason'] . "</td></tr>";
                    }
                    echo "</table>";
                } else {
                    echo "<p>No reports found for the search criteria.</p>";
                }
            } catch (PDOException $e) {
                echo "Error executing query: " . $e->getMessage();
            }
        }

        // Fetch and display all reports regardless of search
        $allReportsQuery = "SELECT * FROM public.REPORT_ACCOUNT ORDER BY report_time DESC";
        $allReportsStatement = $pdo->query($allReportsQuery);
        $allReports = $allReportsStatement->fetchAll(PDO::FETCH_ASSOC);

        if ($allReports) {
            echo "<h2>All Reports</h2>";
            echo "<table>";
            echo "<tr><th>Reporter ID</th><th>Reported Account ID</th><th>Report Time</th><th>Reason</th></tr>";
            foreach ($allReports as $row) {
                echo "<tr><td>" . $row['reporter_id'] . "</td><td>" . $row['be_reported_id'] . "</td><td>" . $row['report_time'] . "</td><td>" . $row['reason'] . "</td></tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No reports found.</p>";
        }

        // ...
        ?>

    </div>
    <?php
    include 'footer.php';
    ?>
</body>

</html>