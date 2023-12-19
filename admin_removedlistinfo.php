<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Removed Project Information Search</title>
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

        <h1>Search for Removed Project Information</h1>
        <form action="admin_removedlistinfo.php" method="post">
            <div>
                <label for="category">Category:</label>
                <select id="category" name="category">
                    <option value="be_reported_project_id" <?php echo ($_POST['category'] ?? '') == 'be_reported_project_id' ? 'selected' : ''; ?>>Reported Project ID</option>
                    <option value="removed_time" <?php echo ($_POST['category'] ?? '') == 'removed_time' ? 'selected' : ''; ?>>Removed Time</option>
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
            $category = $_POST['category'] ?? 'be_reported_project_id';
            $search_query = $_POST['search_query'] ?? '';

            // Validate and sanitize user input
            $validCategories = ['be_reported_project_id', 'removed_time'];
            if (!in_array($category, $validCategories)) {
                die("Invalid category selected.");
            }

            // Modify your SQL query based on the selected category
            $sql = "SELECT * FROM public.REMOVED_LIST WHERE $category::text ILIKE :search_query";

            try {
                $statement = $pdo->prepare($sql);

                // If the selected category is 'removed_time', append % to the search query
                // This is for a partial match, assuming the date is in the format 'YYYY-MM-DD'
                if ($category == 'removed_time') {
                    $search_query .= '%';
                }

                // Bind parameters
                $statement->bindParam(':search_query', $search_query, PDO::PARAM_STR);

                // Execute the query
                $statement->execute();
                $results = $statement->fetchAll(PDO::FETCH_ASSOC);

                // Display search results
                echo "<h2>Search Results</h2>";
                echo "<table>";
                echo "<tr><th>Reported Project ID</th>";

                // If the selected category is 'removed_time', display 'Report Date' instead of 'Report Time'
                if ($category == 'removed_time') {
                    echo "<th>Report Date</th>";
                } else {
                    echo "<th>Report Time</th>";
                }

                echo "</tr>";

                foreach ($results as $row) {
                    echo "<tr><td>" . $row['be_reported_project_id'] . "</td>";

                    // If the selected category is 'removed_time', display the date part only
                    if ($category == 'removed_time') {
                        echo "<td>" . $row['removed_time'] . "</td>";
                    } else {
                        echo "<td>" . $row['removed_time'] . "</td>";
                    }

                    echo "</tr>";
                }
            } catch (PDOException $e) {
                echo "Error executing query: " . $e->getMessage();
            }
        }

        // Fetch and display all entries regardless of search
        $allEntriesQuery = "SELECT * FROM public.REMOVED_LIST";
        $allEntriesStatement = $pdo->query($allEntriesQuery);
        $allEntries = $allEntriesStatement->fetchAll(PDO::FETCH_ASSOC);

        if ($allEntries) {
            echo "<h2>All Entries</h2>";
            echo "<table>";
            echo "<tr><th>Reported Project ID</th><th>Report Time</th></tr>";
            foreach ($allEntries as $row) {
                echo "<tr><td>" . $row['be_reported_project_id'] . "</td><td>" . $row['removed_time'] . "</td></tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No entries found.</p>";
        }

        // ...
        ?>

    </div>
    <?php
    include 'footer.php';
    ?>
</body>

</html>