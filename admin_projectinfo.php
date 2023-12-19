<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Project Info Search</title>
    <style>
        body {
            background-color: #fff;
            /* Set background color to white */
        }

        .container {
            width: 80%;
            margin: 0 auto;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        h1,
        h2 {
            clear: both;
            /* Clear the float property to prevent layout issues */
        }

        form {
            margin-bottom: 20px;
        }

        select,
        input[type="submit"] {
            margin-top: 3px;
            /* Adjusted margin-top */
            vertical-align: middle;
            /* Align elements vertically */
        }

        .float-left {
            float: left;
            margin-right: 20px;
        }

        .actions {
            display: flex;
            align-items: center;
        }

        .actions form {
            margin-bottom: 0;
            margin-right: 10px;
        }
    </style>

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
        // Define the lock file paths
        $readLockFile = 'admin_projectinfo_read.lock';
        $writeLockFile = 'admin_projectinfo_write.lock';

        // Acquire read lock
        if (file_exists($writeLockFile) || file_exists($readLockFile)) {
            die("Another process is currently accessing the data. Please try again later.");
        }

        file_put_contents($readLockFile, 'read');
        ?>

        <h1>Search for Project Information</h1>
        <form action="admin_projectinfo.php" method="post">
            <div>
                <label for="category">Category:</label>
                <select id="category" name="category">
                    <option value="project_id" <?php echo ($_POST['category'] ?? '') == 'project_id' ? 'selected' : ''; ?>>Project ID</option>
                    <option value="project_price" <?php echo ($_POST['category'] ?? '') == 'project_price' ? 'selected' : ''; ?>>Project Price</option>
                    <option value="project_seller_id" <?php echo ($_POST['category'] ?? '') == 'project_seller_id' ? 'selected' : ''; ?>>Project Seller ID</option>
                    <option value="project_sell_status" <?php echo ($_POST['category'] ?? '') == 'project_sell_status' ? 'selected' : ''; ?>>Project Sell Status</option>
                    <option value="launch_date" <?php echo ($_POST['category'] ?? '') == 'launch_date' ? 'selected' : ''; ?>>Launch Date</option>
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
            $category = $_POST['category'] ?? 'project_id';
            $search_query = $_POST['search_query'] ?? '';

            // Validate and sanitize user input
            $validCategories = ['project_id', 'project_price', 'project_seller_id', 'project_sell_status', 'launch_date'];
            if (!in_array($category, $validCategories)) {
                die("Invalid category selected.");
            }

            // Modify your SQL query based on the selected category
            $sql = "SELECT * FROM public.PROJECT WHERE $category ILIKE :search_query";

            try {
                $statement = $pdo->prepare($sql);
                $statement->bindParam(':search_query', $search_query, PDO::PARAM_STR);
                $statement->execute();
                $results = $statement->fetchAll(PDO::FETCH_ASSOC);

                if ($results) {
                    echo "<h2>Search Results</h2>";
                    echo "<div style='float: left;'>";
                    echo "<table>";
                    echo "<tr><th>Project ID</th><th>Project Price</th><th>Project Seller ID</th><th>Project Sell Status</th><th>Launch Date</th><th>Actions</th></tr>";
                    foreach ($results as $row) {
                        echo "<tr>";
                        echo "<td>" . $row['project_id'] . "</td>";
                        echo "<td>" . $row['project_price'] . "</td>";
                        echo "<td>" . $row['project_seller_id'] . "</td>";
                        echo "<td>" . $row['project_sell_status'] . "</td>";
                        echo "<td>" . $row['launch_date'] . "</td>";
                        echo "<td class='actions'>";
                        echo "<form action='update_project_status.php' method='post'>";
                        echo "<input type='hidden' name='project_id' value='" . $row['project_id'] . "'>";
                        echo "<select name='new_status'>";
                        echo "<option value='available' " . ($row['project_sell_status'] == 'available' ? 'selected' : '') . ">Available</option>";
                        echo "<option value='selling' " . ($row['project_sell_status'] == 'selling' ? 'selected' : '') . ">Selling</option>";
                        echo "<option value='removed' " . ($row['project_sell_status'] == 'removed' ? 'selected' : '') . ">Removed</option>";
                        echo "<option value='unavailable' " . ($row['project_sell_status'] == 'unavailable' ? 'selected' : '') . ">Unavailable</option>";
                        echo "</select>";
                        echo "<input type='submit' value='Update' style='width: 80px;'>";
                        echo "</form>";
                        echo "</td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                    echo "</div>";
                } else {
                    echo "<p>No projects found for the search criteria.</p>";
                }
            } catch (PDOException $e) {
                echo "Error executing query: " . $e->getMessage();
            }
        }
        unlink($readLockFile);
        // Fetch and display all projects regardless of search
        $allProjectsQuery = "SELECT * FROM public.PROJECT ORDER BY CAST(project_id AS INTEGER) LIMIT 1000";
        $allProjectsStatement = $pdo->query($allProjectsQuery);
        $allProjects = $allProjectsStatement->fetchAll(PDO::FETCH_ASSOC);

        if ($allProjects) {
            echo "<h2>All Projects</h2>";
            echo "<div style='float: left;'>";
            echo "<table>";
            echo "<tr><th>Project ID</th><th>Project Price</th><th>Project Seller ID</th><th>Project Sell Status</th><th>Launch Date</th><th>Actions</th></tr>";
            foreach ($allProjects as $row) {
                echo "<tr>";
                echo "<td>" . $row['project_id'] . "</td>";
                echo "<td>" . $row['project_price'] . "</td>";
                echo "<td>" . $row['project_seller_id'] . "</td>";
                echo "<td>" . $row['project_sell_status'] . "</td>";
                echo "<td>" . $row['launch_date'] . "</td>";
                echo "<td class='actions'>";
                echo "<form action='update_project_status.php' method='post'>";
                echo "<input type='hidden' name='project_id' value='" . $row['project_id'] . "'>";
                echo "<select name='new_status'>";
                echo "<option value='available' " . ($row['project_sell_status'] == 'available' ? 'selected' : '') . ">Available</option>";
                echo "<option value='selling' " . ($row['project_sell_status'] == 'selling' ? 'selected' : '') . ">Selling</option>";
                echo "<option value='removed' " . ($row['project_sell_status'] == 'removed' ? 'selected' : '') . ">Removed</option>";
                echo "<option value='unavailable' " . ($row['project_sell_status'] == 'unavailable' ? 'selected' : '') . ">Unavailable</option>";
                echo "</select>";
                echo "<input type='submit' value='Update' style='width: 80px;'>";
                echo "</form>";
                echo "</td>";
                echo "</tr>";
            }
            echo "</table>";
            echo "</div>";
        } else {
            echo "<p>No projects found.</p>";
        }

        ?>
    </div>
    <?php
    include 'footer.php';
    ?>
</body>

</html>