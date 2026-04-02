<?php
$host = "localhost";
$user = "root";
$password = "";
$db = "libraryy_db";

$conn = mysqli_connect($host, $user, $password, $db);

if (!$conn) {
    die("Connection Failed: " . mysqli_connect_error());
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Library Management System</title>
</head>
<body>

<h1>Library Management System</h1>

<!-- ADD BOOK -->
<h2>Add Book</h2>
<form method="POST">
    Title: <input type="text" name="title" required><br><br>
    Author: <input type="text" name="author" required><br><br>
    Quantity: <input type="number" name="quantity" required><br><br>
    <input type="submit" name="add_book" value="Add Book">
</form>

<?php
if (isset($_POST['add_book'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $author = mysqli_real_escape_string($conn, $_POST['author']);
    $quantity = (int)$_POST['quantity'];

    if ($title && $author && $quantity > 0) {
        $query = "INSERT INTO bookss (title, author, quantity) VALUES ('$title', '$author', '$quantity')";
        
        if (mysqli_query($conn, $query)) {
            echo "<p style='color:green;'>Book Added Successfully</p>";
        } else {
            echo "<p style='color:red;'>Error: " . mysqli_error($conn) . "</p>";
        }
    } else {
        echo "<p style='color:red;'>Please fill all fields correctly</p>";
    }
}
?>

<hr>

<!-- ISSUE BOOK -->
<h2>Issue Book</h2>
<form method="POST">
    Book ID: <input type="number" name="book_id" required><br><br>
    Student Name: <input type="text" name="student" required><br><br>
    Issue Date: <input type="date" name="issue_date" required><br><br>
    Return Date: <input type="date" name="return_date" required><br><br>
    <input type="submit" name="issue_book" value="Issue Book">
</form>

<?php
if (isset($_POST['issue_book'])) {
    $book_id = (int)$_POST['book_id'];
    $student = mysqli_real_escape_string($conn, $_POST['student']);
    $issue = $_POST['issue_date'];
    $return = $_POST['return_date'];

    if ($book_id && $student && $issue && $return) {
        $query = "INSERT INTO issue_books (book_id, student_name, issue_date, return_date, fine)
                  VALUES ('$book_id', '$student', '$issue', '$return', 0)";

        if (mysqli_query($conn, $query)) {
            echo "<p style='color:green;'>Book Issued Successfully</p>";
        } else {
            echo "<p style='color:red;'>Error: " . mysqli_error($conn) . "</p>";
        }
    } else {
        echo "<p style='color:red;'>All fields are required</p>";
    }
}
?>

<hr>

<!-- DISPLAY ISSUED BOOKS -->
<h2>Issued Books</h2>
<table border="1" cellpadding="10">
<tr>
    <th>ID</th>
    <th>Book ID</th>
    <th>Student</th>
    <th>Issue Date</th>
    <th>Return Date</th>
    <th>Fine</th>
    <th>Return</th>
</tr>

<?php
$result = mysqli_query($conn, "SELECT * FROM issue_books");

while ($row = mysqli_fetch_assoc($result)) {

    $id = $row['id'];
    $return_date = $row['return_date'];
    $today = date("Y-m-d");

    $fine = $row['fine'];

    if ($today > $return_date) {
        $late_days = (strtotime($today) - strtotime($return_date)) / (60 * 60 * 24);
        $fine = $late_days * 5;

        mysqli_query($conn, "UPDATE issue_books SET fine='$fine' WHERE id='$id'");
    }

    echo "<tr>";
    echo "<td>{$row['id']}</td>";
    echo "<td>{$row['book_id']}</td>";
    echo "<td>{$row['student_name']}</td>";
    echo "<td>{$row['issue_date']}</td>";
    echo "<td>{$row['return_date']}</td>";
    echo "<td>$fine</td>";
    echo "<td><a href='?return={$row['id']}'>Return</a></td>";
    echo "</tr>";
}
?>

</table>

<?php
// RETURN BOOK
if (isset($_GET['return'])) {
    $id = (int)$_GET['return'];

    if (mysqli_query($conn, "DELETE FROM issue_books WHERE id='$id'")) {
        echo "<p style='color:green;'>Book Returned Successfully</p>";
    } else {
        echo "<p style='color:red;'>Error: " . mysqli_error($conn) . "</p>";
    }
}
?>

</body>
</html>