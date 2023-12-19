<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Blocklist Info Search</title>
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

        <h1>Search for Blocklist Information</h1>
        <form action="admin_blocklistinfo.php" method="post">
            <div>
                <label for="category">Category:</label>
                <select id="category" name="category">
                    <option value="suspended_id" <?php echo ($_POST['category'] ?? '') == 'suspended_id' ? 'selected' : ''; ?>>Suspended ID</option>
                    <option value="suspended_time" <?php echo ($_POST['category'] ?? '') == 'suspended_time' ? 'selected' : ''; ?>>Suspended Time</option>
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
            $category = $_POST['category'] ?? 'suspended_id';
            $search_query = $_POST['search_query'] ?? '';

            // Validate and sanitize user input
            $validCategories = ['suspended_id', 'suspended_time'];
            if (!in_array($category, $validCategories)) {
                die("Invalid category selected.");
            }

            // Modify your SQL query based on the selected category
            $sql = "SELECT * FROM public.BLOCKLIST WHERE $category ";

            // If the selected category is 'suspended_time', use the timestamp with time zone and LIKE for partial match
            if ($category == 'suspended_time') {
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
                echo "<tr><th>Suspended ID</th>";

                // If the selected category is 'suspended_time', display 'Suspended Date' instead of 'Suspended Time'
                if ($category == 'suspended_time') {
                    echo "<th>Suspended Date</th>";
                } else {
                    echo "<th>Suspended Time</th>";
                }

                echo "</tr>";

                foreach ($results as $row) {
                    echo "<tr><td>" . $row['suspended_id'] . "</td>";

                    // If the selected category is 'suspended_time', display the date part only
                    if ($category == 'suspended_time') {
                        echo "<td>" . $row['suspended_time'] . "</td>";
                    } else {
                        echo "<td>" . $row['suspended_time'] . "</td>";
                    }

                    echo "</tr>";
                }
            } catch (PDOException $e) {
                echo "Error executing query: " . $e->getMessage();
            }
        }

        // Fetch and display all entries regardless of search
        $allEntriesQuery = "SELECT * FROM public.BLOCKLIST";
        $allEntriesStatement = $pdo->query($allEntriesQuery);
        $allEntries = $allEntriesStatement->fetchAll(PDO::FETCH_ASSOC);

        if ($allEntries) {
            echo "<h2>All Entries</h2>";
            echo "<table>";
            echo "<tr><th>Suspended ID</th><th>Suspended Time</th></tr>";
            foreach ($allEntries as $row) {
                echo "<tr><td>" . $row['suspended_id'] . "</td><td>" . $row['suspended_time'] . "</td></tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No entries found.</p>";
        }

        // ...
        ?>

    </div>
</body>

</html>