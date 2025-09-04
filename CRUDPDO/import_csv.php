<?php
include 'db.php';

$errors = [];
$success = "";

if (isset($_POST['import'])) {
    if (isset($_FILES['csv_file']['tmp_name']) && $_FILES['csv_file']['error'] === UPLOAD_ERR_OK) {
        $file = fopen($_FILES['csv_file']['tmp_name'], "r");
        $rowCount = 0;
        $headerSkipped = false;

        while (($row = fgetcsv($file, 1000, ",")) !== FALSE) {
            if (!$headerSkipped) { 
                $headerSkipped = true; // skip header row
                continue;
            }

            $name  = trim($row[0]);
            $email = trim($row[1]);
            $phone = trim($row[2]);

            // === VALIDATION ===
            if (empty($name)) {
                $errors[] = "Row $rowCount: Name required.";
                continue;
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Row $rowCount: Invalid email.";
                continue;
            }
            if (!preg_match('/^[0-9]{10}$/', $phone)) {
                $errors[] = "Row $rowCount: Phone must be 10 digits.";
                continue;
            }

            try {
                $stmt = $conn->prepare("INSERT INTO students (name, email, phone) VALUES (:name, :email, :phone)");
                $stmt->execute([
                    ':name'  => $name,
                    ':email' => $email,
                    ':phone' => $phone
                ]);
                $rowCount++;
            } catch (PDOException $e) {
                if ($e->getCode() == 23000) {
                    $errors[] = "Row $rowCount: Email already exists.";
                } else {
                    $errors[] = "Row $rowCount: DB error.";
                }
            }
        }
        fclose($file);

        if ($rowCount > 0) {
            $success = "$rowCount records imported successfully.";
        }
    } else {
        $errors[] = "Please upload a valid CSV file.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Import CSV</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">

<h2>Import Students (CSV)</h2>

<?php if ($errors): ?>
<div class="alert alert-danger"><ul>
  <?php foreach ($errors as $e) echo "<li>$e</li>"; ?>
</ul></div>
<?php endif; ?>

<?php if ($success): ?>
<div class="alert alert-success"><?= $success ?></div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">
  <div class="mb-3">
    <label>Select CSV File</label>
    <input type="file" name="csv_file" class="form-control" required>
  </div>
  <button type="submit" name="import" class="btn btn-primary">Import</button>
  <a href="index.php" class="btn btn-secondary">Back</a>
</form>

<p class="mt-3"><strong>CSV format (first row is header):</strong></p>
<pre>
Name,Email,Phone
John Doe,john@example.com,9876543210
Jane Doe,jane@example.com,9123456789
</pre>

</body>
</html>
