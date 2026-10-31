/* Show one student and their uploaded files. */
<?php
require_once __DIR__ . '/config.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    set_flash('Invalid student record.', 'error');
    redirect('index.php');
}

$statement = $pdo->prepare(
    'SELECT * FROM students WHERE id = ?'
);
$statement->execute([$id]);
$student = $statement->fetch();

if (!$student) {
    set_flash('Student record not found.', 'error');
    redirect('index.php');
}

// Get this student's uploaded files, newest first.
$fileStatement = $pdo->prepare(
    'SELECT * FROM student_files
     WHERE student_id = ?
     ORDER BY upload_date DESC'
);
$fileStatement->execute([$id]);
$files = $fileStatement->fetchAll();

require 'header.php';
?>

<section class="page-heading">
    <div>
        <p class="eyebrow">STUDENT PROFILE</p>
        <h1><?= e($student['name']) ?></h1>
        <p class="muted">Roll number: <?= e($student['roll_number']) ?></p>
    </div>

    <div class="heading-actions">
        <a class="button secondary" href="edit.php?id=<?= (int) $id ?>">
            Edit details
        </a>
        <a class="button" href="index.php">All students</a>
    </div>
</section>

<section class="detail-card">
    <h2>Student details</h2>

    <div class="details-grid">
        <div>
            <span>Name</span>
            <strong><?= e($student['name']) ?></strong>
        </div>

        <div>
            <span>Roll number</span>
            <strong><?= e($student['roll_number']) ?></strong>
        </div>

        <div>
            <span>Course</span>
            <strong><?= e($student['course']) ?></strong>
        </div>

        <div>
            <span>Email</span>
            <strong><?= e($student['email']) ?></strong>
        </div>

        <div>
            <span>Phone</span>
            <strong><?= e($student['phone'] ?? 'Not provided') ?></strong>
        </div>
    </div>
</section>

<section class="detail-card">
    <h2>Upload profile image or document</h2>

    <form
        class="upload-form"
        method="post"
        action="upload.php"
        enctype="multipart/form-data"
    >
        <input
            type="hidden"
            name="csrf_token"
            value="<?= e(csrf_token()) ?>"
        >
        <input
            type="hidden"
            name="student_id"
            value="<?= (int) $id ?>"
        >

        <label>
            File category
            <select name="file_category" required>
                <option value="profile">Profile image</option>
                <option value="document">ID proof / certificate / document</option>
            </select>
        </label>

        <label>
            Choose file
            <input type="file" name="student_file" required>
        </label>

        <button class="button" type="submit">Upload file</button>
    </form>

    <p class="hint">
        Images: JPG, PNG, WEBP (max 2 MB).
        Documents: PDF, DOC, DOCX (max 5 MB).
    </p>
</section>

<!-- Show the file type, upload date and link. -->
<section class="detail-card">
    <h2>Uploaded files</h2>

    <?php if (!$files): ?>
        <p class="muted">No files have been uploaded for this student.</p>
    <?php else: ?>
        <div class="file-list">
            <?php foreach ($files as $file): ?>
                <div class="file-item">
                    <div>
                        <strong><?= e($file['original_name']) ?></strong>
                        <span>
                            <?= e(ucfirst($file['file_category'])) ?> ·
                            <?= e($file['file_type']) ?> ·
                            <?= e(date(
                                'd M Y, h:i A',
                                strtotime($file['upload_date'])
                            )) ?>
                        </span>
                    </div>

                    <a
                        href="<?= e($file['file_path']) ?>"
                        target="_blank"
                        rel="noopener"
                    >
                        Open file
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<?php require 'footer.php'; ?>
