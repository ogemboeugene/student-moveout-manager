<?php
require_once '../includes/init.php';
Auth::requireRole('dean');

$userModel = new User();
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
                $username = Validator::sanitizeInput($_POST['username'] ?? '');
                $password = $_POST['password'] ?? '';
                $role = $_POST['role'] ?? '';
                
                $errors = Validator::validateUserData($username, $role, 'active', $password);
                
                if (empty($errors)) {
                    if ($userModel->usernameExists($username)) {
                        $message = 'Username already exists.';
                        $messageType = 'danger';
                    } else {
                        if ($userModel->create($username, $password, $role)) {
                            $message = 'User added successfully.';
                            $messageType = 'success';
                        } else {
                            $message = 'Failed to add user.';
                            $messageType = 'danger';
                        }
                    }
                } else {
                    $message = implode('<br>', $errors);
                    $messageType = 'danger';
                }
                break;
                
            case 'edit':
                $id = (int)($_POST['id'] ?? 0);
                $username = Validator::sanitizeInput($_POST['username'] ?? '');
                $role = $_POST['role'] ?? '';
                $status = $_POST['status'] ?? '';
                
                $errors = Validator::validateUserData($username, $role, $status);
                
                if (empty($errors)) {
                    if ($userModel->usernameExists($username, $id)) {
                        $message = 'Username already exists.';
                        $messageType = 'danger';
                    } else {
                        if ($userModel->update($id, $username, $role, $status)) {
                            $message = 'User updated successfully.';
                            $messageType = 'success';
                        } else {
                            $message = 'Failed to update user.';
                            $messageType = 'danger';
                        }
                    }
                } else {
                    $message = implode('<br>', $errors);
                    $messageType = 'danger';
                }
                break;
                
            case 'change_password':
                $id = (int)($_POST['id'] ?? 0);
                $password = $_POST['password'] ?? '';
                
                $errors = Validator::validateUserData('dummy', 'dean', 'active', $password);
                
                if (empty($errors)) {
                    if ($userModel->updatePassword($id, $password)) {
                        $message = 'Password updated successfully.';
                        $messageType = 'success';
                    } else {
                        $message = 'Failed to update password.';
                        $messageType = 'danger';
                    }
                } else {
                    $message = implode('<br>', $errors);
                    $messageType = 'danger';
                }
                break;
                
            case 'delete':
                $id = (int)($_POST['id'] ?? 0);
                $currentUser = Auth::getUser();
                
                if ($id == $currentUser['id']) {
                    $message = 'You cannot delete your own account.';
                    $messageType = 'danger';
                } else {
                    if ($userModel->delete($id)) {
                        $message = 'User deleted successfully.';
                        $messageType = 'success';
                    } else {
                        $message = 'Failed to delete user.';
                        $messageType = 'danger';
                    }
                }
                break;
        }
    }
}

// Get current action
$currentAction = $_GET['action'] ?? 'list';
$editUser = null;

if ($currentAction === 'edit' && isset($_GET['id'])) {
    $editUser = $userModel->getById((int)$_GET['id']);
    if (!$editUser) {
        $currentAction = 'list';
        $message = 'User not found.';
        $messageType = 'danger';
    }
}

// Get users list
$users = $userModel->getAll();
$currentUser = Auth::getUser();

