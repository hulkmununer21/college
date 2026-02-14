<?php $pageTitle = $data['title'] ?? 'Create Level'; ?>

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Create New Level</h1>
                <a href="/academic-structure/levels" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>

            <div class="card">
                <div class="card-body">
                    <form action="/academic-structure/store-level" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($data['csrf_token']) ?>">

                        <div class="form-group">
                            <label for="name">Level Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="<?= htmlspecialchars($_SESSION['form_data']['name'] ?? '') ?>"
                                   placeholder="e.g., 100 Level" required>
                        </div>

                        <div class="form-group">
                            <label for="level_number">Level Number <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="level_number" name="level_number" 
                                   value="<?= htmlspecialchars($_SESSION['form_data']['level_number'] ?? '') ?>"
                                   placeholder="e.g., 100, 200, 300" step="100" min="100" max="900" required>
                            <small class="form-text text-muted">Numeric representation (100, 200, 300, etc.)</small>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="min_credit_units">Minimum Credit Units <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="min_credit_units" name="min_credit_units" 
                                           value="<?= htmlspecialchars($_SESSION['form_data']['min_credit_units'] ?? '15') ?>"
                                           min="0" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="max_credit_units">Maximum Credit Units <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="max_credit_units" name="max_credit_units" 
                                           value="<?= htmlspecialchars($_SESSION['form_data']['max_credit_units'] ?? '24') ?>"
                                           min="0" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"><?= htmlspecialchars($_SESSION['form_data']['description'] ?? '') ?></textarea>
                        </div>

                        <div class="form-group mb-0">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Create Level
                            </button>
                            <a href="/academic-structure/levels" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php unset($_SESSION['form_data']); ?>
