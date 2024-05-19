<?php
session_start();
error_reporting(0);
include('includes/config.php');
if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
} else {
    $requestid = $_GET['request'];

    if (isset($_POST['issue'])) {
        $studentid = strtoupper($_POST['studentid']);
        $bookid = $_POST['bookid'];
        $returndate = $_POST['returndate'];

        try {
            // Insert issued book details
            $sql = "INSERT INTO tblissuedbookdetails(StudentID, BookId, ReturnDate) VALUES(:studentid, :bookid, :returndate)";
            $query = $dbh->prepare($sql);
            $query->bindParam(':studentid', $studentid, PDO::PARAM_STR);
            $query->bindParam(':bookid', $bookid, PDO::PARAM_STR);
            $query->bindParam(':returndate', $returndate, PDO::PARAM_STR);
            $query->execute();
            $lastInsertId = $dbh->lastInsertId();

            if ($lastInsertId) {
                // Update request status to 1
                $sqlUpdate = "UPDATE tblrequest SET Status = 1 WHERE id = :requestid";
                $queryUpdate = $dbh->prepare($sqlUpdate);
                $queryUpdate->bindParam(':requestid', $requestid, PDO::PARAM_INT);
                $queryUpdate->execute();

                $_SESSION['msg'] = "Book issued successfully";
                header('location:book-requests.php');
                exit();
            } else {
                $_SESSION['error'] = "Something went wrong. Please try again";
                header('location:book-requests.php');
                exit();
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = "Database error: " . $e->getMessage();
            header('location:book-requests.php');
            exit();
        }
    }


    // Fetch request details
    $sqlRequest = "SELECT * FROM tblrequest WHERE id = :requestid";
    $queryRequest = $dbh->prepare($sqlRequest);
    $queryRequest->bindParam(':requestid', $requestid, PDO::PARAM_INT);
    $queryRequest->execute();
    $request = $queryRequest->fetch(PDO::FETCH_ASSOC);

    if ($request) {
        // Fetch student details
        $sqlStudent = "SELECT StudentId, FullName FROM tblstudents WHERE StudentId = :studentid";
        $queryStudent = $dbh->prepare($sqlStudent);
        $queryStudent->bindParam(':studentid', $request['StudentId'], PDO::PARAM_STR);
        $queryStudent->execute();
        $student = $queryStudent->fetch(PDO::FETCH_ASSOC);

        // Fetch book details
        $sqlBook = "SELECT id, BookName FROM tblbooks WHERE id = :bookid";
        $queryBook = $dbh->prepare($sqlBook);
        $queryBook->bindParam(':bookid', $request['BookId'], PDO::PARAM_INT);
        $queryBook->execute();
        $book = $queryBook->fetch(PDO::FETCH_ASSOC);
    }
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Online Library Management System | Issue a new Book</title>
    <!-- BOOTSTRAP CORE STYLE  -->
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <!-- FONT AWESOME STYLE  -->
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <!-- CUSTOM STYLE  -->
    <link href="assets/css/style.css" rel="stylesheet" />
    <!-- GOOGLE FONT -->
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />
    <script>
        // function for get student name
        function getstudent() {
            $("#loaderIcon").show();
            jQuery.ajax({
                url: "get_student.php",
                data: 'studentid=' + $("#studentid").val(),
                type: "POST",
                success: function(data) {
                    $("#get_student_name").html(data);
                    $("#loaderIcon").hide();
                },
                error: function() {}
            });
        }

        //function for book details
        function getbook() {
            $("#loaderIcon").show();
            jQuery.ajax({
                url: "get_book.php",
                data: 'bookid=' + $("#bookid").val(),
                type: "POST",
                success: function(data) {
                    $("#get_book_name").html(data);
                    $("#loaderIcon").hide();
                },
                error: function() {}
            });
        }
    </script>
    <style type="text/css">
        .others {
            color: red;
        }
    </style>
</head>

<body>
    <!------MENU SECTION START-->
    <?php include('includes/header.php'); ?>
    <!-- MENU SECTION END-->

    <div class="content-wrapper">
        <div class="container">
            <div class="row pad-botm">
                <div class="col-md-12">
                    <h4 class="header-line">Issue a New Book</h4>
                </div>
            </div>
            <div class="row">
                <div class="col-md-10 col-sm-6 col-xs-12 col-md-offset-1">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            Issue a New Book
                        </div>
                        <div class="panel-body">
                            <form role="form" method="post">
                                <div class="form-group">
                                    <label>Student ID<span style="color:red;">*</span></label>
                                    <input  type="hidden" name="studentid" id="studentid" class="form-control" value="<?php echo htmlentities($student['StudentId']); ?>"  readonly>
                                    <input type="text"  class="form-control" value="<?php echo htmlentities($student['FullName']); ?>"  readonly>
                                </div>
                              
                                <div class="form-group">
                                    <label>Book Title<span style="color:red;">*</span></label>
                                    <input type="hidden" name="bookid" id="bookid" class="form-control" value="<?php echo htmlentities($book['id']); ?>" readonly>
                                    <input type="text" class="form-control" value="<?php echo htmlentities($book['BookName']); ?>" readonly>
                                </div>
                               
                                <div class="form-group">
                                    <label>Return Date<span style="color:red;">*</span></label>
                                    <input type="date" class="form-control" name="returndate" id="returndate" required>
                                </div>
                                <button type="submit" name="issue" id="submit" class="btn btn-info">Issue Book</button>
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
