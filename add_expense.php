<?php
include("session.php"); // Make sure session.php sets $userid, $username, $useremail, $userprofile

$update = false;
$del = false;
$expenseamount = "";
$expensedate = date("Y-m-d");
$expensecategory = "Entertainment";

// ---------------- ADD EXPENSE ----------------
if (isset($_POST['add'])) {
    $expenseamount = $_POST['expenseamount'];
    $expensedate = $_POST['expensedate'];
    $expensecategory = $_POST['expensecategory'];

    if (!isset($userid) || empty($userid)) {
        die("Error: User not logged in.");
    }

    $expenses = "INSERT INTO expenses (user_id, expense, expensedate, expensecategory)
                 VALUES ('$userid', '$expenseamount', '$expensedate', '$expensecategory')";

    if (!mysqli_query($conn, $expenses)) {
        die("MySQL Error: " . mysqli_error($conn));
    }

    header('Location: add_expense.php');
    exit();
}

// ---------------- UPDATE EXPENSE ----------------
if (isset($_POST['update'])) {
    $id = $_GET['edit'];
    $expenseamount = $_POST['expenseamount'];
    $expensedate = $_POST['expensedate'];
    $expensecategory = $_POST['expensecategory'];

    $sql = "UPDATE expenses 
            SET expense='$expenseamount', expensedate='$expensedate', expensecategory='$expensecategory' 
            WHERE user_id='$userid' AND expense_id='$id'";

    if (!mysqli_query($conn, $sql)) {
        die("MySQL Error: " . mysqli_error($conn));
    }

    header('Location: manage_expense.php');
    exit();
}

// ---------------- DELETE EXPENSE ----------------
if (isset($_POST['delete'])) {
    $id = $_GET['delete'];

    $sql = "DELETE FROM expenses WHERE user_id='$userid' AND expense_id='$id'";
    if (!mysqli_query($conn, $sql)) {
        die("MySQL Error: " . mysqli_error($conn));
    }

    header('Location: manage_expense.php');
    exit();
}

// ---------------- EDIT BUTTON CLICK ----------------
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $update = true;

    $record = mysqli_query($conn, "SELECT * FROM expenses WHERE user_id='$userid' AND expense_id=$id");
    if (mysqli_num_rows($record) == 1) {
        $n = mysqli_fetch_array($record);
        $expenseamount = $n['expense'];
        $expensedate = $n['expensedate'];
        $expensecategory = $n['expensecategory'];
    } else {
        echo "WARNING: AUTHORIZATION ERROR: Trying to Access Unauthorized data";
    }
}

// ---------------- DELETE BUTTON CLICK ----------------
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $del = true;

    $record = mysqli_query($conn, "SELECT * FROM expenses WHERE user_id='$userid' AND expense_id=$id");
    if (mysqli_num_rows($record) == 1) {
        $n = mysqli_fetch_array($record);
        $expenseamount = $n['expense'];
        $expensedate = $n['expensedate'];
        $expensecategory = $n['expensecategory'];
    } else {
        echo "WARNING: AUTHORIZATION ERROR: Trying to Access Unauthorized data";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Expense Manager - Dashboard</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <script src="js/feather.min.js"></script>
</head>

<body>
    <div class="d-flex" id="wrapper">

        <!-- Sidebar -->
        <div class="border-right" id="sidebar-wrapper">
            <div class="user">
                <img class="img img-fluid rounded-circle" src="<?php echo $userprofile ?>" width="120">
                <h5><?php echo $username ?></h5>
                <p><?php echo $useremail ?></p>
            </div>
            <div class="sidebar-heading">Management</div>
            <div class="list-group list-group-flush">
                <a href="index.php" class="list-group-item list-group-item-action"><span data-feather="home"></span> Dashboard</a>
                <a href="add_expense.php" class="list-group-item list-group-item-action sidebar-active"><span data-feather="plus-square"></span> Add Expenses</a>
                <a href="manage_expense.php" class="list-group-item list-group-item-action"><span data-feather="dollar-sign"></span> Manage Expenses</a>
            </div>
            <div class="sidebar-heading">Settings</div>
            <div class="list-group list-group-flush">
                <a href="profile.php" class="list-group-item list-group-item-action"><span data-feather="user"></span> Profile</a>
                <a href="logout.php" class="list-group-item list-group-item-action"><span data-feather="power"></span> Logout</a>
            </div>
        </div>

        <!-- Page Content -->
        <div id="page-content-wrapper">

            <nav class="navbar navbar-expand-lg navbar-light border-bottom">
                <button class="toggler" type="button" id="menu-toggle" aria-expanded="false">
                    <span data-feather="menu"></span>
                </button>
            </nav>

            <div class="container">
                <h3 class="mt-4 text-center">Add Your Daily Expenses</h3>
                <hr>
                <div class="row">
                    <div class="col-md-3"></div>

                    <div class="col-md" style="margin:0 auto;">
                        <form action="" method="POST">
                            <div class="form-group row">
                                <label for="expenseamount" class="col-sm-6 col-form-label"><b>Enter Amount($)</b></label>
                                <div class="col-md-6">
                                    <input type="number" class="form-control col-sm-12" value="<?php echo $expenseamount; ?>" id="expenseamount" name="expenseamount" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="expensedate" class="col-sm-6 col-form-label"><b>Date</b></label>
                                <div class="col-md-6">
                                    <input type="date" class="form-control col-sm-12" value="<?php echo $expensedate; ?>" name="expensedate" id="expensedate" required>
                                </div>
                            </div>
                            <fieldset class="form-group">
                                <div class="row">
                                    <legend class="col-form-label col-sm-6 pt-0"><b>Category</b></legend>
                                    <div class="col-md">
                                        <?php
                                        $categories = ['Medicine','Food','Bills & Recharges','Entertainment','Clothings','Rent','Household Items','Others'];
                                        foreach ($categories as $cat) {
                                            $checked = ($expensecategory == $cat) ? 'checked' : '';
                                            echo "<div class='form-check'>
                                                    <input class='form-check-input' type='radio' name='expensecategory' value='$cat' $checked>
                                                    <label class='form-check-label'>$cat</label>
                                                  </div>";
                                        }
                                        ?>
                                    </div>
                                </div>
                            </fieldset>
                            <div class="form-group row">
                                <div class="col-md-12 text-right">
                                    <?php if ($update == true) : ?>
                                        <button class="btn btn-lg btn-block btn-warning" style="border-radius: 0%;" type="submit" name="update">Update</button>
                                    <?php elseif ($del == true) : ?>
                                        <button class="btn btn-lg btn-block btn-danger" style="border-radius: 0%;" type="submit" name="delete">Delete</button>
                                    <?php else : ?>
                                        <button type="submit" name="add" class="btn btn-lg btn-block btn-success" style="border-radius: 0%;">Add Expense</button>
                                    <?php endif ?>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="col-md-3"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="js/jquery.slim.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script>
        $("#menu-toggle").click(function(e) {
            e.preventDefault();
            $("#wrapper").toggleClass("toggled");
        });
        feather.replace();
    </script>
</body>
</html>
