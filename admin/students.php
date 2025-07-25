<?php
require_once '../includes/init.php';
Auth::requireRole('dean');

$student = new Student();
$message = '';
$messageType = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    if (!Auth::validateCSRFToken($csrf_token)) {
        $message = 'Invalid security token. Please try again.';
        $messageType = 'danger';
    } else {
        switch ($action) {
            case 'add':
                $name = Validator::sanitizeInput($_POST['name'] ?? '');
                $grade = Validator::sanitizeInput($_POST['grade'] ?? '');
                
                $errors = Validator::validateStudentData($name, $grade);
                
                if (empty($errors)) {
                    if ($student->create($name, $grade)) {
                        $message = 'Student added successfully.';
                        $messageType = 'success';
                    } else {
                        $message = 'Failed to add student.';
                        $messageType = 'danger';
                    }
                } else {
                    $message = implode('<br>', $errors);
                    $messageType = 'danger';
                }
                break;
                
            case 'edit':
                $id = (int)($_POST['id'] ?? 0);
                $name = Validator::sanitizeInput($_POST['name'] ?? '');
                $grade = Validator::sanitizeInput($_POST['grade'] ?? '');
                
                $errors = Validator::validateStudentData($name, $grade);
                
                if (empty($errors)) {
                    if ($student->update($id, $name, $grade)) {
                        $message = 'Student updated successfully.';
                        $messageType = 'success';
                    } else {
                        $message = 'Failed to update student.';
                        $messageType = 'danger';
                    }
                } else {
                    $message = implode('<br>', $errors);
                    $messageType = 'danger';
                }
                break;
                
            case 'delete':
                $id = (int)($_POST['id'] ?? 0);
                
                if ($student->delete($id)) {
                    $message = 'Student deleted successfully.';
                    $messageType = 'success';
                } else {
                    $message = 'Failed to delete student.';
                    $messageType = 'danger';
                }
                break;
        }
    }
}

// Get current action
$currentAction = $_GET['action'] ?? 'list';
$editStudent = null;

if ($currentAction === 'edit' && isset($_GET['id'])) {
    $editStudent = $student->getById((int)$_GET['id']);
    if (!$editStudent) {
        $currentAction = 'list';
        $message = 'Student not found.';
        $messageType = 'danger';
    }
}

// Get students list
$students = $student->getAll();

$pageTitle = 'Manage Students';
include 'includes/header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>Manage Students</h1>
                <a href="?action=add" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>
                    Add Student
                </a>
            </div>
            
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $messageType; ?>" role="alert">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <?php if ($currentAction === 'add' || $currentAction === 'edit'): ?>
        <!-- Add/Edit Form -->
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5><?php echo $currentAction === 'edit' ? 'Edit Student' : 'Add New Student'; ?></h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" data-validate>
                            <input type="hidden" name="csrf_token" value="<?php echo Auth::generateCSRFToken(); ?>">
                            <input type="hidden" name="action" value="<?php echo $currentAction; ?>">
                            <?php if ($editStudent): ?>
                                <input type="hidden" name="id" value="<?php echo $editStudent['id']; ?>">
                            <?php endif; ?>
                            
                            <div class="form-group mb-3">
                                <label for="name" class="form-label">Student Name <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control" 
                                       id="name" 
                                       name="name" 
                                       value="<?php echo htmlspecialchars($editStudent['name'] ?? ''); ?>"
                                       required 
                                       maxlength="100">
                            </div>
                            
                            <div class="form-group mb-3">
                                <label for="grade" class="form-label">Grade <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control" 
                                       id="grade" 
                                       name="grade" 
                                       value="<?php echo htmlspecialchars($editStudent['grade'] ?? ''); ?>"
                                       required 
                                       maxlength="20"
                                       placeholder="e.g., Grade 10, Grade 11">
                            </div>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i>
                                    <?php echo $currentAction === 'edit' ? 'Update Student' : 'Add Student'; ?>
                                </button>
                                <a href="students.php" class="btn btn-secondary">
                                    <i class="fas fa-times me-1"></i>
                                    Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
    
    <!-- Students List -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>Students List (<?php echo count($students); ?> students)</h5>
                </div>
                <div class="card-body">
                    <?php if ($students): ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Grade</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($students as $studentRecord): ?>
                                        <tr>
                                            <td><?php echo $studentRecord['id']; ?></td>
                                            <td><?php echo htmlspecialchars($studentRecord['name']); ?></td>
                                            <td><?php echo htmlspecialchars($studentRecord['grade']); ?></td>
                                            <td>
                                                <?php if ($student->isCalled($studentRecord['id'])): ?>
                                                    <span class="badge bg-warning">Called</span>
                                                <?php else: ?>
                                                    <span class="badge bg-success">Available</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo date('M j, Y', strtotime($studentRecord['created_at'])); ?></td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <a href="?action=edit&id=<?php echo $studentRecord['id']; ?>" 
                                                       class="btn btn-outline-primary">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" 
                                                            class="btn btn-outline-danger" 
                                                            onclick="deleteStudent(<?php echo $studentRecord['id']; ?>, '<?php echo htmlspecialchars($studentRecord['name']); ?>')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-user-graduate fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No students found. <a href="?action=add">Add the first student</a>.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete confirmation modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete student <strong id="deleteStudentName"></strong>?</p>
                <p class="text-danger"><small>This action cannot be undone.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" id="deleteForm" class="d-inline">
                    <input type="hidden" name="csrf_token" value="<?php echo Auth::generateCSRFToken(); ?>">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" id="deleteStudentId">
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function deleteStudent(id, name) {
    document.getElementById('deleteStudentId').value = id;
    document.getElementById('deleteStudentName').textContent = name;
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}
</script>

<?php include 'includes/footer.php'; ?>
