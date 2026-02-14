<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/dashboard.php'; ?>

<div class="container-fluid px-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">📝 Edit Course</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="/courses">Courses</a></li>
                    <li class="breadcrumb-item active"><?= htmlspecialchars($course['code']) ?></li>
                </ol>
            </nav>
        </div>
        <a href="/courses/view/<?= $course['id'] ?>" class="btn btn-secondary">
            <i class="fas fa-eye me-1"></i>View Course
        </a>
    </div>

    <!-- Course Form -->
    <div class="card">
        <div class="card-body">
            <form method="POST" action="/courses/update/<?= $course['id'] ?>" novalidate>
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

                <div class="row">
                    <!-- Course Code -->
                    <div class="col-md-4 mb-3">
                        <label for="code" class="form-label">Course Code <span class="text-danger">*</span></label>
                        <input 
                            type="text" 
                            class="form-control <?= isset($errors['code']) ? 'is-invalid' : '' ?>" 
                            id="code" 
                            name="code" 
                            value="<?= htmlspecialchars($form_data['code'] ?? $course['code']) ?>"
                            required
                            maxlength="20"
                            style="text-transform: uppercase;"
                            placeholder="e.g., CSC301">
                        <?php if (isset($errors['code'])): ?>
                            <div class="invalid-feedback"><?= $errors['code'] ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Course Title -->
                    <div class="col-md-8 mb-3">
                        <label for="title" class="form-label">Course Title <span class="text-danger">*</span></label>
                        <input 
                            type="text" 
                            class="form-control <?= isset($errors['title']) ? 'is-invalid' : '' ?>" 
                            id="title" 
                            name="title" 
                            value="<?= htmlspecialchars($form_data['title'] ?? $course['title']) ?>"
                            required
                            maxlength="200"
                            placeholder="e.g., Database Management Systems">
                        <?php if (isset($errors['title'])): ?>
                            <div class="invalid-feedback"><?= $errors['title'] ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="row">
                    <!-- Department -->
                    <div class="col-md-4 mb-3">
                        <label for="department_id" class="form-label">Department <span class="text-danger">*</span></label>
                        <select 
                            class="form-select <?= isset($errors['department_id']) ? 'is-invalid' : '' ?>" 
                            id="department_id" 
                            name="department_id"
                            required>
                            <option value="">Select Department</option>
                            <?php foreach ($departments as $department): ?>
                                <option value="<?= $department['id'] ?>" 
                                    <?= ($form_data['department_id'] ?? $course['department_id']) == $department['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($department['code']) ?> - <?= htmlspecialchars($department['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($errors['department_id'])): ?>
                            <div class="invalid-feedback"><?= $errors['department_id'] ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Level -->
                    <div class="col-md-4 mb-3">
                        <label for="level_id" class="form-label">Level <span class="text-danger">*</span></label>
                        <select 
                            class="form-select <?= isset($errors['level_id']) ? 'is-invalid' : '' ?>" 
                            id="level_id" 
                            name="level_id"
                            required>
                            <option value="">Select Level</option>
                            <?php foreach ($levels as $level): ?>
                                <option value="<?= $level['id'] ?>" 
                                    <?= ($form_data['level_id'] ?? $course['level_id']) == $level['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($level['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($errors['level_id'])): ?>
                            <div class="invalid-feedback"><?= $errors['level_id'] ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Semester -->
                    <div class="col-md-2 mb-3">
                        <label for="semester_number" class="form-label">Semester <span class="text-danger">*</span></label>
                        <select 
                            class="form-select <?= isset($errors['semester_number']) ? 'is-invalid' : '' ?>" 
                            id="semester_number" 
                            name="semester_number"
                            required>
                            <option value="">Select</option>
                            <option value="1" <?= ($form_data['semester_number'] ?? $course['semester_number']) == 1 ? 'selected' : '' ?>>
                                First
                            </option>
                            <option value="2" <?= ($form_data['semester_number'] ?? $course['semester_number']) == 2 ? 'selected' : '' ?>>
                                Second
                            </option>
                        </select>
                        <?php if (isset($errors['semester_number'])): ?>
                            <div class="invalid-feedback"><?= $errors['semester_number'] ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Credit Units -->
                    <div class="col-md-2 mb-3">
                        <label for="credit_units" class="form-label">Credit Units <span class="text-danger">*</span></label>
                        <input 
                            type="number" 
                            class="form-control <?= isset($errors['credit_units']) ? 'is-invalid' : '' ?>" 
                            id="credit_units" 
                            name="credit_units" 
                            value="<?= htmlspecialchars($form_data['credit_units'] ?? $course['credit_units']) ?>"
                            required
                            min="1"
                            max="10">
                        <?php if (isset($errors['credit_units'])): ?>
                            <div class="invalid-feedback"><?= $errors['credit_units'] ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Course Type -->
                <div class="mb-3">
                    <label class="form-label">Course Type <span class="text-danger">*</span></label>
                    <div>
                        <div class="form-check form-check-inline">
                            <input 
                                class="form-check-input" 
                                type="radio" 
                                name="is_elective" 
                                id="is_core" 
                                value="0"
                                <?= ($form_data['is_elective'] ?? $course['is_elective']) == 0 ? 'checked' : '' ?>>
                            <label class="form-check-label" for="is_core">
                                Core/Compulsory
                            </label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input 
                                class="form-check-input" 
                                type="radio" 
                                name="is_elective" 
                                id="is_elective" 
                                value="1"
                                <?= ($form_data['is_elective'] ?? $course['is_elective']) == 1 ? 'checked' : '' ?>>
                            <label class="form-check-label" for="is_elective">
                                Elective
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea 
                        class="form-control" 
                        id="description" 
                        name="description" 
                        rows="4"
                        placeholder="Enter course description..."><?= htmlspecialchars($form_data['description'] ?? $course['description'] ?? '') ?></textarea>
                </div>

                <!-- Status -->
                <div class="mb-4">
                    <div class="form-check form-switch">
                        <input 
                            class="form-check-input" 
                            type="checkbox" 
                            id="is_active" 
                            name="is_active" 
                            value="1"
                            <?= ($form_data['is_active'] ?? $course['is_active']) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="is_active">
                            Active (Available for registration)
                        </label>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex justify-content-between">
                    <a href="/courses/view/<?= $course['id'] ?>" class="btn btn-secondary">
                        <i class="fas fa-times me-1"></i>Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Update Course
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Auto-uppercase course code
document.getElementById('code').addEventListener('input', function(e) {
    e.target.value = e.target.value.toUpperCase();
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
