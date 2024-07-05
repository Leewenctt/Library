<?php
include ("ini.php");

// Prevent unauthorized access
if (!isset($_SESSION["user"]) || $row['role'] !== "Admin") {
    header("location: library.php");
    exit;
}

// Dashboard
$status_query = "SELECT 
        SUM(CASE WHEN status = 'Available' THEN 1 ELSE 0 END) AS available,
        SUM(CASE WHEN status = 'Reserved' THEN 1 ELSE 0 END) AS reserved,
        SUM(CASE WHEN status = 'Not Available' THEN 1 ELSE 0 END) AS not_available,
        SUM(CASE WHEN status = 'Overdue' THEN 1 ELSE 0 END) AS overdue
    FROM books";
$status_result = mysqli_query($conn, $status_query);
$status_data = mysqli_fetch_assoc($status_result);
$total_books = array_sum($status_data);

function getPercentage($value, $total)
{
    return ($total > 0) ? round(($value / $total) * 100) : 0;
}

$available_percentage = getPercentage($status_data['available'], $total_books);
$reserved_percentage = getPercentage($status_data['reserved'], $total_books);
$not_available_percentage = getPercentage($status_data['not_available'], $total_books);
$overdue_percentage = getPercentage($status_data['overdue'], $total_books);

// Add book
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $author = mysqli_real_escape_string($conn, $_POST['author']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $genre = mysqli_real_escape_string($conn, $_POST['genre']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $publisher = mysqli_real_escape_string($conn, $_POST['publisher']);
    $publication_date = mysqli_real_escape_string($conn, $_POST['publication_date']);
    $isbn = mysqli_real_escape_string($conn, $_POST['isbn']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    if (isset($_FILES["file-input"]) && $_FILES["file-input"]["tmp_name"] != "") {
        $target_dir = "../covers/";
        $target_file = $target_dir . basename($_FILES["file-input"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    
        $check = getimagesize($_FILES["file-input"]["tmp_name"]);
        if ($check !== false) {
            $uploadOk = 1;
        } else {
            $uploadOk = 0;
        }
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "webp") {
            $uploadOk = 0;
        }
        if (!$uploadOk == 0) {
            if (!file_exists($target_file)) {
                move_uploaded_file($_FILES["file-input"]["tmp_name"], $target_file);
            }
            $add_query = "INSERT INTO books (title, author, description, genre, category, publisher, publication_date, isbn, status, cover_dir) 
                      VALUES ('$title', '$author', '$description', '$genre', '$category', '$publisher', '$publication_date', '$isbn', '$status', '$target_file')";
        }
    }else{
        $add_query = "INSERT INTO books (title, author, description, genre, category, publisher, publication_date, isbn, status) 
                      VALUES ('$title', '$author', '$description', '$genre', '$category', '$publisher', '$publication_date', '$isbn', '$status')";
    }

    mysqli_query($conn, $add_query);
    header("Refresh:0");
}

// Delete book
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete'])) {
    $book_id = mysqli_real_escape_string($conn, $_POST['id']);

    $delete_query = "DELETE FROM books WHERE book_id = '$book_id'";

    mysqli_query($conn, $delete_query);
    header("Refresh:0");
}

// Edit book
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit'])) {
    $book_id = mysqli_real_escape_string($conn, $_POST['id']);
    $edit_query = "SELECT * FROM books WHERE book_id = '$book_id'";
    $edit_result = mysqli_query($conn, $edit_query);
    $edit_data = mysqli_fetch_assoc($edit_result);
}

// Update book
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    $book_id = mysqli_real_escape_string($conn, $_POST['id']);
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $author = mysqli_real_escape_string($conn, $_POST['author']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $genre = mysqli_real_escape_string($conn, $_POST['genre']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $publisher = mysqli_real_escape_string($conn, $_POST['publisher']);
    $publication_date = mysqli_real_escape_string($conn, $_POST['publication_date']);
    $isbn = mysqli_real_escape_string($conn, $_POST['isbn']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    if (isset($_FILES["file-edit"]) && $_FILES["file-edit"]["tmp_name"] != "") {
        $target_dir = "../covers/";
        $target_file = $target_dir . basename($_FILES["file-edit"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    
        $check = getimagesize($_FILES["file-edit"]["tmp_name"]);
        if ($check !== false) {
            $uploadOk = 1;
        } else {
            $uploadOk = 0;
        }
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "webp") {
            $uploadOk = 0;
        }
        if (!$uploadOk == 0) {
            if (!file_exists($target_file)) {
                move_uploaded_file($_FILES["file-edit"]["tmp_name"], $target_file);
            }
            $update_query = "UPDATE books SET 
                title = '$title', 
                author = '$author', 
                description = '$description', 
                genre = '$genre', 
                category = '$category', 
                publisher = '$publisher', 
                publication_date = '$publication_date', 
                isbn = '$isbn', 
                status = '$status',
                cover_dir =  '$target_file'
                WHERE book_id = '$book_id'";
        }
    }else{
        $update_query = "UPDATE books SET 
            title = '$title', 
            author = '$author', 
            description = '$description', 
            genre = '$genre', 
            category = '$category', 
            publisher = '$publisher', 
            publication_date = '$publication_date', 
            isbn = '$isbn', 
            status = '$status' 
            WHERE book_id = '$book_id'";
    }

    mysqli_query($conn, $update_query);
    header("location: admin.php");
}

// Pagination variables
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Current page number, default is 1
$records_per_page = 8; // Number of records to display per page
$offset = ($page - 1) * $records_per_page; // Offset for SQL query

// Sorting books and searching
$sort_by = isset($_GET['sort_by']) ? mysqli_real_escape_string($conn, $_GET['sort_by']) : 'book_id';

// Construct the WHERE clause for search
$search_query = "";
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $search_query = "WHERE title LIKE '%$search%' OR author LIKE '%$search%' OR genre LIKE '%$search%' OR category LIKE '%$search%' OR isbn LIKE '%$search%' OR status LIKE '%$search%'";
}

$sort_options = [
    'book_id' => 'ID',
    'title' => 'Title',
    'author' => 'Author',
    'genre' => 'Genre',
    'category' => 'Category',
    'isbn' => 'ISBN',
    'status' => 'Status'
];

// Count total records
$count_query = "SELECT COUNT(*) AS total FROM books $search_query";
$count_result = mysqli_query($conn, $count_query);
$count_data = mysqli_fetch_assoc($count_result);
$total_records = $count_data['total'];

// Calculate total pages
$total_pages = ceil($total_records / $records_per_page);

// Display books with pagination
$books_query = "SELECT * FROM books $search_query ORDER BY $sort_by, title LIMIT $offset, $records_per_page";
$books_result = mysqli_query($conn, $books_query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Urbanist:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="../css/admin.css">
    <style>
        /* Pie chart css */
        .circle {
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.4);
            width: 140px;
            height: 140px;
            border-radius: 50%;
            background: conic-gradient(
                #38bd0f <?= $available_percentage ?>%, 
                #e7d109 <?= $available_percentage ?>% <?= $available_percentage + $reserved_percentage ?>%, 
                #ff7300 <?= $available_percentage + $reserved_percentage ?>% <?= $available_percentage + $reserved_percentage + $not_available_percentage ?>%, 
                #db0505 <?= $available_percentage + $reserved_percentage + $not_available_percentage ?>%
            );
            display: flex;
            justify-content: center;
            align-items: center;
            color: #fff;
            font-size: 18px;
            font-weight: 700;
            text-align: center;
            padding: 5px;
        }
    </style>
</head>
<body>
    <!-- Page header -->
    <?php include('header.php'); ?>
    
    <div class="container" style="margin-top: 110px;">
        <div class="nav-links">
            <span>Navigation</span>
            <nav style="display: flex;flex-direction: column;">
                <a href="dashboard.php">Dashboard</a>
                <a href="users.php">Manage Users</a>
                <a href="admin.php"><b>Manage Books</b></a>
            </nav>
        </div>
        <div class="left-section">
            <!-- Sort by section and Search bar -->
            <div class="flex-container">
                <h2 style="margin-top: 3px;display: flex;justify-content: space-between;align-items:center;">MANAGE BOOKS
                    <form method="GET" action="admin.php" style="display:flex;">
                        <select name="sort_by" onchange="this.form.submit()" style="width: auto; margin-bottom: 0;">
                            <?php foreach ($sort_options as $key => $value): ?>
                                <option value="<?= $key ?>" <?= ($sort_by == $key) ? 'selected' : '' ?>><?= $value ?></option>
                            <?php endforeach; ?>
                        </select>
                        <input type="text" id="search" name="search" value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>" placeholder="Search">
                        <button type="submit" class="search-button">
                            <img src="../img/search.png" alt="Search">
                        </button>
                    </form>
                </h2>
            </div>
            <!-- Book table section -->
            <table>
                <tr>
                    <th>ID</th>
                    <th>COVER</th>
                    <th>TITLE</th>
                    <th>AUTHOR</th>
                    <th>GENRE</th>
                    <th>CATEGORY</th>
                    <th>ISBN</th>
                    <th>STATUS</th>
                    <th>ACTION</th>
                </tr>
                <?php
                if (mysqli_num_rows($books_result) > 0) {
                    $count = ($page - 1) * $records_per_page + 1;
                    while ($book = $books_result->fetch_assoc()) { ?>
                        <tr>
                            <td><?= $count++ ?></td>
                            <td><div style="background:url(<?= $book['cover_dir'] ?>)" class="td_img"><div></td>
                            <td><?= $book['title'] ?></td>
                            <td><?= $book['author'] ?></td>
                            <td><?= $book['genre'] ?></td>
                            <td><?= $book['category'] ?></td>
                            <td><?= $book['isbn'] ?></td>
                            <td><div class="status-circle <?= strtolower(str_replace(' ', '-', $book['status'])) ?>"></div></td>
                            <td>
                                <div style="display: flex; flex-direction: row; justify-content: space-around; align-content: center;">
                                    <form method="POST" action="admin.php#editModal">
                                        <input type="hidden" name="id" value="<?= $book['book_id'] ?>">
                                        <button type="submit" name="edit" class="tbl-btn"><img src="../img/edit.png" alt="Edit" style="width: 20px; height: 20px;"></button>
                                    </form>
                                    <form method="POST" action="admin.php">
                                        <input type="hidden" name="id" value="<?= $book['book_id'] ?>">
                                        <button type="submit" name="delete" class="tbl-btn" onclick="return confirm('Confirm remove <?= $book['title']; ?> by <?= $book['author']; ?>?')"><img src="../img/delete.png" alt="Delete" style="width: 18px; height: 18px;"></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="9">No books found.</td>
                    </tr>
                <?php } ?>
            </table>
                <div class="pagination">
                    <?php if ($total_pages > 1): ?>
                        <?php if ($page > 1): ?>
                            <a href="admin.php?page=<?= ($page - 1) ?>&sort_by=<?= $sort_by ?>&search=<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">Previous</a>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <a href="admin.php?page=<?= $i ?>&sort_by=<?= $sort_by ?>&search=<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>" <?= ($page == $i) ? 'class="active"' : '' ?>><?= $i ?></a>
                        <?php endfor; ?>
                        
                        <?php if ($page < $total_pages): ?>
                            <a href="admin.php?page=<?= ($page + 1) ?>&sort_by=<?= $sort_by ?>&search=<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">Next</a>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
        </div>
        
        <!-- Modal pop-up for edit -->
        <div id="editModal" class="modal">
            <div class="modal-content">
                <a href="" class="close">&times;</a>
                <h2 style='margin-top:0'>EDIT BOOK</h2>
                <form method="POST" class="edit-form" action="#" enctype="multipart/form-data">
                    <input type="hidden" name="id" id="editBookId" value="<?= $edit_data['book_id'] ?>">
                    <div class="input-group">
                        <div class="modal-div">
                            <label for="title">Title:</label>
                            <input type="text" id="title" name="title" value="<?= $edit_data['title'] ?>"
                                placeholder="<?= $edit_data['title'] ?>" required>
                        </div>
                        <div class="modal-div">
                            <label for="author">Author:</label>
                            <input type="text" id="author" name="author" value="<?= $edit_data['author'] ?>"
                                placeholder="<?= $edit_data['author'] ?>" required>
                        </div>
                    </div>
                    <div class="input-group" style="justify-content:start;">
                        <div class="upload-container" style="margin:0px;width: 51.3%;">
                            <!-- Book cover edit -->
                            <input type="file" id="file-edit" name="file-edit" style="display: none;" onchange="getImgPreviewEdit(event)">
                            <div class="preview" style="display:flex">
                                <div style="margin-right:40%;">
                                    Cover:
                                </div>
                                <label for="file-edit" id="preview-img-edit" class="preview-img" style="background: url(<?= $edit_data['cover_dir'] ?>); background-size:cover; background-position:center center;"></label>
                            </div>
                        </div>
                        <div style="width: 50%;">
                            <div class="input-group" style="margin: 0px;">
                                <div class="modal-div" style="margin:0px">
                                    <label for="genre">Genre:</label>
                                    <input type="text" id="genre" name="genre" value="<?= $edit_data['genre'] ?>"
                                        placeholder="<?= $edit_data['genre'] ?>"><br>
                                </div>
                            </div> 
                            <div class="input-group" style="margin: 0px;">
                                <div class="modal-div"  style="margin:0px">
                                    <label for="category">Category:</label>
                                    <select id="categoy" name="category">
                                        <option value="Education" <?= ($edit_data['category'] == 'Education') ? 'selected' : '' ?>>Education</option>
                                        <option value="Fiction" <?= ($edit_data['category'] == 'Fiction') ? 'selected' : '' ?>>Fiction</option>
                                        <option value="Non-Fiction" <?= ($edit_data['category'] == 'Non-Fiction') ? 'selected' : '' ?>>Non-Fiction</option>
                                    </select><br>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="input-group">
                        <div class="modal-div">
                            <label for="publisher">Publisher:</label>
                            <input type="text" id="publisher" name="publisher" value="<?= $edit_data['publisher'] ?>"
                                placeholder="<?= $edit_data['publisher'] ?>"><br>
                        </div>
                        <div class="modal-div">
                            <label for="publication_date">Publication Date:</label>
                            <input type="date" id="publication_date" name="publication_date"
                                value="<?= $edit_data['publication_date'] ?>"
                                placeholder="<?= $edit_data['publication_date'] ?>"><br>
                        </div>
                    </div>
                    <div class="input-group">
                        <div class="modal-div">
                            <label for="isbn">ISBN:</label>
                            <input type="text" id="isbn" name="isbn" value="<?= $edit_data['isbn'] ?>"
                                placeholder="<?= $edit_data['isbn'] ?>"><br>
                        </div>
                        <div class="modal-div">
                            <label for="status">Status:</label>
                            <select id="status" name="status">
                                <option value="Available" <?= $edit_data['status'] == 'Available' ? 'selected' : '' ?>>
                                    Available</option>
                                <option value="Reserved" <?= $edit_data['status'] == 'Reserved' ? 'selected' : '' ?>>
                                    Reserved</option>
                                <option value="Not Available" <?= $edit_data['status'] == 'Not Available' ? 'selected' : '' ?>>Not Available</option>
                                <option value="Overdue" <?= $edit_data['status'] == 'Overdue' ? 'selected' : '' ?>>Overdue
                                </option>
                            </select><br>
                        </div>
                    </div>
                    <div class="input-group" style="display: flex;flex-direction: column;">
                        <div class="modal-div">
                            <label for="description">Description:</label>
                            <textarea id="description" name="description" rows="3" cols="46"
                                placeholder="<?= $edit_data['description'] ?>"><?= $edit_data['description'] ?></textarea><br>
                        </div>
                    </div>
                    <input type="submit" name="update" value="UPDATE">
                </form>
            </div>
        </div>
        <!-- Right section -->
        <div class="right-section">
            <!-- Pie chart overview -->
            <div class="side">
                <div class="chart" style="display:flex; flex-direction:column;">
                    <div class="circle">
                        <div style="border-radius: 50%;background: white;width: 65%;height: 65%;align-content: center;">
                            <span
                                style="font-size:30px; font-weight:600; color:black;"><?= mysqli_num_rows($books_result) ?></span><br>
                        </div>
                    </div>
                    <div>
                        <h4>OVERVIEW</h4>
                    </div>
                </div>
                <div class="stats">
                    <div class="stats1">
                        <span>LEGEND</span>
                    </div>
                    <div class="stats2">
                        <div class="stat">
                            <div class="stat-container">
                                <div class="color-box available"></div>
                                <span>Available: <?= $status_data['available'] ?> (<?= $available_percentage ?>%)</span>
                            </div>
                            <div class="stat-container">
                                <div class="color-box reserved"></div>
                                <span>Reserved: <?= $status_data['reserved'] ?> (<?= $reserved_percentage ?>%)</span>
                            </div>
                        </div>
                        <div class="stat">
                            <div class="stat-container">
                                <div class="color-box not-available"></div>
                                <span>Not Available: <?= $status_data['not_available'] ?>
                                    (<?= $not_available_percentage ?>%)</span>
                            </div>
                            <div class="stat-container">
                                <div class="color-box overdue"></div>
                                <span>Overdue: <?= $status_data['overdue'] ?> (<?= $overdue_percentage ?>%)</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Add book form -->
            <div class="side" style="margin-bottom: 80px;">
                <h2 style='margin-top:3px'>ADD A BOOK</h2>
                <form method="POST" class="add-form" enctype="multipart/form-data">
                    <div class="input-group">
                        <div>
                            <label for="title">Title:</label>
                            <input type="text" id="title" name="title" placeholder="Title" required>
                        </div>
                        <div>
                            <label for="author">Author:</label>
                            <input type="text" id="author" name="author" placeholder="Author" required>
                        </div>
                    </div>
                    <div class="input-group">
                        <div id="upload-container" class="upload-container">
                            <!-- Book cover input -->
                            <input type="file" id="file-input" name="file-input" style="display: none;" onchange="getImgPreview(event)">
                            <div id="preview" style="display:flex">
                                <div style="margin-right:20px; margin-left: 5px;">
                                    Cover:
                                </div>
                                <label for="file-input" id="preview-img" class="preview-img"></label>
                            </div>
                        </div>
                        <div>
                            <div class="input-group">
                                <div style="margin:0px;">
                                    <label for="genre" required>Genre:</label>
                                    <input type="text" id="genre" name="genre" placeholder="Genre"><br>
                                </div>
                            </div>
                            <div class="input-group" style="width: 97%;">
                                <div style="margin:0px;">
                                    <label for="category" required>Category:</label>
                                    <select id="category" name="category" placeholder="Category">
                                        <option value="Education">Education</option>
                                        <option value="Fiction">Fiction</option>
                                        <option value="Non-Fiction">Non-Fiction</option>
                                    </select><br>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="input-group">
                        <div>
                            <label for="publisher">Publisher:</label>
                            <input type="text" id="publisher" name="publisher" placeholder="Publisher" required><br>
                        </div>
                        <div>
                            <label for="publication_date">Publication Date:</label>
                            <input type="date" id="publication_date" name="publication_date"><br>
                        </div>
                    </div>
                    <div class="input-group">
                        <div>
                            <label for="isbn" required>ISBN:</label>
                            <input type="text" id="isbn" name="isbn" placeholder="X-XX-XXXXXX-X"><br>
                        </div>
                        <div>
                            <label for="status">Status:</label>
                            <select id="status" name="status" required>
                                <option value="Available">Available</option>
                                <option value="Reserved">Reserved</option>
                                <option value="Not Available">Not Available</option>
                                <option value="Overdue">Overdue</option>
                            </select><br>
                        </div>
                    </div>
                    <div class="input-group" style="display: flex;flex-direction: column;">
                        <div>
                            <label for="description" style="margin-left: 5px;">Description:</label>
                            <textarea id="description" name="description" rows="3" cols="46"
                                placeholder=""></textarea><br>
                        </div>
                    </div>
                    <input type="submit" name="add" value="ADD">
                </form>
            </div>
        </div>
    </div>
    
    <!-- Page footer -->
    <?php include ('footer.php'); ?>

    <!-- JS not neccessary but improves QoL -->
    <script>
        function getImgPreview(event) {
            var file = event.target.files[0];

            if (file) {
                var image = URL.createObjectURL(file);
                var imagediv = document.getElementById('preview-img');

                imagediv.style.backgroundImage = `url(${image})`;
            }
        }

        function getImgPreviewEdit(event) {
            var file = event.target.files[0];

            if (file) {
                var image = URL.createObjectURL(file);
                var imagedivedit = document.getElementById('preview-img-edit');

                imagedivedit.style.backgroundImage = `url(${image})`;
            }
        }
    </script>
</body>
</html>