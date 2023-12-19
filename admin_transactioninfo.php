<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Transaction Info Search</title>
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

        <h1>Search for Transaction Information</h1>
        <form action="admin_transactioninfo.php" method="post">
            <div>
                <label for="category">Category:</label>
                <select id="category" name="category">
                    <option value="transaction_id" <?php echo ($_POST['category'] ?? '') == 'transaction_id' ? 'selected' : ''; ?>>Transaction ID</option>
                    <option value="seller_id" <?php echo ($_POST['category'] ?? '') == 'seller_id' ? 'selected' : ''; ?>>Seller ID</option>
                    <option value="buyer_id" <?php echo ($_POST['category'] ?? '') == 'buyer_id' ? 'selected' : ''; ?>>Buyer ID</option>
                    <option value="transaction_time" <?php echo ($_POST['category'] ?? '') == 'transaction_time' ? 'selected' : ''; ?>>Transaction Time</option>
                    <option value="transaction_status" <?php echo ($_POST['category'] ?? '') == 'transaction_status' ? 'selected' : ''; ?>>Transaction Status</option>
                    <option value="project_id" <?php echo ($_POST['category'] ?? '') == 'project_id' ? 'selected' : ''; ?>>Project ID</option>
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
            $category = $_POST['category'] ?? 'transaction_id';
            $search_query = $_POST['search_query'] ?? '';

            // Validate and sanitize user input
            $validCategories = ['transaction_id', 'seller_id', 'buyer_id', 'transaction_time', 'transaction_status', 'project_id'];
            if (!in_array($category, $validCategories)) {
                die("Invalid category selected.");
            }

            // Modify your SQL query based on the selected category
            $sql = "SELECT * FROM public.TRANSACTION WHERE $category ";

            // If the selected category is 'transaction_time', use the timestamp with time zone and LIKE for partial match
            if ($category == 'transaction_time') {
                $sql .= "::text LIKE :search_query";
                $search_query .= '%';
            } else {
                $sql .= "ILIKE :search_query";
            }

            try {
                $statement = $pdo->prepare($sql);
                $statement->bindParam(':search_query', $search_query, PDO::PARAM_STR);
                $statement->execute();
                $results = $statement->fetchAll(PDO::FETCH_ASSOC);

                // Display search results
                echo "<h2>Search Results</h2>";
                echo "<table>";
                echo "<tr><th>Transaction ID</th><th>Seller ID</th><th>Buyer ID</th><th>Transaction Time</th><th>Transaction Status</th><th>Project ID</th></tr>";

                foreach ($results as $row) {
                    echo "<tr><td>" . $row['transaction_id'] . "</td><td>" . $row['seller_id'] . "</td><td>" . $row['buyer_id'] . "</td><td>" . $row['transaction_time'] . "</td><td>" . $row['transaction_status'] . "</td><td>" . $row['project_id'] . "</td></tr>";
                }

                echo "</table>";
            } catch (PDOException $e) {
                echo "Error executing query: " . $e->getMessage();
            }
        }

        // Fetch and display all transactions regardless of search
        $allTransactionsQuery = "SELECT * FROM public.TRANSACTION";
        $allTransactionsStatement = $pdo->query($allTransactionsQuery);
        $allTransactions = $allTransactionsStatement->fetchAll(PDO::FETCH_ASSOC);

        if ($allTransactions) {
            echo "<h2>All Transactions</h2>";
            echo "<table>";
            echo "<tr><th>Transaction ID</th><th>Seller ID</th><th>Buyer ID</th><th>Transaction Time</th><th>Transaction Status</th><th>Project ID</th></tr>";
            foreach ($allTransactions as $row) {
                echo "<tr><td>" . $row['transaction_id'] . "</td><td>" . $row['seller_id'] . "</td><td>" . $row['buyer_id'] . "</td><td>" . $row['transaction_time'] . "</td><td>" . $row['transaction_status'] . "</td><td>" . $row['project_id'] . "</td></tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No transactions found.</p>";
        }
        ?>
    </div>
    <?php
    include 'footer.php';
    ?>
</body>

</html>