$pageTitle = 'Manage Users';
include 'includes/header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>Manage Users</h1>
                <a href="?action=add" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>
                    Add User
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
                        <h5><?php echo $currentAction === 'edit' ? 'Edit User' : 'Add New User'; ?></h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" data-validate>
                            <input type="hidden" name="csrf_token" value="<?php echo Auth::generateCSRFToken(); ?>">
                            <input type="hidden" name="action" value="<?php echo $currentAction; ?>">
                            <?php if ($editUser): ?>
                                <input type="hidden" name="id" value="<?php echo $editUser['id']; ?>">
                            <?php endif; ?>
                            
                            <div class="form-group mb-3">
                                <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control" 
                                       id="username" 
                                       name="username" 
                                       value="<?php echo htmlspecialchars($editUser['username'] ?? ''); ?>"
                                       required 
                                       maxlength="50">
                            </div>
                            
                            <?php if ($currentAction === 'add'): ?>
                                <div class="form-group mb-3">
                                    <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                    <input type="password" 
                                           class="form-control" 
                                           id="password" 
                                           name="password" 
                                           required 
                                           minlength="6">
                                </div>
                            <?php endif; ?>
                            
                            <div class="form-group mb-3">
                                <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                                <select class="form-select" id="role" name="role" required>
                                    <option value="">Select Role</option>
                                    <option value="dean" <?php echo ($editUser['role'] ?? '') === 'dean' ? 'selected' : ''; ?>>Dean (Admin)</option>
                                    <option value="teacher" <?php echo ($editUser['role'] ?? '') === 'teacher' ? 'selected' : ''; ?>>Teacher</option>
                                </select>
                            </div>
                            
                            <?php if ($currentAction === 'edit'): ?>
                                <div class="form-group mb-3">
                                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                    <select class="form-select" id="status" name="status" required>
                                        <option value="active" <?php echo ($editUser['status'] ?? '') === 'active' ? 'selected' : ''; ?>>Active</option>
                                        <option value="inactive" <?php echo ($editUser['status'] ?? '') === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                    </select>
                                </div>
                            <?php endif; ?>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i>
                                    <?php echo $currentAction === 'edit' ? 'Update User' : 'Add User'; ?>
                                </button>
                                <a href="users.php" class="btn btn-secondary">
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
    
    <!-- Users List -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>Users List (<?php echo count($users); ?> users)</h5>
                </div>
                <div class="card-body">
                    <?php if ($users): ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Username</th>
                                        <th>Role</th>
                                        <th>Status</th>
                                        <th>Last Login</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users as $userRecord): ?>
                                        <tr <?php echo $userRecord['id'] == $currentUser['id'] ? 'class="table-info"' : ''; ?>>
                                            <td><?php echo $userRecord['id']; ?></td>
                                            <td>
                                                <?php echo htmlspecialchars($userRecord['username']); ?>
                                                <?php if ($userRecord['id'] == $currentUser['id']): ?>
                                                    <small class="text-muted">(You)</small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?php echo $userRecord['role'] === 'dean' ? 'primary' : 'secondary'; ?>">
                                                    <?php echo ucfirst($userRecord['role']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?php echo $userRecord['status'] === 'active' ? 'success' : 'danger'; ?>">
                                                    <?php echo ucfirst($userRecord['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php 
                                                if ($userRecord['last_login']) {
                                                    echo date('M j, Y g:i A', strtotime($userRecord['last_login']));
                                                } else {
                                                    echo '<span class="text-muted">Never</span>';
                                                }
                                                ?>
                                            </td>
                                            <td><?php echo date('M j, Y', strtotime($userRecord['created_at'])); ?></td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <a href="?action=edit&id=<?php echo $userRecord['id']; ?>" 
                                                       class="btn btn-outline-primary">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" 
                                                            class="btn btn-outline-warning" 
                                                            onclick="changePassword(<?php echo $userRecord['id']; ?>, '<?php echo htmlspecialchars($userRecord['username']); ?>')">
                                                        <i class="fas fa-key"></i>
                                                    </button>
                                                    <?php if ($userRecord['id'] != $currentUser['id']): ?>
                                                        <button type="button" 
                                                                class="btn btn-outline-danger" 
                                                                onclick="deleteUser(<?php echo $userRecord['id']; ?>, '<?php echo htmlspecialchars($userRecord['username']); ?>')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No users found.</p>
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
                <p>Are you sure you want to delete user <strong id="deleteUserName"></strong>?</p>
                <p class="text-danger"><small>This action cannot be undone.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" id="deleteForm" class="d-inline">
                    <input type="hidden" name="csrf_token" value="<?php echo Auth::generateCSRFToken(); ?>">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" id="deleteUserId">
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Change Password Modal -->
<div class="modal fade" id="passwordModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Change Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?php echo Auth::generateCSRFToken(); ?>">
                    <input type="hidden" name="action" value="change_password">
                    <input type="hidden" name="id" id="passwordUserId">
                    
                    <p>Change password for user <strong id="passwordUserName"></strong>:</p>
                    
                    <div class="form-group">
                        <label for="password" class="form-label">New Password</label>
                        <input type="password" class="form-control" name="password" required minlength="6">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Change Password</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function deleteUser(id, username) {
    document.getElementById('deleteUserId').value = id;
    document.getElementById('deleteUserName').textContent = username;
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

function changePassword(id, username) {
    document.getElementById('passwordUserId').value = id;
    document.getElementById('passwordUserName').textContent = username;
    const modal = new bootstrap.Modal(document.getElementById('passwordModal'));
    modal.show();
}
</script>

<?php include 'includes/footer.php'; ?>
