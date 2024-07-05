<?php
include("ini.php");

// Extra pages
$total_books_query = "SELECT COUNT(*) AS total FROM books";
$total_books_result = mysqli_query($conn, $total_books_query);
$total_books_row = mysqli_fetch_assoc($total_books_result);
$total_books = $total_books_row['total'];

$books_per_page = 15;
$total_pages = ceil($total_books / $books_per_page);
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$current_page = max(1, min($current_page, $total_pages));
$offset = ($current_page - 1) * $books_per_page;

$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';


// Get categories and genres
function getCategories($conn) {
    $categories_query = "SELECT DISTINCT category FROM books";
    $categories_result = mysqli_query($conn, $categories_query);

    $categories = array();
    if ($categories_result && mysqli_num_rows($categories_result) > 0) {
        while ($row = mysqli_fetch_assoc($categories_result)) {
            $categories[] = $row['category'];
        }
    }

    return $categories;
}

// Category and genre sorting
$category = isset($_GET['category']) ? $_GET['category'] : '';
$genre = isset($_GET['genre']) ? $_GET['genre'] : '';

$where_conditions = [];
if ($filter == 'available') {
    $where_conditions[] = "status = 'Available'";
}
if (!empty($category)) {
    $where_conditions[] = "category = '" . mysqli_real_escape_string($conn, $category) . "'";
}
if (!empty($genre)) {
    $where_conditions[] = "genre = '" . mysqli_real_escape_string($conn, $genre) . "'";
}

$where_clause = "";
if (!empty($where_conditions)) {
    $where_clause = "WHERE " . implode(' AND ', $where_conditions);
}

$books_query = "SELECT * FROM books $where_clause ORDER BY FIELD(status, 'Available', 'Reserved', 'Not Available', 'Overdue'), title LIMIT $offset, $books_per_page";

// Search
$search_query = "";
$search_results = [];
$search_term = isset($_GET['search']) ? $_GET['search'] : "";

if (!empty($search_term)) {
    $search_conditions = [];
    $search_conditions[] = "(title LIKE '%" . mysqli_real_escape_string($conn, $search_term) . "%' OR author LIKE '%" . mysqli_real_escape_string($conn, $search_term) . "%')";
    
    $search_where_clause = "WHERE " . implode(' AND ', $search_conditions);
    $search_query = "SELECT * FROM books $search_where_clause ORDER BY FIELD(status, 'Available', 'Reserved', 'Not Available', 'Overdue'), title";

    $search_result = mysqli_query($conn, $search_query);

    if ($search_result && mysqli_num_rows($search_result) > 0) {
        while ($row = mysqli_fetch_assoc($search_result)) {
            $search_results[] = $row;
        }
    }
}

$books_result = mysqli_query($conn, $books_query);

$books = array();
if ($books_result && mysqli_num_rows($books_result) > 0) {
    while ($book_row = mysqli_fetch_assoc($books_result)) {
        $books[] = $book_row;
    }
}

// Function to fetch genres based on selected category
function getGenresByCategory($conn, $category) {
    $genres_query = "SELECT DISTINCT genre FROM books WHERE category = '" . mysqli_real_escape_string($conn, $category) . "'";
    $genres_result = mysqli_query($conn, $genres_query);

    $genres = array();
    if ($genres_result && mysqli_num_rows($genres_result) > 0) {
        while ($row = mysqli_fetch_assoc($genres_result)) {
            $genres[] = $row['genre'];
        }
    }
    return $genres;
}

$categories = getCategories($conn);

