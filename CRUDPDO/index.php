<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>PHP PDO CRUD Example</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">

<h2 class="mb-4">Student Records (PDO)</h2>
<a href="create.php" class="btn btn-success mb-3">+ Add Student</a>

<table class="table table-bordered">
  <thead>
    <tr>
      <th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Action</th>
    </tr>
  </thead>
  <tbody>
    <?php
    $stmt = $conn->query("SELECT * FROM students");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      echo "<tr>
              <td>{$row['id']}</td>
              <td>{$row['name']}</td>
              <td>{$row['email']}</td>
              <td>{$row['phone']}</td>
              <td>
                <a href='edit.php?id={$row['id']}' class='btn btn-warning btn-sm'>Edit</a>
                <a href='delete.php?id={$row['id']}' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure?\");'>Delete</a>
              </td>
            </tr>";
    }
    ?>
  </tbody>
</table>

</body>
</html>



