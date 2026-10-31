/* Show the student list and search results. */
<?php
require_once __DIR__ . '/config.php';

// Get the search text entered by the user.
$search = trim($_GET['search'] ?? '');

// Search by student name, roll number or course.
if ($search !== '') {
    $statement = $pdo->prepare(
        'SELECT * FROM students
         WHERE name LIKE :search
            OR roll_number LIKE :search
            OR course LIKE :search
         ORDER BY id DESC'
    );
    $statement->execute(['search' => "%{$search}%"]);
} else {
    $statement = $pdo->query(
        'SELECT * FROM students ORDER BY id DESC'
    );
}

// Get the matching student records.
$students = $statement->fetchAll();

require 'header.php';
?>

<section class="page-heading">
    <div>
        <p class="eyebrow">COLLEGE ADMINISTRATION</p>
        <h1>Student records</h1>
        <p class="muted">Add, update and manage student information.</p>
    </div>

    <a class="button" href="create.php">+ Add student</a>
</section>

<form class="search-form" method="get">
    <input
        type="search"
        name="search"
        placeholder="Search by name, roll number or course"
        value="<?= e($search) ?>"
    >
    <button class="button secondary" type="submit">Search</button>

    <?php if ($search !== ''): ?>
        <a class="clear-link" href="index.php">Clear</a>
    <?php endif; ?>
</form>

<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Roll number</th>
                <th>Course</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
        </thead>

        <!-- Show a message if no student records are found. -->
        <tbody>
            <?php if (!$students): ?>
                <tr>
                    <td colspan="5" class="empty">No student records found.</td>
                </tr>
            <?php endif; ?>

            <?php foreach ($students as $student): ?>
                <tr>
                    <td><?= e($student['name']) ?></td>
                    <td><?= e($student['roll_number']) ?></td>
                    <td><?= e($student['course']) ?></td>
                    <td><?= e($student['email']) ?></td>
                    <td class="actions">
                        <a href="view.php?id=<?= (int) $student['id'] ?>">View</a>
                        <a href="edit.php?id=<?= (int) $student['id'] ?>">Edit</a>

                        <form
                            method="post"
                            action="delete.php"
                            onsubmit="return confirm('Delete this student and uploaded files?');"
                        >
                            <input
                                type="hidden"
                                name="csrf_token"
                                value="<?= e(csrf_token()) ?>"
                            >
                            <input
                                type="hidden"
                                name="id"
                                value="<?= (int) $student['id'] ?>"
                            >
                            <button class="text-button danger" type="submit">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require 'footer.php'; ?>
