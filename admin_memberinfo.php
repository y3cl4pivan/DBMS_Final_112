<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Member Info Search</title>
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
        ?>

        <h1>Search for Member Information</h1>
        <form action="admin_memberinfo.php" method="post">
            <div>
                <label for="category">Category:</label>
                <select id="category" name="category">
                    <option value="name" <?php echo ($_POST['category'] ?? '') == 'name' ? 'selected' : ''; ?>>Name</option>
                    <option value="member_id" <?php echo ($_POST['category'] ?? '') == 'member_id' ? 'selected' : ''; ?>>Member ID</option>
                    <option value="preferred_transaction_place" <?php echo ($_POST['category'] ?? '') == 'preferred_transaction_place' ? 'selected' : ''; ?>>Preferred Transaction Place</option>
                    <option value="department" <?php echo ($_POST['category'] ?? '') == 'department' ? 'selected' : ''; ?>>Department</option>
                    <option value="email" <?php echo ($_POST['category'] ?? '') == 'email' ? 'selected' : ''; ?>>Email</option>
                    <option value="status" <?php echo ($_POST['category'] ?? '') == 'status' ? 'selected' : ''; ?>>Status</option>
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
            $category = $_POST['category'] ?? 'name';
            $search_query = $_POST['search_query'] ?? '';

            // Validate and sanitize user input
            $validCategories = ['name', 'member_id', 'preferred_transaction_place', 'department', 'email', 'status'];
            if (!in_array($category, $validCategories)) {
                die("Invalid category selected.");
            }

            // Modify your SQL query based on the selected category
            $sql = "SELECT * FROM public.MEMBER WHERE $category ILIKE :search_query";

            try {
                $statement = $pdo->prepare($sql);
                $statement->bindParam(':search_query', $search_query, PDO::PARAM_STR);
                $statement->execute();
                $results = $statement->fetchAll(PDO::FETCH_ASSOC);

                if ($results) {
                    echo "<h2>Search Results</h2>";
                    echo "<div style='float: left;'>";
                    echo "<table>";
                    echo "<tr><th>Member ID</th><th>Name</th><th>Department</th><th>Email</th><th>Preferred Transaction Place</th><th>Status</th><th>Actions</th></tr>";
                    foreach ($results as $row) {
                        echo "<tr>";
                        echo "<td>" . $row['member_id'] . "</td>";
                        echo "<td>" . $row['name'] . "</td>";
                        echo "<td>" . $row['department'] . "</td>";
                        echo "<td>" . $row['email'] . "</td>";
                        echo "<td>" . $row['preferred_transaction_place'] . "</td>";
                        echo "<td>" . $row['status'] . "</td>";
                        echo "<td class='actions'>";
                        echo "<form action='update_status.php' method='post'>";
                        echo "<input type='hidden' name='member_id' value='" . $row['member_id'] . "'>";
                        echo "<select name='new_status'>";
                        echo "<option value='active' " . ($row['status'] == 'active' ? 'selected' : '') . ">Active</option>";
                        echo "<option value='suspended' " . ($row['status'] == 'suspended' ? 'selected' : '') . ">Suspended</option>";
                        echo "</select>";
                        echo "<input type='submit' value='Update' style='width: 80px;'>";
                        echo "</form>";
                        echo "</td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                    echo "</div>";
                } else {
                    echo "<p>No members found for the search criteria.</p>";
                }
            } catch (PDOException $e) {
                echo "Error executing query: " . $e->getMessage();
            }
        }

        // Fetch and display all members regardless of search
        $allMembersQuery = "SELECT * FROM public.MEMBER";
        $allMembersStatement = $pdo->query($allMembersQuery);
        $allMembers = $allMembersStatement->fetchAll(PDO::FETCH_ASSOC);

        if ($allMembers) {
            echo "<h2>All Members</h2>";
            echo "<div style='float: left;'>";
            echo "<table>";
            echo "<tr><th>Member ID</th><th>Name</th><th>Department</th><th>Email</th><th>Preferred Transaction Place</th><th>Status</th><th>Actions</th></tr>";
            foreach ($allMembers as $row) {
                echo "<tr>";
                echo "<td>" . $row['member_id'] . "</td>";
                echo "<td>" . $row['name'] . "</td>";
                echo "<td>" . $row['department'] . "</td>";
                echo "<td>" . $row['email'] . "</td>";
                echo "<td>" . $row['preferred_transaction_place'] . "</td>";
                echo "<td>" . $row['status'] . "</td>";
                echo "<td class='actions'>";
                echo "<form action='update_status.php' method='post'>";
                echo "<input type='hidden' name='member_id' value='" . $row['member_id'] . "'>";
                echo "<select name='new_status'>";
                echo "<option value='active' " . ($row['status'] == 'active' ? 'selected' : '') . ">Active</option>";
                echo "<option value='suspended' " . ($row['status'] == 'suspended' ? 'selected' : '') . ">Suspended</option>";
                echo "</select>";
                echo "<input type='submit' value='Update' style='width: 80px;'>";
                echo "</form>";
                echo "</td>";
                echo "</tr>";
            }
            echo "</table>";
            echo "</div>";
        } else {
            echo "<p>No members found.</p>";
        }

        ?>
    </div>
    
</body>

</html>