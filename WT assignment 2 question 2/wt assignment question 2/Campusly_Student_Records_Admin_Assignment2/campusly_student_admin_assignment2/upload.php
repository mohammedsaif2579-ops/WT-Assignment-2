/* Check the uploaded file and save its details in the database. */
<?php
require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('index.php');
}

// Check the form token before uploading.
verify_csrf();

$studentId = filter_input(INPUT_POST, 'student_id', FILTER_VALIDATE_INT);
$category = $_POST['file_category'] ?? '';

if (
    !$studentId
    || !in_array($category, ['profile', 'document'], true)
) {
    set_flash('Invalid upload request.', 'error');
    redirect('index.php');
}

$studentCheck = $pdo->prepare(
    'SELECT id FROM students WHERE id = ?'
);
$studentCheck->execute([$studentId]);

if (!$studentCheck->fetch()) {
    set_flash('Student record not found.', 'error');
    redirect('index.php');
}

if (
    !isset($_FILES['student_file'])
    || $_FILES['student_file']['error'] !== UPLOAD_ERR_OK
) {
    set_flash('Choose a file and try uploading again.', 'error');
    redirect("view.php?id={$studentId}");
}

$file = $_FILES['student_file'];
// Set the size limit based on the type of file.
$maxSize = $category === 'profile'
    ? 2 * 1024 * 1024
    : 5 * 1024 * 1024;

if ($file['size'] <= 0 || $file['size'] > $maxSize) {
    set_flash('The file is empty or exceeds the allowed size.', 'error');
    redirect("view.php?id={$studentId}");
}

// Only these image and document types are accepted.
$allowedTypes = $category === 'profile'
    ? [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ]
    : [
        'application/pdf' => 'pdf',
        'application/msword' => 'doc',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
            => 'docx',
    ];

// Check the actual file type, not just its name.
$finfo = new finfo(FILEINFO_MIME_TYPE);
$mimeType = $finfo->file($file['tmp_name']);

if (!isset($allowedTypes[$mimeType])) {
    set_flash('This file type is not allowed.', 'error');
    redirect("view.php?id={$studentId}");
}

$extension = $allowedTypes[$mimeType];
// Give the file a new random name to avoid replacing another upload.
$storedName = bin2hex(random_bytes(16)) . '.' . $extension;
$folder = $category === 'profile'
    ? 'uploads/profiles'
    : 'uploads/documents';
$absoluteFolder = __DIR__ . '/' . $folder;

if (
    !is_dir($absoluteFolder)
    && !mkdir($absoluteFolder, 0755, true)
) {
    set_flash('Upload folder could not be created.', 'error');
    redirect("view.php?id={$studentId}");
}

$absolutePath = $absoluteFolder . '/' . $storedName;
$relativePath = $folder . '/' . $storedName;

if (file_exists($absolutePath)) {
    set_flash('File already exists. Please try again.', 'error');
    redirect("view.php?id={$studentId}");
}

// Move the file into the correct uploads folder.
if (!move_uploaded_file($file['tmp_name'], $absolutePath)) {
    set_flash('File upload failed.', 'error');
    redirect("view.php?id={$studentId}");
}

try {
    // Save the file details and the student ID in the database.
    $insert = $pdo->prepare(
        'INSERT INTO student_files
            (student_id, original_name, stored_name, file_path,
             file_type, file_size, file_category)
         VALUES
            (:student_id, :original_name, :stored_name, :file_path,
             :file_type, :file_size, :file_category)'
    );

    $insert->execute([
        'student_id' => $studentId,
        'original_name' => basename($file['name']),
        'stored_name' => $storedName,
        'file_path' => $relativePath,
        'file_type' => $mimeType,
        'file_size' => (int) $file['size'],
        'file_category' => $category,
    ]);

    set_flash('File uploaded successfully.');
} catch (PDOException $exception) {
    if (is_file($absolutePath)) {
        unlink($absolutePath);
    }

    set_flash('File details could not be saved.', 'error');
}

redirect("view.php?id={$studentId}");
