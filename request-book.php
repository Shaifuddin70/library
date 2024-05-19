<?php
session_start();
include('includes/config.php');
if (strlen($_SESSION['login']) == 0) {
    header('location:index.php');
    exit();
} else {
    if (isset($_GET['request'])) {
        $bookId = $_GET['request'];
        $studentId = $_SESSION['stdid'];

        // Check if the book is already requested by the student
        $checkSql = "SELECT * FROM tblrequest WHERE StudentId=:studentId AND BookId=:bookId AND status IS NULL";
        $checkQuery = $dbh->prepare($checkSql);
        $checkQuery->bindParam(':studentId', $studentId, PDO::PARAM_STR);
        $checkQuery->bindParam(':bookId', $bookId, PDO::PARAM_STR);
        $checkQuery->execute();

        // Check if the book is already issued to the student
        $checkIssueSql = "SELECT * FROM tblissuedbookdetails WHERE StudentID=:studentId AND BookId=:bookId AND ReturnStatus IS NULL";
        $checkIssueQuery = $dbh->prepare($checkIssueSql);
        $checkIssueQuery->bindParam(':studentId', $studentId, PDO::PARAM_STR);
        $checkIssueQuery->bindParam(':bookId', $bookId, PDO::PARAM_STR);
        $checkIssueQuery->execute();

        if ($checkQuery->rowCount() == 0 && $checkIssueQuery->rowCount() == 0) {
            $sql = "INSERT INTO tblrequest(StudentId, BookId) VALUES(:studentId, :bookId)";
            $query = $dbh->prepare($sql);
            $query->bindParam(':studentId', $studentId, PDO::PARAM_STR);
            $query->bindParam(':bookId', $bookId, PDO::PARAM_STR);
            $query->execute();
            $_SESSION['reqmsg'] = "Book requested successfully.";
        } else {
            $_SESSION['reqmsg'] = "You have already requested this book Or Issued to you";
        }

        header('location:request-book.php');
        exit();
    }

?>
    <!DOCTYPE html>
    <html xmlns="http://www.w3.org/1999/xhtml">

    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Online Library Management System | Request New Books</title>
        <!-- BOOTSTRAP CORE STYLE  -->
        <link href="assets/css/bootstrap.css" rel="stylesheet" />
        <!-- FONT AWESOME STYLE  -->
        <link href="assets/css/font-awesome.css" rel="stylesheet" />
        <!-- DATATABLE STYLE  -->
        <link href="assets/js/dataTables/dataTables.bootstrap.css" rel="stylesheet" />
        <!-- CUSTOM STYLE  -->
        <link href="assets/css/style.css" rel="stylesheet" />
        <!-- GOOGLE FONT -->
        <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />
    </head>

    <body>
        <!------MENU SECTION START-->
        <?php include('includes/header.php'); ?>
        <!-- MENU SECTION END-->
        <div class="content-wrapper">
            <div class="container">
                <div class="row pad-botm">
                    <div class="col-md-12">
                        <h4 class="header-line">Request New Books</h4>
                    </div>
                </div>

                <?php if (isset($_SESSION['reqmsg'])) { ?>
                    <div class="alert alert-success">
                        <strong>Success!</strong> <?php echo htmlentities($_SESSION['reqmsg']); ?>
                    </div>
                    <?php unset($_SESSION['reqmsg']); ?>
                <?php } ?>

                <div class="row">
                    <div class="col-md-12">
                        <!-- Advanced Tables -->
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                Books Listing
                            </div>
                            <div class="panel-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Book Name</th>
                                                <th>Category</th>
                                                <th>Author</th>
                                                <th>ISBN</th>
                                                <th>Status</th>

                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $sql = "SELECT tblbooks.BookName, tblcategory.CategoryName, tblauthors.AuthorName, tblbooks.ISBNNumber, tblbooks.status, tblbooks.id as bookid FROM tblbooks JOIN tblcategory ON tblcategory.id = tblbooks.CatId JOIN tblauthors ON tblauthors.id = tblbooks.AuthorId";
                                            $query = $dbh->prepare($sql);
                                            $query->execute();
                                            $results = $query->fetchAll(PDO::FETCH_OBJ);
                                            $cnt = 1;
                                            if ($query->rowCount() > 0) {
                                                foreach ($results as $result) { ?>
                                                    <tr class="odd gradeX">
                                                        <td class="center"><?php echo htmlentities($cnt); ?></td>
                                                        <td class="center"><?php echo htmlentities($result->BookName); ?></td>
                                                        <td class="center"><?php echo htmlentities($result->CategoryName); ?></td>
                                                        <td class="center"><?php echo htmlentities($result->AuthorName); ?></td>
                                                        <td class="center"><?php echo htmlentities($result->ISBNNumber); ?></td>
                                                        <td class="center">
                                                            <?php
                                                            if ($result->status == 0) {
                                                                echo htmlentities("Available");
                                                            } else {
                                                                echo htmlentities("Not Available");
                                                            }
                                                            ?>
                                                        </td>
                                                        <td class="center">
                                                            <?php if ($result->status == 0) : ?>
                                                                <a href="request-book.php?request=<?php echo htmlentities($result->bookid); ?>" class="btn btn-primary">Request</a>
                                                            <?php else : ?>
                                                                <button class="btn btn-primary" disabled>Request</button>
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>
                                            <?php
                                                    $cnt++;
                                                }
                                            }
                                            ?>


                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!--End Advanced Tables -->
                    </div>
                </div>
            </div>
        </div>

        <!-- CONTENT-WRAPPER SECTION END-->
        <?php include('includes/footer.php'); ?>
        <!-- FOOTER SECTION END-->
        <!-- JAVASCRIPT FILES PLACED AT THE BOTTOM TO REDUCE THE LOADING TIME  -->
        <!-- CORE JQUERY  -->
        <script src="assets/js/jquery-1.10.2.js"></script>
        <!-- BOOTSTRAP SCRIPTS  -->
        <script src="assets/js/bootstrap.js"></script>
        <!-- DATATABLE SCRIPTS  -->
        <script src="assets/js/dataTables/jquery.dataTables.js"></script>
        <script src="assets/js/dataTables/dataTables.bootstrap.js"></script>
        <!-- CUSTOM SCRIPTS  -->
        <script src="assets/js/custom.js"></script>
    </body>

    </html>
<?php } ?>