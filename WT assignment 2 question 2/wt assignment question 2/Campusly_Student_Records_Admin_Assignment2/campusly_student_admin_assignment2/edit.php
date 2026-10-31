/* Edit the details of an existing student. */
<?php
require_once __DIR__ . '/config.php';

// Read the student ID from the address bar.
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    set_flash('Invalid student record.', 'error');
    redirect('index.php');
}

// Get the current details to show in the form.
$statement = $pdo->prepare(
    'SELECT * FROM students WHERE id = ?'
);
$statement->execute([$id]);
$student = $statement->fetch();

if (!$student) {
    set_flash('Student record not found.', 'error');
    redirect('index.php');
}

$errors = [];

// Check the updated details when the form is submitted.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $student['name'] = trim($_POST['name'] ?? '');
    $student['roll_number'] = trim($_POST['roll_number'] ?? '');
    $student['email'] = trim($_POST['email'] ?? '');
    $student['course'] = trim($_POST['course'] ?? '');
    $student['phone'] = trim($_POST['phone'] ?? '');

    if ($student['name'] === '' || strlen($student['name']) > 120) {
        $errors[] = 'Enter a valid student name.';
    }

    if (
        $student['roll_number'] === ''
        || strlen($student['roll_number']) > 40
    ) {
        $errors[] = 'Enter a valid roll number.';
    }

    if (!filter_var($student['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Enter a valid email address.';
    }

    if ($student['course'] === '' || strlen($student['course']) > 100) {
        $errors[] = 'Enter a valid course name.';
    }

    // Update the record if all details are valid.
    if (!$errors) {
        try {
            $update = $pdo->prepare(
                'UPDATE students
                 SET name = :name,
                     roll_number = :roll_number,
                     email = :email,
                     course = :course,
                     phone = :phone
                 WHERE id = :id'
            );

            $update->execute([
                'name' => $student['name'],
                'roll_number' => $student['roll_number'],
                'email' => $student['email'],
                'course' => $student['course'],
                'phone' => $student['phone'] !== ''
                    ? $student['phone']
                    : null,
                'id' => $id,
            ]);

            set_flash('Student details updated successfully.');
            redirect("view.php?id={$id}");
        } catch (PDOException $exception) {
            $errors[] = 'That roll number may already belong to another student.';
        }
    }
}

require 'header.php';
?>

<section class="page-heading">
    <div>
        <p class="eyebrow">UPDATE RECORD</p>
        <h1>Edit student</h1>
    </div>

    <a class="button secondary" href="view.php?id=<?= (int) $id ?>">
        Cancel
    </a>
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
                value="<?= e($student['name']) ?>"
            >
        </label>

        <label>
            Roll number
            <input
                type="text"
                name="roll_number"
                maxlength="40"
                required
                value="<?= e($student['roll_number']) ?>"
            >
        </label>

        <label>
            Email address
            <input
                type="email"
                name="email"
                maxlength="150"
                required
                value="<?= e($student['email']) ?>"
            >
        </label>

        <label>
            Course
            <input
                type="text"
                name="course"
                maxlength="100"
                required
                value="<?= e($student['course']) ?>"
            >
        </label>

        <label>
            Phone number
            <input
                type="text"
                name="phone"
                maxlength="20"
                value="<?= e($student['phone'] ?? '') ?>"
            >
        </label>
    </div>

    <button class="button" type="submit">Update student</button>
</form>

<?php require 'footer.php'; ?>
