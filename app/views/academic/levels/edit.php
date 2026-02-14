<?php $pageTitle = $data['title'] ?? 'Edit Level'; ?>

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Edit Level</h1>
                <a href="/academic-structure/levels" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>

            <div class="card">
                <div class="card-body">
                    <form action="/academic-structure/update-level/<?= $data['level']['id'] ?>" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($data['csrf_token']) ?>">

                        <div class="form-group">
                            <label for="name">Level Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="<?= htmlspecialchars($data['level']['name']) ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="level_number">Level Number <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="level_number" name="level_number" 
                                   value="<?= $data['level']['level_number'] ?>"
                                   step="100" min="100" max="900" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="min_credit_units">Minimum Credit Units <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="min_credit_units" name="min_credit_units" 
                                           value="<?= $data['level']['min_credit_units'] ?>" min="0" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="max_credit_units">Maximum Credit Units <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="max_credit_units" name="max_credit_units" 
                                           value="<?= $data['level']['max_credit_units'] ?>" min="0" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"><?= htmlspecialchars($data['level']['description'] ?? '') ?></textarea>
                        </div>

                        <div class="form-group mb-0">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Level
                            </button>
                            <a href="/academic-structure/levels" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