function getStatusColor($status) {
    switch ($status) {
        case 'Available':
            return '#38bd0f';
        case 'Reserved':
            return '#e7d109';
        case 'Not Available':
            return '#ff7300';
        case 'Overdue':
            return '#db0505';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Library</title>
    <link href="https://fonts.googleapis.com/css2?family=Urbanist:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/library.css">
</head>
<body>
    <?php include('header.php'); ?>

    <div class="wrapper">
        <div class="banner"></div>
        <div class="search-section">
            <div>
                <form method="GET" action="<?= $_SERVER['PHP_SELF'] ?>">
                    <div class="search-bar">
                        <input type="text" name="search" placeholder="Search..." value="<?= htmlspecialchars($search_term); ?>">
                        <input type="hidden" name="filter" value="<?= htmlspecialchars($filter); ?>">
                        <input type="hidden" name="category" value="<?= htmlspecialchars($category); ?>">
                        <input type="hidden" name="genre" value="<?= htmlspecialchars($genre); ?>">
                        <button type="submit" class="search-button">
                            <img src="../img/search.png" alt="Search">
                        </button>
                    </div>
                </form>
            </div>
            <?php if (!empty($search_term)): ?>
                <div class="search-results">
                    <span>Found <b><?= count($search_results); ?></b> <?= count($search_results) == 1 ? "result" : 'results' ?> for <b>'<?= htmlspecialchars($search_term); ?>'</b> :</span>
                    <?php if (count($search_results) > 0): ?>
                        <div class="searched">
                            <?php
                            $search_count = 0;
                            foreach ($search_results as $result):
                                if ($search_count % 10 == 0) {
                                    echo '<div class="search-row">';
                                }
                            ?>
                                <a href="book.php?book=<?= $result['book_id']; ?>" class="search-book">
                                    <div class="search-cover" style="background: url(<?= $result['cover_dir']; ?>); background-size: cover; background-repeat: no-repeat; background-position: center center;"></div>
                                    <div class="search-text">
                                        <span class="search-title"><?= $result['title']; ?></span>
                                        <span class="search-author"><?= $result['author']; ?></span>
                                    </div>
                                </a>
                            <?php
                                $search_count++;
                                if ($search_count % 10 == 0) {
                                    echo '</div>';
                                }
                            endforeach;
                            
                            if ($search_count % 10 != 0) {
                                echo '</div>';
                            }
                            ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="all-section">
            <span class="section-header">OUR CATALOGUE</span>
            <div class="sorting">
                <div>
                    <span style="margin-left: 12px; margin-right: 20px;">
                        <a href="?filter=all<?= !empty($category) ? '&category=' . urlencode($category) : ''; ?><?= !empty($genre) ? '&genre=' . urlencode($genre) : ''; ?><?= !empty($search_term) ? '&search=' . urlencode($search_term) : ''; ?>" class="<?= $filter == 'all' ? 'active' : ''; ?>">ALL</a>
                    </span>
                    <span>
                        <a href="?filter=available<?= !empty($category) ? '&category=' . urlencode($category) : ''; ?><?= !empty($genre) ? '&genre=' . urlencode($genre) : ''; ?><?= !empty($search_term) ? '&search=' . urlencode($search_term) : ''; ?>" class="<?= $filter == 'available' ? 'active' : ''; ?>">AVAILABLE</a>
                    </span>
                </div>
                <div>
                    <form id="filterForm" method="GET" action="<?= $_SERVER['PHP_SELF'] ?>">
                        <label for="category">Filter by:</label>
                        <select name="category" id="category" onchange="updateGenres()">
                            <option value="">Category</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat ?>" <?= $cat == $category ? 'selected' : '' ?>><?= $cat ?></option>
                            <?php endforeach; ?>
                        </select>
                        <select name="genre" id="genre">
                            <option value="">All Genres</option>
                            <?php foreach (getGenresByCategory($conn, $category) as $gen): ?>
                                <option value="<?= $gen ?>" <?= $gen == $genre ? 'selected' : '' ?>><?= $gen ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" onclick="updateBooks()">Filter</button>
                        <button type="button" onclick="clearFilters()">Clear</button>
                        <input type="hidden" name="filter" value="<?= htmlspecialchars($filter); ?>">
                        <input type="hidden" name="search" value="<?= htmlspecialchars($search_term); ?>">
                    </form>
                </div>
            </div>
            <?php if (count($books) > 0): ?>
                <?php
                $count = 0;
                foreach ($books as $book) {
                    if ($count % 5 == 0) {
                        echo '<div class="all-row">';
                    }
                    ?>
                    <a href="book.php?book=<?= $book['book_id']; ?>" class="book-card">
                        <div class="cover" style="background: url(<?= $book['cover_dir']; ?>); background-size: cover; background-repeat: no-repeat; background-position: center center;"></div>
                        <div class="text">
                            <div class="text1">
                                <span class="title"><?= $book['title']; ?></span>
                                <span class="author"><?= $book['author']; ?></span>
                            </div>
                            <div class="status" style="background-color: <?= getStatusColor($book['status']); ?>; box-shadow: 0 0 5px <?= getStatusColor($book['status']); ?>;"><?= $book['status']; ?></div>
                        </div>
                    </a>
                    <?php
                    $count++;
                    if ($count % 5 == 0) {
                        echo '</div>';
                    }
                }

                if ($count % 5 != 0) {
                    echo '</div>';
                }
                ?>
            <?php else: ?>
                <div class="no-books-found">
                    <span>No books found.</span>
                </div>
            <?php endif; ?>

            <div class="pagination">
                <?php if ($current_page > 1): ?>
                    <a href="?page=<?= $current_page - 1; ?><?= !empty($category) ? '&category=' . urlencode($category) : ''; ?><?= !empty($genre) ? '&genre=' . urlencode($genre) : ''; ?><?= !empty($search_term) ? '&search=' . urlencode($search_term) : ''; ?>">&laquo; Previous</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?= $i; ?><?= !empty($category) ? '&category=' . urlencode($category) : ''; ?><?= !empty($genre) ? '&genre=' . urlencode($genre) : ''; ?><?= !empty($search_term) ? '&search=' . urlencode($search_term) : ''; ?>" class="<?= $i == $current_page ? 'active' : ''; ?>"><?= $i; ?></a>
                <?php endfor; ?>

                <?php if ($current_page < $total_pages): ?>
                    <a href="?page=<?= $current_page + 1; ?><?= !empty($category) ? '&category=' . urlencode($category) : ''; ?><?= !empty($genre) ? '&genre=' . urlencode($genre) : ''; ?><?= !empty($search_term) ? '&search=' . urlencode($search_term) : ''; ?>">Next &raquo;</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php include('footer.php'); ?>

    <script>
        function updateGenres() {
            var category = document.getElementById('category').value;
            var genreSelect = document.getElementById('genre');

            genreSelect.innerHTML = '<option value="">All Genres</option>';

            <?php foreach ($categories as $cat): ?>
                if (category === '<?= $cat ?>') {
                    <?php
                        $genres = getGenresByCategory($conn, $cat);
                        foreach ($genres as $gen):
                    ?>
                        genreSelect.innerHTML += '<option value="<?= $gen ?>" <?= $gen == $genre ? 'selected' : '' ?>><?= $gen ?></option>';
                    <?php endforeach; ?>
                }
            <?php endforeach; ?>
        }

        function updateBooks() {
            document.getElementById("filterForm").submit();
        }

        function clearFilters() {
            document.getElementById('category').value = '';
            document.getElementById('genre').innerHTML = '<option value="">All Genres</option>';
            document.getElementById('filterForm').submit();
        }
    </script>
</body>
</html>
