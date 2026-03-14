<?php
    session_start();
    require_once "database.php";
    
    //store session data
    if(isset($_SESSION["views"]))
        $_SESSION["views"]=$_SESSION["views"]+1;
    else
        $_SESSION["views"]=1;
    //echo "Views=". $_SESSION['views'];
    

    //make sure user is logged in
    if (!isset($_SESSION["UserId"]))
    {
        header("Location: login.php");
        exit();
    };

    //get search inputs
    $search = isset($_GET["search"]) ? trim($_GET["search"]) : '';
    $category = isset($_GET["categories"]) ? $_GET["categories"] : '';

    // Pagination setup
    $limit = 5; // rows per page
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    if($page < 1) $page = 1;
    $offset = ($page - 1) * $limit;

    //build query to get data from database
    $sql = "SELECT b.ISBN, b.BookTitle, b.Author, c.CategoryDescription, b.Edition, b.Reserved FROM Books b JOIN Categories c ON b.Category = c.CategoryID WHERE 1=1";

    $params = [];
    $types = '';

    // Apply search filter
    if (!empty($search)) {
        $sql .= " AND (b.BookTitle LIKE ? OR b.Author LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
        $types .= "ss";
    }

    // Apply category filter
    if (!empty($category)) {
        $sql .= " AND b.Category = ?";
        $params[] = $category;
        $types .= "i";
    }

    // Add pagination
    $sql .= " LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;
    $types .= "ii";

    // Prepare + execute
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();


    // Count total rows for pagination
    $countSql = "SELECT COUNT(*) as total FROM Books b JOIN Categories c ON b.Category = c.CategoryID WHERE 1=1";
    $countParams = [];
    $countTypes = '';

    //this allows partial search
    if(!empty($search))
    {
        $countSql .= " AND (b.BookTitle LIKE ? OR b.Author LIKE ?)";
        $countParams[] = "%$search%";
        $countParams[] = "%$search%";
        $countTypes .= 'ss';
    }

    //this allows category search
    if(!empty($category))
    {
        $countSql .= " AND b.Category = ?";
        $countParams[] = $category;
        $countTypes .= 'i';
    
    }

    //prepare statement
    $countStmt = $conn->prepare($countSql);
    if($countTypes)
    {
        $countStmt->bind_param($countTypes, ...$countParams);
    }

    $countStmt->execute();
    $countResult = $countStmt->get_result();
    $totalRows = $countResult->fetch_assoc()['total'];
    $totalPages = ceil($totalRows / $limit);

    if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['logout']))
    {
        session_destroy(); 
        //delete cookie
        setcookie("Username", "", time()-3600, "/");
        //go to login page
        header("Location: login.php");
        exit();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results</title>
    <link rel="stylesheet" href="../css/searchResults.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Text:ital@0;1&display=swap" rel="stylesheet">
</head>
<body>
    <?php if(isset($_SESSION['message'])): ?>
        <p id="search-feedback"><?php 
            echo htmlspecialchars($_SESSION['message']); 
            unset($_SESSION['message']); // clear the message so it shows only once
        ?></p>
    <?php endif; ?>

        <!--This is for the name of the library-->
    <div class="stripe">
        <p id="title" >Blooms</p>
        <a href="home.php" id="menu-button">Home</a>
        <a href="reserved.php" id="button">My Reserved Books</a>
            <!--Logout-->
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="post">
            <input type="submit" name="logout" value="logout" id="button" >
        </form>
    </div>
    

    <h2 id="search-results-title">Search Results</h2>
    <div id="search-results-container">
    
            <!--Table headings-->
        <?php if($result->num_rows > 0): ?>
            <table id="search-results-table">
                <tr class="search-results-header">
                    <th class="sr-title">Title</th>
                    <th class="sr-author">Author</th>
                    <th class="sr-category">Category</th>
                    <th class="sr-availability">Availability</th>
                    <th class="sr-action">Action</th>
                </tr>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr class="search-results-row">
                        <td class="sr-title"><?php echo htmlspecialchars($row["BookTitle"]); ?></td>
                        <td class="sr-author"><?php echo htmlspecialchars($row["Author"]); ?></td>
                        <td class="sr-category"><?php echo htmlspecialchars($row["CategoryDescription"]); ?></td>
                        <td class="sr-availability"><?php echo $row["Reserved"] == 0 ? "Available" : "Reserved"; ?></td>
                        <td class="sr-action">
                            <!--If the book is availible to reserve there is a reserve button-->
                            <?php if($row["Reserved"] == 0): ?>
                                <form method="post" action="reserveBook.php">
                                    <input type="hidden" name="ISBN" value="<?php echo $row["ISBN"]; ?>">
                                    <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                                    <input type="hidden" name="categories" value="<?php echo htmlspecialchars($category); ?>">
                                    <input type="hidden" name="page" value="<?php echo $page; ?>">
                                    <button type="submit">Reserve</button>
                                </form>

                            <?php else: ?>
                                N/A
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
            </table>
        
        <!--If there are no results-->
        <?php else: ?>
            <p id="no-results">No books found matching your search.</p>
        <?php endif; ?>

        <!-- Pagination -->
        <div class="grid_pagination">
            <div id="pagination">
                <!-- Previous arrow (always visible) -->
                <a class="pagination-arrow <?php echo ($page <= 1) ? 'disabled' : ''; ?>" href="<?php echo ($page > 1) ? '?search=' . urlencode($search) . '&categories=' . urlencode($category) . '&page=' . ($page-1) : '#'; ?>">&larr;</a>

                <!-- Page indicator -->
                <span class="page-indicator">Page <?php echo $page; ?> of <?php echo $totalPages; ?></span>

                <!-- Next arrow (always visible) -->
                <a class="pagination-arrow <?php echo ($page >= $totalPages) ? 'disabled' : ''; ?>"href="<?php echo ($page < $totalPages) ? '?search=' . urlencode($search) . '&categories=' . urlencode($category) . '&page=' . ($page+1) : '#'; ?>">&rarr;</a>
            </div>
            
        </div>

    </div>
    
</body>
</html>