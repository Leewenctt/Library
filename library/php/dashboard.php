<?php
include('ini.php'); // Include admin authentication


// Prevent unauthorized access
if (!isset($_SESSION["user"]) || $row['role'] !== "Admin") {
    header("location: library.php");
    exit;
}

if (isset($_POST['approve_return'])) {
    $reserve_id = intval($_POST['reserve_id']);

    // Start a transaction
    mysqli_begin_transaction($conn);

    try {
        // Update the reservation to mark it as returned and approved
        $approve_query = "UPDATE reservations SET return_approved = 1, status = 'Returned' WHERE reserve_id = $reserve_id";
        if (!mysqli_query($conn, $approve_query)) {
            throw new Exception(mysqli_error($conn));
        }

        // Get the book_id associated with this reservation
        $book_id_query = "SELECT book_id FROM reservations WHERE reserve_id = $reserve_id";
        $book_id_result = mysqli_query($conn, $book_id_query);
        if (!$book_id_result) {
            throw new Exception(mysqli_error($conn));
        }
        $book_id_row = mysqli_fetch_assoc($book_id_result);
        $book_id = intval($book_id_row['book_id']);

        // Update the book status to 'Available'
        $update_book_query = "UPDATE books SET status = 'Available' WHERE book_id = $book_id";
        if (!mysqli_query($conn, $update_book_query)) {
            throw new Exception(mysqli_error($conn));
        }

        // Commit the transaction
        mysqli_commit($conn);
    } catch (Exception $e) {
        // Rollback the transaction in case of an error
        mysqli_rollback($conn);
        die("Error approving return: " . $e->getMessage());
    }
}

// Reject return request
if (isset($_POST['reject_return'])) {
    $reserve_id = intval($_POST['reserve_id']);
    $reject_query = "UPDATE reservations SET return_requested = 0 WHERE reserve_id = $reserve_id";
    mysqli_query($conn, $reject_query);
}

// Fetch return requests
$return_requests_query = "SELECT r.reserve_id, b.title, u.full_name, r.return_requested 
                          FROM reservations r
                          JOIN books b ON r.book_id = b.book_id
                          JOIN users u ON r.user_id = u.user_id
                          WHERE r.return_requested = 1 AND r.return_approved = 0";
$requests_result = mysqli_query($conn, $return_requests_query);

if (!$requests_result) {
    die("Error fetching return requests: " . mysqli_error($conn));
}

// Fetch users
$users_query = "SELECT user_id, username, email, mobile_number FROM users LIMIT 5";
$users_results = mysqli_query($conn, $users_query);

if (!$users_results) {
    die("Error fetching users: " . mysqli_error($conn));
}

// Fetch books
$books_query = "SELECT book_id, title, author, isbn FROM books LIMIT 5";
$books_result = mysqli_query($conn, $books_query);

if (!$books_result) {
    die("Error fetching books: " . mysqli_error($conn));
}

// Fetch total counts
$total_users_query = "SELECT COUNT(*) AS total_users FROM users";
$total_users_result = mysqli_query($conn, $total_users_query);
$total_users_row = mysqli_fetch_assoc($total_users_result);
$total_users = intval($total_users_row['total_users']);

$total_books_query = "SELECT COUNT(*) AS total_books FROM books";
$total_books_result = mysqli_query($conn, $total_books_query);
$total_books_row = mysqli_fetch_assoc($total_books_result);
$total_books = intval($total_books_row['total_books']);

$total_reserved_query = "SELECT COUNT(*) AS total_reserved FROM reservations WHERE status = 'Reserved'";
$total_reserved_result = mysqli_query($conn, $total_reserved_query);
$total_reserved_row = mysqli_fetch_assoc($total_reserved_result);
$total_reserved = intval($total_reserved_row['total_reserved']);

$total_returned_query = "SELECT COUNT(*) AS total_returned FROM reservations WHERE status = 'Returned'";
$total_returned_result = mysqli_query($conn, $total_returned_query);
$total_returned_row = mysqli_fetch_assoc($total_returned_result);
$total_returned = intval($total_returned_row['total_returned']);
?>


<!DOCTYPE html>
<html>

<head>
    <title>Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Urbanist:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/dashboard.css">
</head>

<body>
    <?php include('header.php'); ?>

    <div class="wrapper">
        <div class="nav-links">
            <span>Navigation</span>
            <nav style="display: flex;flex-direction: column;">
                <a href="dashboard.php"><b>Dashboard</b></a>
                <a href="users.php">Manage Users</a>
                <a href="admin.php">Manage Books</a>
            </nav>
        </div>
        <div class="dashboard">
            <div class="summary-cards">
                <div class="card">
                    <h3><?= htmlspecialchars($total_users) ?></h3>
                    <p>Total users</p>
                </div>
                <div class="card">
                    <h3><?= htmlspecialchars($total_books) ?></h3>
                    <p>Total Books</p>
                </div>
                <div class="card">
                    <h3><?= htmlspecialchars($total_reserved) ?></h3>
                    <p>Reserved</p>
                </div>
                <div class="card">
                    <h3><?= htmlspecialchars($total_returned) ?></h3>
                    <p>Returned</p>
                </div>
            </div>

            <div class="lists">
                <div class="book-list">
                    <h2>Books List
                        <button onclick = "window.location.href='admin.php'">View All</button>
                    </h2>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Author</th>
                                <th>ISBN</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($book = mysqli_fetch_assoc($books_result)): ?>
                            <tr>
                                <td><?= htmlspecialchars($book['book_id']) ?></td>
                                <td><?= htmlspecialchars($book['title']) ?></td>
                                <td><?= htmlspecialchars($book['author']) ?></td>
                                <td><?= htmlspecialchars($book['isbn']) ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

                <div class="user-list">
                    <h2>Users List
                    <button onclick = "window.location.href='users.php'">View All</button>
                    </h2>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Email Address</th>
                                <th>Mobile Number</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($user = mysqli_fetch_assoc($users_results)): ?>
                            <tr>
                                <td><?= htmlspecialchars($user['user_id']) ?></td>
                                <td><?= htmlspecialchars($user['username']) ?></td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td><?= htmlspecialchars($user['mobile_number']) ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="return-requests">
                <h2>Return Requests</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Reservation ID</th>
                            <th>Book Title</th>
                            <th>User Name</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($request = mysqli_fetch_assoc($requests_result)): ?>
                        <tr>
                            <td><?= htmlspecialchars($request['reserve_id']) ?></td>
                            <td><?= htmlspecialchars($request['title']) ?></td>
                            <td><?= htmlspecialchars($request['full_name']) ?></td>
                            <td>
                                <div>
                                    <div>
                                        <form action="dashboard.php" method="post" style="display:inline;">
                                            <input type="hidden" name="reserve_id" value="<?= $request['reserve_id'] ?>">
                                            <button type="submit" name="approve_return">Approve</button>
                                        </form>
                                    </div>
                                    <div>
                                        <form action="dashboard.php" method="post" style="display:inline;">
                                            <input type="hidden" name="reserve_id" value="<?= $request['reserve_id'] ?>">
                                            <button type="submit" name="reject_return">Reject</button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php include('footer.php'); ?>
</body>

</html>
