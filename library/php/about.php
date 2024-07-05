<?php
include ('ini.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>About</title>
    <link href="https://fonts.googleapis.com/css2?family=Urbanist:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="../css/about.css">
</head>

<body>
    <?php include ('header.php'); ?>

    <div class="container">
        <div class="section">
            <h1>ABOUT US</h1>
            <p style="margin-bottom: 20px;">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam feugiat urna
                sed purus malesuada, sed
                vestibulum dui vestibulum. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere
                cubilia curae; In sed nisi eu augue cursus auctor. Sed vestibulum, risus id scelerisque convallis,
                lectus velit iaculis est, a consectetur sapien lectus eget dui. Nam vel ipsum tempor, tincidunt ex a,
                molestie justo. </p>
            <p style="margin-bottom: 20px;">Etiam rhoncus, ligula eget rutrum laoreet, est sapien pulvinar odio, ac
                blandit metus erat eget metus.
                Quisque non commodo risus. Cras maximus est a erat tincidunt, nec efficitur tortor condimentum. Sed in
                efficitur dui. Nulla facilisi. Morbi fringilla velit risus, at elementum eros tincidunt ac. Suspendisse
                lacinia interdum nibh auctor lobortis. Proin et turpis vel mi commodo sollicitudin vel in velit.
                Vestibulum sodales urna eget nulla pellentesque scelerisque. Ut accumsan vehicula odio sit amet luctus.
                Sed vestibulum diam non dolor posuere, ut elementum nulla feugiat.</p>
            <img src="../img/about_banner1.jpg" alt="Image 1" style="box-shadow: 0 1px 7px rgba(64, 64, 64, .7);">
        </div>
        <div class="section">
            <h1 style="margin-bottom: 30px;">OUR VISION</h1>
            <div style="display: flex;flex-direction: row;justify-content: space-between;">
                <div class="banner"></div>
                <p style="width:50%;">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam feugiat urna sed
                    purus malesuada, sed vestibulum dui vestibulum. Vestibulum ante ipsum primis in faucibus orci luctus
                    et ultrices posuere
                    cubilia curae; In sed nisi eu augue cursus auctor. Sed vestibulum, risus id scelerisque convallis,
                    lectus velit iaculis est, a consectetur sapien lectus eget dui. Nam vel ipsum tempor, tincidunt ex
                    a, molestie justo. Proin et turpis vel mi commodo sollicitudin vel in velit. Vestibulum sodales urna
                    eget nulla pellentesque scelerisque. Ut accumsan vehicula odio sit amet luctus. Sed vestibulum diam
                    non dolor posuere, ut elementum nulla feugiat.</p>
            </div>
            <div style="display: flex;flex-direction: row;justify-content: space-between;margin-top: 20px;">
                <p style="width:50%;">Etiam rhoncus, ligula eget rutrum laoreet, est sapien pulvinar odio, ac blandit
                    metus erat eget
                    metus. Quisque non commodo risus. Cras maximus est a erat tincidunt, nec efficitur tortor
                    condimentum. Sed in efficitur dui. Nulla facilisi. Morbi fringilla velit risus, at elementum eros
                    tincidunt ac. Suspendisse lacinia interdum nibh auctor lobortis. Proin et turpis vel mi commodo
                    sollicitudin vel in velit. Vestibulum sodales urna eget nulla pellentesque scelerisque. Ut accumsan
                    vehicula odio sit amet luctus. Sed vestibulum diam non dolor posuere, ut elementum nulla feugiat.
                    Duis fringilla, augue quis consectetur laoreet, turpis quam lobortis elit.</p>
                <div class="banner2"></div>
            </div>
            <div class="section" style="margin-top: 140px;">
                <h1>MEET THE TEAM</h1>
                <div style="display: flex;flex-direction: row;justify-content: space-around;margin-top: 30px;">
                    <div>
                        <div class="staff" id="staff1"></div>
                        <b>Maria Nicole Maglalang</b>
                    </div>
                    <div>
                        <div class="staff" id="staff2"></div>
                        <b>Kim Vincent Salangsang</b>
                    </div>
                    <div>
                        <div class="staff" id="staff3"></div>
                        <b>Joannah Ann Guiterrez</b>
                    </div>
                </div>
                <p style="margin-top: 30px;">Sed interdum, libero vel pellentesque pharetra, leo augue fermentum lorem,
                    non ultricies nisl sem eu
                    urna. Curabitur nec placerat dolor. Phasellus bibendum mauris vitae risus convallis consectetur.
                    Nullam ac erat id sapien consequat commodo. Mauris et varius magna, in hendrerit elit. Fusce ut eros
                    quis mauris tincidunt finibus. In bibendum quam sit amet lobortis venenatis. Fusce lobortis
                    convallis eros, sit amet malesuada ante volutpat nec. Nam id urna ultricies, bibendum sem eu,
                    gravida metus. Curabitur at enim id urna blandit hendrerit. Ut eget lectus in metus efficitur
                    maximus in a nulla. Donec sed sapien ut risus venenatis viverra.</p>
            </div>
        </div>
    </div>
        <?php include ('footer.php'); ?>
</body>

</html>