<?php
include ('ini.php');

$todayDate = date('Y-m-d');

$returnDateDefault = date('Y-m-d', strtotime($todayDate . ' + 1 day'));

function markBookAsReserved($conn, $book_id, $reservation_id) {
    $update_query = "UPDATE books SET status = 'Reserved' WHERE book_id = $book_id";
    mysqli_query($conn, $update_query);
    
    if (mysqli_affected_rows($conn) > 0) {
        return true;
    }
    return false;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pickup_date'], $_POST['duration'], $_POST['return_date'], $_POST['book_id'])) {
    $pickup_date = mysqli_real_escape_string($conn, $_POST['pickup_date']);
    $duration = (int)$_POST['duration'];
    $return_date = mysqli_real_escape_string($conn, $_POST['return_date']);
    $book_id = (int)$_POST['book_id'];
    
    $insert_query = "INSERT INTO reservations (pickup_date, duration, return_date, status, approval_status, requested_at, user_id, book_id)
                    VALUES ('$pickup_date', $duration, '$return_date', 'Reserved', 'Approved', NOW(), {$row['user_id']}, $book_id)";
    mysqli_query($conn, $insert_query);

    if (mysqli_affected_rows($conn) > 0) {
        $reservation_id = mysqli_insert_id($conn);

        if (markBookAsReserved($conn, $book_id, $reservation_id)) {
            echo '<script>alert("Reservation Requested Successfully!");</script>';
        } else {
            echo '<script>alert("Failed to update book status.");</script>';
        }
    } else {
        echo '<script>alert("Failed to request reservation.");</script>';
    }
}


if (isset($_GET['book'])) {
    $book_id = (int)$_GET['book'];

    $book_query = "SELECT * FROM books WHERE book_id = $book_id";
    $book_result = mysqli_query($conn, $book_query);

    if ($book_result && mysqli_num_rows($book_result) > 0) {
        $book = mysqli_fetch_assoc($book_result);
    } else {
        header("location: library.php");
        exit;
    }
} else {
    header("location: library.php");
    exit;
}

$buttonDisabled = $book['status'] != 'Available';

