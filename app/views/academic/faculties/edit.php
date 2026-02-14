<?php $pageTitle = $data['title'] ?? 'Edit Faculty'; ?>

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Edit Faculty</h1>
                <a href="/academic-structure/faculties" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>

            <!-- Edit Form -->
            <div class="card">
                <div class="card-body">
                    <form action="/academic-structure/update-faculty/<?= $data['faculty']['id'] ?>" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($data['csrf_token']) ?>">

                        <div class="form-group">
                            <label for="name">Faculty Name <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control" 
                                   id="name" 
                                   name="name" 
                                   value="<?= htmlspecialchars($data['faculty']['name']) ?>"
                                   required>
                        </div>

                        <div class="form-group">
                            <label for="code">Faculty Code <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control text-uppercase" 
                                   id="code" 
                                   name="code" 
                                   value="<?= htmlspecialchars($data['faculty']['code']) ?>"
                                   pattern="[A-Z]{2,5}"
                                   maxlength="5"
                                   required>
                            <small class="form-text text-muted">2-5 uppercase letters</small>
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control" 
                                      id="description" 
                                      name="description" 
                                      rows="4"><?= htmlspecialchars($data['faculty']['description'] ?? '') ?></textarea>
                        </div>

                        <div class="form-group mb-0">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Faculty
                            </button>
                            <a href="/academic-structure/faculties" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('code').addEventListener('input', function() {
    this.value = this.value.toUpperCase();
});
</script>
