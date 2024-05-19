<?php
session_start();
error_reporting(0);
include('includes/config.php');
if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
} else {
    if (isset($_POST['return'])) {
        $rid = intval($_GET['rid']);
        $rstatus = 1;
        $sql = "UPDATE tblissuedbookdetails SET ReturnStatus=:rstatus WHERE id=:rid";
        $query = $dbh->prepare($sql);
        $query->bindParam(':rid', $rid, PDO::PARAM_INT);
        $query->bindParam(':rstatus', $rstatus, PDO::PARAM_INT);
        $query->execute();
    
        $checkSql = "SELECT * FROM tblissuedbookdetails WHERE id=:rid";
        $checkQuery = $dbh->prepare($checkSql);
        $checkQuery->bindParam(':rid', $rid, PDO::PARAM_INT);
        $checkQuery->execute();
        $row = $checkQuery->fetch(PDO::FETCH_ASSOC); // Fetch the row
        $bookid = $row['BookId']; // Access BookId from the fetched row
    echo $bookid;
        $sql1 = "UPDATE tblbooks SET status='0' WHERE id=:bookid"; // Corrected SQL query
        $query1 = $dbh->prepare($sql1);
        $query1->bindParam(':bookid', $bookid, PDO::PARAM_INT); // Bind BookId as parameter
        $query1->execute();
    
        $_SESSION['msg'] = "Book Returned successfully";
        header('location: manage-issued-books.php');
    }
    
    
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Online Library Management System | Issued Book Details</title>
    <!-- BOOTSTRAP CORE STYLE  -->
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <!-- FONT AWESOME STYLE  -->
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
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
                    <h4 class="header-line">Issued Book Details</h4>
                </div>
            </div>
            <div class="row">
                <div class="col-md-10 col-sm-6 col-xs-12 col-md-offset-1">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            Issued Book Details
                        </div>
                        <div class="panel-body">
                            <form role="form" method="post">
                                <?php
                                $rid = intval($_GET['rid']);
                                $sql = "SELECT 
                                            tblstudents.FullName,
                                            tblbooks.BookName,
                                            tblbooks.ISBNNumber,
                                            tblissuedbookdetails.IssuesDate,
                                            tblissuedbookdetails.ReturnDate,
                                            tblissuedbookdetails.id as rid,
                                            tblissuedbookdetails.ReturnStatus 
                                        FROM 
                                            tblissuedbookdetails 
                                        JOIN 
                                            tblstudents 
                                        ON 
                                            tblstudents.StudentId = tblissuedbookdetails.StudentId 
                                        JOIN 
                                            tblbooks 
                                        ON 
                                            tblbooks.id = tblissuedbookdetails.BookId 
                                        WHERE 
                                            tblissuedbookdetails.id = :rid";
                                $query = $dbh->prepare($sql);
                                $query->bindParam(':rid', $rid, PDO::PARAM_INT);
                                $query->execute();
                                $results = $query->fetchAll(PDO::FETCH_OBJ);
                                if ($query->rowCount() > 0) {
                                    foreach ($results as $result) {
                                ?>

                                        <div class="form-group">
                                            <label>Student Name :</label>
                                            <?php echo htmlentities($result->FullName); ?>
                                        </div>

                                        <div class="form-group">
                                            <label>Book Name :</label>
                                            <?php echo htmlentities($result->BookName); ?>
                                        </div>

                                        <div class="form-group">
                                            <label>ISBN :</label>
                                            <?php echo htmlentities($result->ISBNNumber); ?>
                                        </div>

                                        <div class="form-group">
                                            <label>Book Issued Date :</label>
                                            <?php echo date('Y-m-d', strtotime($result->IssuesDate)); ?>
                                        </div>

                                        <div class="form-group">
                                            <label>Book Returned Date :</label>
                                            <?php if ($result->ReturnDate == "") {
                                                echo htmlentities("Not Returned Yet");
                                            } else {
                                                echo date('Y-m-d', strtotime($result->ReturnDate));
                                            }
                                            ?>
                                        </div>

                                        <?php if ($result->ReturnStatus == 0) { ?>
                                            <button type="submit" name="return" id="submit" class="btn btn-info">Return Book</button>
                                        <?php } ?>

                                <?php
                                    }
                                }
                                ?>
                            </form>
                        </div>
                    </div>
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
    <!-- CUSTOM SCRIPTS  -->
    <script src="assets/js/custom.js"></script>
</body>

</html>
<?php } ?>
