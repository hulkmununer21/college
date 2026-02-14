<?php $pageTitle = $data['title'] ?? 'Create Semester'; ?>

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Create Semester for <?= htmlspecialchars($data['session']['name']) ?></h1>
                <a href="/academic-structure/view-session/<?= $data['session']['id'] ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>

            <div class="card">
                <div class="card-body">
                    <form action="/academic-structure/store-semester" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($data['csrf_token']) ?>">
                        <input type="hidden" name="session_id" value="<?= $data['session']['id'] ?>">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="semester_number">Semester Number <span class="text-danger">*</span></label>
                                    <select class="form-control" id="semester_number" name="semester_number" required>
                                        <option value="">-- Select --</option>
                                        <option value="1">First Semester</option>
                                        <option value="2">Second Semester</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Semester Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name" 
                                           value="<?= htmlspecialchars($_SESSION['form_data']['name'] ?? '') ?>"
                                           placeholder="e.g., First Semester" required>
                                </div>
                            </div>
                        </div>

                        <h5 class="mt-3 mb-3">Semester Period</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="start_date">Start Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="start_date" name="start_date" 
                                           value="<?= htmlspecialchars($_SESSION['form_data']['start_date'] ?? '') ?>"
                                           min="<?= $data['session']['start_date'] ?>"
                                           max="<?= $data['session']['end_date'] ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="end_date">End Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="end_date" name="end_date" 
                                           value="<?= htmlspecialchars($_SESSION['form_data']['end_date'] ?? '') ?>"
                                           min="<?= $data['session']['start_date'] ?>"
                                           max="<?= $data['session']['end_date'] ?>" required>
                                </div>
                            </div>
                        </div>

                        <h5 class="mt-3 mb-3">Course Registration Period</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="registration_start_date">Registration Start Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="registration_start_date" name="registration_start_date" 
                                           value="<?= htmlspecialchars($_SESSION['form_data']['registration_start_date'] ?? '') ?>"
                                           min="<?= $data['session']['start_date'] ?>"
                                           max="<?= $data['session']['end_date'] ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="registration_end_date">Registration End Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="registration_end_date" name="registration_end_date" 
                                           value="<?= htmlspecialchars($_SESSION['form_data']['registration_end_date'] ?? '') ?>"
                                           min="<?= $data['session']['start_date'] ?>"
                                           max="<?= $data['session']['end_date'] ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <small>
                                <strong>Note:</strong> All dates must be within the session period 
                                (<?= date('M j, Y', strtotime($data['session']['start_date'])) ?> - 
                                <?= date('M j, Y', strtotime($data['session']['end_date'])) ?>)
                            </small>
                        </div>

                        <div class="form-group mb-0">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Create Semester
                            </button>
                            <a href="/academic-structure/view-session/<?= $data['session']['id'] ?>" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php unset($_SESSION['form_data']); ?>

<script>
// Auto-fill name when semester number is selected
document.getElementById('semester_number').addEventListener('change', function() {
    const semesterNames = ['', 'First Semester', 'Second Semester'];
    document.getElementById('name').value = semesterNames[this.value] || '';
});
</script>
