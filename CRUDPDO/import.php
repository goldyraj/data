<?php
include 'db.php';
require 'vendor/autoload.php'; // PhpSpreadsheet autoload

use PhpOffice\PhpSpreadsheet\IOFactory;

$errors = [];
$success = "";

if (isset($_POST['import'])) {
    if (isset($_FILES['excel_file']['tmp_name']) && $_FILES['excel_file']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['excel_file']['tmp_name'];

        try {
            $spreadsheet = IOFactory::load($file);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            $rowCount = 0;

            foreach ($rows as $index => $row) {
                if ($index === 0) continue; // skip header row

                $name  = trim($row[0]);
                $email = trim($row[1]);
                $phone = trim($row[2]);

                // === VALIDATION ===
                if (empty($name)) {
                    $errors[] = "Row " . ($index+1) . ": Name is required.";
                    continue;
                }
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $errors[] = "Row " . ($index+1) . ": Invalid email.";
                    continue;
                }
                if (!preg_match('/^[0-9]{10}$/', $phone)) {
                    $errors[] = "Row " . ($index+1) . ": Phone must be 10 digits.";
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
                        $errors[] = "Row " . ($index+1) . ": Email already exists.";
                    } else {
                        $errors[] = "Row " . ($index+1) . ": DB error - " . $e->getMessage();
                    }
                }
            }

            if ($rowCount > 0) {
                $success = "$rowCount records imported successfully.";
            }
        } catch (Exception $e) {
            $errors[] = "Error reading Excel file: " . $e->getMessage();
        }
    } else {
        $errors[] = "Please upload a valid Excel file.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Import Excel</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">

<h2>Import Students from Excel</h2>

<?php if (!empty($errors)): ?>
  <div class="alert alert-danger">
    <ul>
      <?php foreach ($errors as $error) echo "<li>$error</li>"; ?>
    </ul>
  </div>
<?php endif; ?>

<?php if (!empty($success)): ?>
  <div class="alert alert-success"><?= $success ?></div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">
  <div class="mb-3">
    <label>Select Excel File (.xlsx or .xls)</label>
    <input type="file" name="excel_file" class="form-control" required>
  </div>
  <button type="submit" name="import" class="btn btn-primary">Import</button>
  <a href="index.php" class="btn btn-secondary">Back</a>
</form>

<p class="mt-3"><strong>Excel format (first row as header):</strong></p>
<pre>
Name      | Email              | Phone
John Doe  | john@example.com   | 9876543210
Jane Doe  | jane@example.com   | 9123456789
</pre>

</body>
</html>
