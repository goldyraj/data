<?php include 'db.php'; ?>

<?php
$id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM students WHERE id = :id");
$stmt->execute([':id' => $id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (isset($_POST['update'])) {

      $name  = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
      // Basic PHP Validation
    if (empty($name)) {
        $errors[] = "Name is required.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email is required.";
    }
   if (empty($phone) || !preg_match('/^[0-9]{10}$/', $phone)) {
        $errors[] = "Phone number must be exactly 10 digits.";
    }
      if (empty($errors)) {
        try {
    $stmt = $conn->prepare("UPDATE students SET name=:name, email=:email, phone=:phone WHERE id=:id");
    $stmt->execute([
        ':name'  => $_POST['name'],
        ':email' => $_POST['email'],
        ':phone' => $_POST['phone'],
        ':id'    => $id
    ]);

    header("Location: index.php");
    exit();
    } catch (PDOException $e) {
            if ($e->getCode() == 23000) { // duplicate email error
                $errors[] = "Email already exists!";
            } else {
                $errors[] = "Database error: " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Student</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">

<h2>Edit Student</h2>
<?php if (!empty($errors)): ?>
  <div class="alert alert-danger">
    <ul>
      <?php foreach ($errors as $error) echo "<li>$error</li>"; ?>
    </ul>
  </div>
<?php endif; ?>
<form method="POST">
  <div class="mb-3">
    <label>Name</label>
    <input type="text" name="name" value="<?= htmlspecialchars($row['name']) ?>" class="form-control" required>
  </div>
  <div class="mb-3">
    <label>Email</label>
    <input type="email" name="email" value="<?= htmlspecialchars($row['email']) ?>" class="form-control" required>
  </div>
  <div class="mb-3">
    <label>Phone</label>
    <input type="text" name="phone" value="<?= htmlspecialchars($row['phone']) ?>" class="form-control" >
  </div>
  <button type="submit" name="update" class="btn btn-primary">Update</button>
  <a href="index.php" class="btn btn-secondary">Back</a>
</form>

</body>
</html>
