<?php
$errors = [];
$successMessage = '';

$allowedPriorities = ['low', 'normal', 'urgent'];

$subject = '';
$description = '';
$priority = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject = trim($_POST['subject'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $priority = $_POST['priority'] ?? '';

    if ($subject === '') {
        $errors['subject'] = 'Тема тікета обов\'язкова для заповнення.';
    }

    if (mb_strlen($description) < 15) {
        $errors['description'] = 'Опис має містити щонайменше 15 символів.';
    }

    if (!in_array($priority, $allowedPriorities)) {
        $errors['priority'] = 'Оберіть коректний пріоритет зі списку.';
    }

    if (empty($errors)) {
        $successMessage = "Тікет «" . htmlspecialchars($subject) . "» успішно створено!";
        $subject = '';
        $description = '';
        $priority = '';
    }
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Практична робота №2 - Варіант 19</title>
    <style>
        body { font-family: sans-serif; padding: 20px; background-color: #f4f4f9; }
        .form-container { background: white; padding: 20px; border-radius: 8px; border: 1px solid #ccc; max-width: 500px; margin: 0 auto; }
        .form-group { margin-bottom: 15px; }
        label { display: block; font-weight: bold; margin-bottom: 5px; }
        input[type="text"], textarea, select { width: 100%; padding: 8px; box-sizing: border-box; }
        .error { color: red; font-size: 0.9em; margin-top: 5px; display: block;}
        .success { color: green; font-weight: bold; margin-bottom: 15px; padding: 10px; background: #e8f5e9; border: 1px solid #c8e6c9; border-radius: 5px;}
        button { padding: 10px 15px; background-color: #3498db; color: white; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background-color: #2980b9; }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Створення тікета</h2>

    <?php if ($successMessage): ?>
        <div class="success"><?= $successMessage ?></div>
    <?php endif; ?>

    <form method="post" action="form.php" id="ticketForm">

        <div class="form-group">
            <label for="subject">Тема тікета:</label>
            <input type="text" id="subject" name="subject"
                   value="<?= htmlspecialchars($subject) ?>"
                   required>
            <?php if (isset($errors['subject'])): ?>
                <span class="error"><?= $errors['subject'] ?></span>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="description">Опис проблеми:</label>
            <textarea id="description" name="description" rows="5" required minlength="15"><?= htmlspecialchars($description) ?></textarea>
            <?php if (isset($errors['description'])): ?>
                <span class="error"><?= $errors['description'] ?></span>
            <?php endif; ?>
            <span class="error" id="js-error-description"></span>
        </div>

        <div class="form-group">
            <label for="priority">Пріоритет:</label>
            <select id="priority" name="priority" required>
                <option value="" disabled <?= $priority === '' ? 'selected' : '' ?>>Оберіть пріоритет...</option>
                <option value="low" <?= $priority === 'low' ? 'selected' : '' ?>>Низький</option>
                <option value="normal" <?= $priority === 'normal' ? 'selected' : '' ?>>Звичайний</option>
                <option value="urgent" <?= $priority === 'urgent' ? 'selected' : '' ?>>Терміновий</option>
            </select>
            <?php if (isset($errors['priority'])): ?>
                <span class="error"><?= $errors['priority'] ?></span>
            <?php endif; ?>
        </div>

        <button type="submit">Створити тікет</button>
    </form>
</div>

<script>
    const descriptionField = document.getElementById('description');
    const form = document.getElementById('ticketForm');

    const isSuccess = <?= $successMessage ? 'true' : 'false' ?>;

    if (isSuccess) {
        localStorage.removeItem('ticket_draft_description');
    } else {
        const savedDraft = localStorage.getItem('ticket_draft_description');
        if (savedDraft && descriptionField.value === '') {
            descriptionField.value = savedDraft;
        }
    }

    descriptionField.addEventListener('input', () => {
        localStorage.setItem('ticket_draft_description', descriptionField.value);
    });

    form.addEventListener('submit', function(event) {
        const descValue = descriptionField.value.trim();
        const jsErrorSpan = document.getElementById('js-error-description');

        if (descValue.length < 15) {
            event.preventDefault();
            jsErrorSpan.textContent = 'Помилка (JS): Опис занадто короткий. Потрібно мінімум 15 символів.';
            descriptionField.style.borderColor = 'red';
        } else {
            jsErrorSpan.textContent = '';
            descriptionField.style.borderColor = '#ccc';
        }
    });
</script>
</body>
</html>