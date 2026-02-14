<?php $pageTitle = $data['title'] ?? 'Edit Semester'; ?>

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Edit Semester</h1>
                <a href="/academic-structure/view-session/<?= $data['semester']['session_id'] ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>

            <div class="card">
                <div class="card-body">
                    <form action="/academic-structure/update-semester/<?= $data['semester']['id'] ?>" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($data['csrf_token']) ?>">
                        <input type="hidden" name="session_id" value="<?= $data['semester']['session_id'] ?>">

                        <div class="alert alert-info">
                            <strong>Session:</strong> <?= htmlspecialchars($data['semester']['session_name']) ?>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="semester_number">Semester Number <span class="text-danger">*</span></label>
                                    <select class="form-control" id="semester_number" name="semester_number" required>
                                        <option value="1" <?= ($data['semester']['semester_number'] == 1) ? 'selected' : '' ?>>First Semester</option>
                                        <option value="2" <?= ($data['semester']['semester_number'] == 2) ? 'selected' : '' ?>>Second Semester</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Semester Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name" 
                                           value="<?= htmlspecialchars($data['semester']['name']) ?>" required>
                                </div>
                            </div>
                        </div>

                        <h5 class="mt-3 mb-3">Semester Period</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="start_date">Start Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="start_date" name="start_date" 
                                           value="<?= $data['semester']['start_date'] ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="end_date">End Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="end_date" name="end_date" 
                                           value="<?= $data['semester']['end_date'] ?>" required>
                                </div>
                            </div>
                        </div>

                        <h5 class="mt-3 mb-3">Course Registration Period</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="registration_start_date">Registration Start Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="registration_start_date" name="registration_start_date" 
                                           value="<?= $data['semester']['registration_start_date'] ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="registration_end_date">Registration End Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="registration_end_date" name="registration_end_date" 
                                           value="<?= $data['semester']['registration_end_date'] ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-0">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Semester
                            </button>
                            <a href="/academic-structure/view-session/<?= $data['semester']['session_id'] ?>" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
