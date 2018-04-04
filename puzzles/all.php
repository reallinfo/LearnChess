<?php
session_start();
include "../include/functions.php";
$sql = "SELECT id FROM `puzzles_to_review`";
$result = mysqli_query($connection,$sql);
if ($result) {
    $unreviewedCount = mysqli_num_rows($result);
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Approved puzzles • LearnChess</title>
        <?php include_once "../include/head.php" ?>
        <link href="../css/puzzles.css" rel="stylesheet" type="text/css">
        <link href="../css/chessground.css" rel="stylesheet" type="text/css">
    </head>
    <body>
        <div class="top">
            <?php include_once "../include/topbar.php" ?>
        </div>
        <div class="page">
            <div class="main <?php if(!$l) { echo 'center'; } ?>">
                <div class="block">
                    <h1 class="block-title">Approved puzzles<?php if($l and isAllowed('puzzle')) { ?>
                        <span class="alternate" <?php if($unreviewedCount > 0) { echo "data-hint=\"There are $unreviewedCount unreviewed puzzles\""; } else { echo 'data-hint="There are no more puzzles to review"'; } ?>>
                            <a class="button <?php if($unreviewedCount > 0) { echo "blue"; } else { echo "disabled"; } ?>" <?php if($unreviewedCount > 0) { echo "href=\"review\""; } ?>><span><i class="fa fa-dashboard"></i> Review new puzzles <?php if($unreviewedCount > 0) { echo "($unreviewedCount)"; } ?></a>
                        </span>
                    <?php } ?>
                    </h1>
                    <div class="accepted-puzzles">
                    <?php
                    $sql = "SELECT * FROM `puzzles_approved` WHERE removed='0' ORDER BY id DESC";
                    $result = mysqli_query($connection,$sql);
                    if (mysqli_num_rows($result) > 0) {
                        while($row = mysqli_fetch_array($result,MYSQLI_ASSOC)) {
                            $fen = $row['fen'];
                            $pID = $row['id'];
                            $authorID = $row['author_id'];
                        ?>
                        <div class="puzzle-container">
                            <a href="<?php echo "view/$pID" ?>" class="puzzle" data-fen="<?php echo $fen ?>"></a>
                            <div class="credits">Created by <br /><?php createUserLink($authorID) ?></div>
                        </div>
                    <?php }
                    } else {
                        echo "<p class=\"nothing-to-see center\">No approved puzzles. <a href=\"new\">Why not make one?</a></p>";
                    } ?>
                    </div>
                </div>
            </div>
            <?php if($l) { ?>
            <div class="right-area">
                <div class="block">
                    <h1 class="block-title">Your puzzles <span class="alternate" data-hint="Create new puzzle"><a href="new" class="button blue"><span><i class="fa fa-plus"></i></span></a></span></h1>
                    <div class="your-puzzles">
                        <?php
                        $myID = $_SESSION['userid'];
                        $sql = "SELECT id FROM `puzzles_to_review` WHERE author_id='$myID'";
                        $result = mysqli_query($connection,$sql);
                        if (mysqli_num_rows($result) > 0) {
                            $rows = mysqli_num_rows($result);
                            echo "<h3>$rows puzzles in review</h3>";
                        } else { ?>
                        <p class="nothing-to-see">No puzzles in review</p>
                        <?php } 
                        $sql2 = "SELECT id FROM `puzzles_approved` WHERE author_id='$myID' AND removed='0' ORDER BY id DESC";
                        $result2 = mysqli_query($connection,$sql2);
                        if (mysqli_num_rows($result2) > 0) {
                            $rows = mysqli_num_rows($result2);
                            echo "<h3>$rows accepted puzzles</h3>";
                            while($row = mysqli_fetch_array($result2,MYSQLI_ASSOC)) {
                                $puzzleID = $row['id'];
                                ?>
                                <a href="view/<?php echo $puzzleID ?>" class="puzzle">Puzzle <?php echo $puzzleID ?></a>
                        <?php }
                        } else { ?>
                        <p class="nothing-to-see">No accepted puzzles</p>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
        <footer>
            <?php include_once "../include/footer.php" ?>
        </footer>
        <script src="../js/global.js"></script>
        <script src="../js/chessground.min.js"></script>
        <script src="../js/loadposition.js"></script>
        <script>
        const puzzlePreviews = document.querySelectorAll('[data-fen]');
        puzzlePreviews.forEach(e=>{
            const fen = e.getAttribute('data-fen');
            loadPosition(e,fen);
        });
        </script>
    </body>
</html>
