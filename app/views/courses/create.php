<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/dashboard.php'; ?>

<div class="container-fluid px-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">➕ Create New Course</h1>
        <a href="/courses" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i>Back to Courses
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="/courses/store">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

                        <div class="mb-3">
                            <label for="code" class="form-label">Course Code <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control <?= isset($errors['code']) ? 'is-invalid' : '' ?>" 
                                   id="code" 
                                   name="code" 
                                   value="<?= htmlspecialchars($form_data['code'] ?? '') ?>"
                                   placeholder="e.g., CSC301"
                                   required>
                            <?php if (isset($errors['code'])): ?>
                                <div class="invalid-feedback"><?= $errors['code'] ?></div>
                            <?php endif; ?>
                            <small class="text-muted">Enter a unique course code (e.g., CSC301, MTH202)</small>
                        </div>

                       <div class="mb-3">
                            <label for="title" class="form-label">Course Title <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control <?= isset($errors['title']) ? 'is-invalid' : '' ?>" 
                                   id="title" 
                                   name="title" 
                                   value="<?= htmlspecialchars($form_data['title'] ?? '') ?>"
                                   placeholder="e.g., Operating Systems"
                                   required>
                            <?php if (isset($errors['title'])): ?>
                                <div class="invalid-feedback"><?= $errors['title'] ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" 
                                      id="description" 
                                      name="description" 
                                      rows="3"
                                      placeholder="Enter course description..."><?= htmlspecialchars($form_data['description'] ?? '') ?></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="department_id" class="form-label">Department <span class="text-danger">*</span></label>
                                <select class="form-select <?= isset($errors['department_id']) ? 'is-invalid' : '' ?>" 
                                        id="department_id" 
                                        name="department_id" 
                                        required>
                                    <option value="">Select Department</option>
                                    <?php foreach ($departments as $dept): ?>
                                        <option value="<?= $dept['id'] ?>" <?= isset($form_data['department_id']) && $form_data['department_id'] == $dept['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($dept['code']) ?> - <?= htmlspecialchars($dept['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (isset($errors['department_id'])): ?>
                                    <div class="invalid-feedback"><?= $errors['department_id'] ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="level_id" class="form-label">Level <span class="text-danger">*</span></label>
                                <select class="form-select <?= isset($errors['level_id']) ? 'is-invalid' : '' ?>" 
                                        id="level_id" 
                                        name="level_id" 
                                        required>
                                    <option value="">Select Level</option>
                                    <?php foreach ($levels as $level): ?>
                                        <option value="<?= $level['id'] ?>" <?= isset($form_data['level_id']) && $form_data['level_id'] == $level['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($level['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (isset($errors['level_id'])): ?>
                                    <div class="invalid-feedback"><?= $errors['level_id'] ?></div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="semester_number" class="form-label">Semester <span class="text-danger">*</span></label>
                                <select class="form-select <?= isset($errors['semester_number']) ? 'is-invalid' : '' ?>" 
                                        id="semester_number" 
                                        name="semester_number" 
                                        required>
                                    <option value="">Select Semester</option>
                                    <option value="1" <?= isset($form_data['semester_number']) && $form_data['semester_number'] == 1 ? 'selected' : '' ?>>First Semester</option>
                                    <option value="2" <?= isset($form_data['semester_number']) && $form_data['semester_number'] == 2 ? 'selected' : '' ?>>Second Semester</option>
                                </select>
                                <?php if (isset($errors['semester_number'])): ?>
                                    <div class="invalid-feedback"><?= $errors['semester_number'] ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="credit_units" class="form-label">Credit Units <span class="text-danger">*</span></label>
                                <input type="number" 
                                       class="form-control <?= isset($errors['credit_units']) ? 'is-invalid' : '' ?>" 
                                       id="credit_units" 
                                       name="credit_units" 
                                       value="<?= htmlspecialchars($form_data['credit_units'] ?? '') ?>"
                                       min="1" 
                                       max="10"
                                       placeholder="e.g., 3"
                                       required>
                                <?php if (isset($errors['credit_units'])): ?>
                                    <div class="invalid-feedback"><?= $errors['credit_units'] ?></div>
                                <?php endif; ?>
                                <small class="text-muted">Enter value between 1 and 10</small>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="is_elective" 
                                       name="is_elective" 
                                       <?= isset($form_data['is_elective']) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="is_elective">
                                    This is an elective course
                                </label>
                                <small class="form-text text-muted d-block">
                                    Leave unchecked for core/compulsory courses
                                </small>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Create Course
                            </button>
                            <a href="/courses" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card bg-light">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-info-circle me-2"></i>Guidelines</h5>
                    <ul class="mb-0">
                        <li>Course code must be unique across all departments</li>
                        <li>Use standard naming convention (e.g., CSC301, MTH102)</li>
                        <li>Credit units typically range from 2-6 units</li>
                        <li>Core courses are mandatory for students</li>
                        <li>Elective courses are optional</li>
                        <li>Prerequisites can be added after creating the course</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