$genre = $book['genre'];
$category = $book['category'];
$recommendations_query = "SELECT * FROM books WHERE (genre = '$genre' OR category = '$category') AND book_id != $book_id LIMIT 6";
$recommendations_result = mysqli_query($conn, $recommendations_query);

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
        default:
            return '#000000';
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($book['title']); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Urbanist:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/book.css">
</head>
<body>
    <!-- Page header -->
    <?php include('header.php'); ?>

    <div class="wrapper">
        <div class="container">
            <!-- Book info section -->
            <div class="book-section">
                <div class="cover" style="background: url(<?= $book['cover_dir']; ?>); background-size: cover; background-repeat: no-repeat; background-position: center center;"></div>
                <div class="info">
                    <span id="title"><?= htmlspecialchars($book['title']) ?></span>
                    <span id="author"><?= htmlspecialchars($book['author']) ?></span>

                    <span class="additional">Genres:</span>
                    <div class="genres">
                        <div class="tags">
                            <div class="tag"><?= htmlspecialchars($book['category']) ?></div>
                            <div class="tag"><?= htmlspecialchars($book['genre']) ?></div>
                        </div>
                    </div>
                    
                    <span class="additional2">Description:</span>
                    <span id="description-book"><?= htmlspecialchars($book['description']) ?></span>

                    <span class="additional">Additional Information:</span>
                    <div class="add-info">
                        <div>
                            <span class="margin">Publisher: <?= htmlspecialchars($book['publisher']) ?></span>
                            <span class="margin">Publication Date: <?= htmlspecialchars($book['publication_date']) ?></span>
                        </div>
                        <div>
                            <span class="margin">ISBN: <?= htmlspecialchars($book['isbn']) ?></span>
                            <span class="margin" style="color: white; padding: 2px 6px; width: fit-content; border-radius: 15px; background: <?= getStatusColor($book['status']) ?>; box-shadow: 0 0 5px <?= getStatusColor($book['status']) ?>;"><?= htmlspecialchars($book['status']) ?></span>
                        </div>
                    </div>
                    <div class="btns">
                        <a href="https://www.google.com/search?q=<?= urlencode($book['title']); ?>+by+<?= urlencode($book['author']); ?>" target="_blank">Search Online</a>
                        <label for="modal-toggle" class="modal-button <?= $buttonDisabled ? 'disabled' : '' ?>" <?= $buttonDisabled ? 'disabled' : '' ?>>Reserve</label>
                    </div>
                </div>
            </div>
            <!-- Recommendations section -->
            <div class="recommendations">
                <div class="header-text">
                    <span><b>You might also like:</b></span>
                </div>
                <div class="row">
                    <?php if ($recommendations_result && mysqli_num_rows($recommendations_result) > 0): ?>
                        <?php while ($rec_book = mysqli_fetch_assoc($recommendations_result)): ?>
                            <a href="book.php?book=<?= $rec_book['book_id']; ?>" class="book">
                                <div class="recc-cover" style="background: url(<?= $rec_book['cover_dir']; ?>); background-size: cover; background-repeat: no-repeat; background-position: center center;"></div>
                                <div class="book-label">
                                    <span class="titl"><?= htmlspecialchars($rec_book['title']); ?></span>
                                    <span class="descrip"><?= htmlspecialchars($rec_book['author']); ?></span>
                                </div>
                            </a>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <span>No recommendations available.</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal for reservation -->
    <input type="checkbox" id="modal-toggle" style="display: none;">
    <div class="modal">
        <!-- If logged in -->
        <?php if (isset($_SESSION['user'])): ?>
            <div class="modal-content">
                <div class="notice2">
                    <span class="notice-header">Reservation</span>
                    <span class="close" onclick="document.getElementById('modal-toggle').checked = false;">&times;</span>
                </div>
                
                <form action="" method="POST">
                    <input type="hidden" name="book_id" value="<?= $book['book_id'] ?>">
                    <div class="section">
                        <span>User Information:</span>
                        <div class="form-group">
                            <input type="text" name="full_name" class="unchange" value="<?= htmlspecialchars($row['full_name']) ?>" readonly>
                            <input type="text" name="username" class="unchange" value="<?= htmlspecialchars($row['username']) ?>" readonly>
                        </div>
                        <div class="form-group">
                            <input type="email" name="email" class="unchange" value="<?= htmlspecialchars($row['email']) ?>" readonly>
                            <input type="text" name="mobile" class="unchange" value="<?= htmlspecialchars($row['mobile_number']) ?>" readonly>
                        </div>
                    </div>
                    <div class="section">
                        <span>Reservation Details:</span>
                        <div class="reservation-details">
                            <div class="book-cover">
                                <img src="<?= htmlspecialchars($book['cover_dir']) ?>" alt="Book Cover">
                            </div>
                            <div class="book-info">
                                <span style="margin-top:0;"><?= htmlspecialchars($book['title']) ?></span>
                                <span><?= htmlspecialchars($book['author']) ?></span>
                                <span><?= htmlspecialchars($book['isbn']) ?></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <div style="display:flex; flex-direction:column; width: 50%">
                                <label for="pickup_date">Pickup Date:</label>
                                <input type="date" name="pickup_date" id="pickup_date" placeholder="Pickup Date" value="<?= $todayDate ?>" onchange="updateReturnDate()" oninput="updateReturnDate()">
                            </div>
                            <div style="display:flex; flex-direction:column; width: 50%">
                                <label for="return_date">Return Date:</label>
                                <input type="date" name="return_date" id="return_date" placeholder="Return Date" value="<?= $returnDateDefault ?>" readonly>
                            </div>
                        </div>
                        <div class="form-group duration">
                            <label for="duration">Duration:</label>
                            <input type="range" name="duration" min="1" max="7" value="1" id="duration" onchange="updateReturnDate()" oninput="updateReturnDate()">
                            <span id="duration-value">1 days</span>
                        </div>
                    </div>
                    <button type="submit">Reserve</button>
                </form>
            </div>
        <!-- If not logged in -->
        <?php else: ?>
            <div class="modal-content2">
                <div class="notice2">
                    <span class="notice-header">Notice</span>
                    <span class="close" onclick="document.getElementById('modal-toggle').checked = false;">&times;</span>
                </div>
                <span>You must be logged in to make a reservation.</span>
            </div>
        <?php endif; ?>
    </div>
    <!-- Page footer -->
    <?php include('footer.php');?>
    <!-- JS not required but improves QoL-->
    <script>
        function updateReturnDate() {
            var pickupDate = document.getElementById('pickup_date').value;
            var duration = parseInt(document.getElementById('duration').value);
            
            if (pickupDate && !isNaN(duration)) {
                var date = new Date(pickupDate);
                date.setDate(date.getDate() + duration);
                
                var returnDateInput = document.getElementById('return_date');
                returnDateInput.value = date.toISOString().slice(0, 10);

                var durationText = document.getElementById('duration-value');
                durationText.textContent = duration + ' days';
            }
        }
        
        window.onload = function() {
            updateReturnDate();
            
            var returnDateInput = document.getElementById('return_date');
            returnDateInput.value = '<?= $returnDateDefault ?>';
        };
    </script>
</body>
</html>
