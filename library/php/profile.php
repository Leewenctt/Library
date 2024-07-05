<?php
include ('ini.php');

if (!isset($_SESSION['user'])) {
    header('location: library.php');
    exit();
}

if (isset($_POST['request_return'])) {
    $book_id = intval($_POST['book_id']);
    $user_id = $row['user_id'];

    $update_query = "UPDATE reservations SET return_requested = 1 WHERE book_id = $book_id AND user_id = $user_id AND status = 'Reserved'";
    $result = mysqli_query($conn, $update_query);

    if ($result) {
        echo "Return request submitted successfully.";
    } else {
        echo "Error submitting return request: " . mysqli_error($conn);
    }
}

$reservations_query = "SELECT b.title, b.author, b.genre, b.category, b.cover_dir, r.pickup_date, r.return_date, r.requested_at, r.duration, r.book_id, r.return_requested
                      FROM reservations r
                      JOIN books b ON r.book_id = b.book_id
                      WHERE r.user_id = {$row['user_id']}
                      AND r.status = 'Reserved'";

$reservations_result = mysqli_query($conn, $reservations_query);

if (!$reservations_result) {
    die("Error fetching reserved books: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Profile Page</title>
    <link href="https://fonts.googleapis.com/css2?family=Urbanist:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="../css/profile.css">
</head>
<body>
    <?php include ('header.php'); ?>

    <div class="profile_header">
        <div class="banner"></div>
        <form id="profile-pic-form" method="POST" enctype="multipart/form-data">
            <input type="file" id="profile-pic-input" name="profile_pic" style="display: none;"
                onchange="document.getElementById('upload').click();">
            <label for="profile-pic-input" class="profile-pic"
                style="background-image: url('<?php echo $row['profile_pic']; ?>');"></label>
            <button type="submit" name="upload" id="upload" style="display: none;"></button>
        </form>
        <h1><?php echo $row['full_name']; ?></h1>
        <span class="header_text"><?php echo $row['email']; ?></span>
    </div>

    <div class="profile_contents">
        <div class="wrapper">
            <h2>Reserved Books:</h2>
            <div class="reserved-container">
                <?php while ($book = mysqli_fetch_assoc($reservations_result)): ?>
                <div class="reserved-card">
                    <div class="cover" style="background: url(<?= $book['cover_dir'] ?>); background-size: cover; background-position: center center; background-repeat: no-repeat;"></div>
                    <div class="info">
                        <div class="divv">
                            <div class="details">
                                <span><b>Title:  </b><?php echo htmlspecialchars($book['title']); ?></span>
                                <span><b>Author:  </b><?php echo htmlspecialchars($book['author']); ?></span>
                                <div class="genres">
                                    <span><b>Tags: </b><?php echo htmlspecialchars($book['category']); ?>,</span>
                                    <span><?php echo htmlspecialchars($book['genre']); ?></span>
                                </div>
                            </div>
                            <div class="reservation-details">
                                <span><b>Requested On:  </b><?php echo htmlspecialchars($book['requested_at']); ?></span>
                                <span><b>Pickup Date:  </b><?php echo htmlspecialchars(date('Y-m-d', strtotime($book['pickup_date']))); ?></span>
                                <span><b>Return Date:  </b><?php echo htmlspecialchars(date('Y-m-d', strtotime($book['return_date']))); ?></span>
                            </div>
                        </div>
                        <div class="buttons">
                            <button onclick="location.href='book.php?book=<?= $book['book_id'] ?>'">View</button>
                            <?php if ($book['return_requested'] == 1): ?>
                                <button type="button" class="disabled-button" style="background: #ccc; cursor: default; padding: 10px 30px;" >Pending</button>
                            <?php else: ?>
                                <form method="post" style="display:inline;">
                                    <input type="hidden" name="book_id" value="<?= $book['book_id'] ?>">
                                    <button type="submit" name="request_return" onclick="return confirm('Request return for <?= $book['title'] ?> by <?= $book['author'] ?>?')">Return</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>

    <?php include ('footer.php'); ?>
</body>
</html>
