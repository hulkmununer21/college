<?php $pageTitle = $data['title'] ?? 'Create Department'; ?>

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Create New Department</h1>
                <a href="/academic-structure/departments" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>

            <div class="card">
                <div class="card-body">
                    <form action="/academic-structure/store-department" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($data['csrf_token']) ?>">

                        <div class="form-group">
                            <label for="faculty_id">Faculty <span class="text-danger">*</span></label>
                            <select class="form-control" id="faculty_id" name="faculty_id" required>
                                <option value="">-- Select Faculty --</option>
                                <?php foreach ($data['faculties'] as $faculty): ?>
                                    <option value="<?= $faculty['id'] ?>" 
                                        <?= (($_SESSION['form_data']['faculty_id'] ?? '') == $faculty['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($faculty['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="name">Department Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="<?= htmlspecialchars($_SESSION['form_data']['name'] ?? '') ?>"
                                   placeholder="e.g., Computer Science" required>
                        </div>

                        <div class="form-group">
                            <label for="code">Department Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control text-uppercase" id="code" name="code" 
                                   value="<?= htmlspecialchars($_SESSION['form_data']['code'] ?? '') ?>"
                                   placeholder="e.g., CSC" pattern="[A-Z]{2,5}" maxlength="5" required>
                            <small class="form-text text-muted">2-5 uppercase letters</small>
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="4"><?= htmlspecialchars($_SESSION['form_data']['description'] ?? '') ?></textarea>
                        </div>

                        <div class="form-group mb-0">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Create Department
                            </button>
                            <a href="/academic-structure/departments" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php unset($_SESSION['form_data']); ?>
