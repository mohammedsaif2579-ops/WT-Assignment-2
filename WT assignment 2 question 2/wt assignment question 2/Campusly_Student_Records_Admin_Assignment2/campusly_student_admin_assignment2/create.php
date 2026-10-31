/* Add a new student to the database. */
<?php
require_once __DIR__ . '/config.php';

$errors = [];
$name = '';
$rollNumber = '';
$email = '';
$course = '';
$phone = '';

// Run this part after the form is submitted.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $name = trim($_POST['name'] ?? '');
    $rollNumber = trim($_POST['roll_number'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $course = trim($_POST['course'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    // Check that the required details have been entered.
    if ($name === '' || strlen($name) > 120) {
        $errors[] = 'Enter a valid student name.';
    }

    if ($rollNumber === '' || strlen($rollNumber) > 40) {
        $errors[] = 'Enter a valid roll number.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Enter a valid email address.';
    }

    if ($course === '' || strlen($course) > 100) {
        $errors[] = 'Enter a valid course name.';
    }

    // Save the details if there are no errors.
    if (!$errors) {
        try {
            // Add the new student using a prepared query.
            $statement = $pdo->prepare(
                'INSERT INTO students
                    (name, roll_number, email, course, phone)
                 VALUES
                    (:name, :roll_number, :email, :course, :phone)'
            );

            $statement->execute([
                'name' => $name,
                'roll_number' => $rollNumber,
                'email' => $email,
                'course' => $course,
                'phone' => $phone !== '' ? $phone : null,
            ]);

            $studentId = (int) $pdo->lastInsertId();

            set_flash('Student added successfully.');
            redirect("view.php?id={$studentId}");
        } catch (PDOException $exception) {
            $errors[] = 'That roll number may already exist.';
        }
    }
}

require 'header.php';
?>

<section class="page-heading">
    <div>
        <p class="eyebrow">NEW RECORD</p>
        <h1>Add student</h1>
        <p class="muted">Enter the student details below.</p>
    </div>

    <a class="button secondary" href="index.php">Back to list</a>
</section>

<?php if ($errors): ?>
    <div class="alert error">
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?= e($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form class="form-card" method="post">
    <input
        type="hidden"
        name="csrf_token"
        value="<?= e(csrf_token()) ?>"
    >

    <div class="form-grid">
        <label>
            Student name
            <input
                type="text"
                name="name"
                maxlength="120"
                required
                value="<?= e($name) ?>"
            >
        </label>

        <label>
            Roll number
            <input
                type="text"
                name="roll_number"
                maxlength="40"
                required
                value="<?= e($rollNumber) ?>"
            >
        </label>

        <label>
            Email address
            <input
                type="email"
                name="email"
                maxlength="150"
                required
                value="<?= e($email) ?>"
            >
        </label>

        <label>
            Course
            <input
                type="text"
                name="course"
                maxlength="100"
                required
                value="<?= e($course) ?>"
            >
        </label>

        <label>
            Phone number
            <input
                type="text"
                name="phone"
                maxlength="20"
                value="<?= e($phone) ?>"
            >
        </label>
    </div>

    <button class="button" type="submit">Save student</button>
</form>

<?php require 'footer.php'; ?>
