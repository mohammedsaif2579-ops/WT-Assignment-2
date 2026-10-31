/* Delete a student and the files linked to that student. */
<?php
require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('index.php');
}

// Only allow a valid form submission to delete a record.
verify_csrf();

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    set_flash('Invalid student record.', 'error');
    redirect('index.php');
}

// Get the file names before deleting the student record.
$fileStatement = $pdo->prepare(
    'SELECT file_path FROM student_files WHERE student_id = ?'
);
$fileStatement->execute([$id]);
$files = $fileStatement->fetchAll();

// Delete the student. Related file details are removed by the database.
$delete = $pdo->prepare(
    'DELETE FROM students WHERE id = ?'
);
$delete->execute([$id]);

if ($delete->rowCount()) {
    foreach ($files as $file) {
        $path = __DIR__ . '/' . $file['file_path'];

        if (is_file($path)) {
            unlink($path);
        }
    }

    set_flash('Student record and uploaded files were deleted.');
} else {
    set_flash('Student record was not found.', 'error');
}

redirect('index.php');
