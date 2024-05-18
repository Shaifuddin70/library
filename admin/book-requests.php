<?php
session_start();
include('includes/config.php');
if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
    exit(); // Ensure the script stops execution after redirection
} else {
    // Reset session variables after displaying messages
    $error = isset($_SESSION['error']) ? $_SESSION['error'] : "";
    $msg = isset($_SESSION['msg']) ? $_SESSION['msg'] : "";
    $delmsg = isset($_SESSION['delmsg']) ? $_SESSION['delmsg'] : "";
    $_SESSION['error'] = "";
    $_SESSION['msg'] = "";
    $_SESSION['delmsg'] = "";
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Online Library Management System | Manage Issued Books</title>
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
    <!-- MENU SECTION START-->
    <?php include('includes/header.php'); ?>
    <!-- MENU SECTION END-->
    <div class="content-wrapper">
        <div class="container">
            <div class="row pad-botm">
                <div class="col-md-12">
                    <h4 class="header-line">Book Requests</h4>
                </div>
                <div class="row">
                    <?php if (!empty($error)) { ?>
                    <div class="col-md-6">
                        <div class="alert alert-danger">
                            <strong>Error:</strong> <?php echo htmlentities($error); ?>
                        </div>
                    </div>
                    <?php } ?>
                    <?php if (!empty($msg)) { ?>
                    <div class="col-md-6">
                        <div class="alert alert-success">
                            <strong>Success:</strong> <?php echo htmlentities($msg); ?>
                        </div>
                    </div>
                    <?php } ?>
                    <?php if (!empty($delmsg)) { ?>
                    <div class="col-md-6">
                        <div class="alert alert-success">
                            <strong>Success:</strong> <?php echo htmlentities($delmsg); ?>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <!-- Advanced Tables -->
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Book Requests
                        </div>
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Student Name</th>
                                            <th>Book Name</th>
                                            <th>ISBN</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $sql = "SELECT r.id, s.FullName AS StudentName, b.BookName, b.ISBNNumber
                                                FROM tblrequest r
                                                INNER JOIN tblstudents s ON r.StudentId = s.StudentId
                                                INNER JOIN tblbooks b ON r.BookId = b.id";
                                        $query = $dbh->prepare($sql);
                                        $query->execute();
                                        $results = $query->fetchAll(PDO::FETCH_OBJ);
                                        $cnt = 1;
                                        if ($query->rowCount() > 0) {
                                            foreach ($results as $result) {
                                        ?>
                                        <tr class="odd gradeX">
                                            <td class="center"><?php echo htmlentities($cnt); ?></td>
                                            <td class="center"><?php echo htmlentities($result->StudentName); ?></td>
                                            <td class="center"><?php echo htmlentities($result->BookName); ?></td>
                                            <td class="center"><?php echo htmlentities($result->ISBNNumber); ?></td>
                                            <td class="center">
                                                <a href="request-book.php?request=<?php echo htmlentities($result->id); ?>" class="btn btn-primary">Issue</a>
                                            </td>
                                        </tr>
                                        <?php $cnt = $cnt + 1;
                                            }
                                        } ?>
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
