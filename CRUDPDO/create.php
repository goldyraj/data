<?php include 'db.php'; ?>

<?php
if (isset($_POST['submit'])) {

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
    $stmt = $conn->prepare("INSERT INTO students (name, email, phone) VALUES (:name, :email, :phone)");
    $stmt->execute([
        ':name'  => $_POST['name'],
        ':email' => $_POST['email'],
        ':phone' => $_POST['phone']
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
  <title>Add Student</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
<?php if (!empty($errors)): ?>
  <div class="alert alert-danger">
    <ul>
      <?php foreach ($errors as $error) echo "<li>$error</li>"; ?>
    </ul>
  </div>
<?php endif; ?>
<h2>Add Student</h2>
<form method="POST">
  <div class="mb-3">
    <label>Name</label>
    <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($name) ?>">
  </div>
  <div class="mb-3">
    <label>Email</label>
    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($email) ?>" >
  </div>
  <div class="mb-3">
    <label>Phone</label>
    <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($phone) ?>">
  </div>
  <button type="submit" name="submit" class="btn btn-success">Save</button>
  <a href="index.php" class="btn btn-secondary">Back</a>
</form>

</body>
</html>
