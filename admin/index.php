<?php
require_once '../includes/init.php';
Auth::requireRole('dean');

$user = Auth::getUser();
$student = new Student();
$call = new Call();

// Get statistics
$totalStudents = $student->getCount();
$activeCalls = $call->getCallsCount();

$pageTitle = 'Admin Dashboard';
include 'includes/header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <h1>Admin Dashboard</h1>
            <p class="lead">Welcome back, <?php echo htmlspecialchars($user['username']); ?>!</p>
        </div>
    </div>
    
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body text-center">
                    <h2 class="text-primary"><?php echo $totalStudents; ?></h2>
                    <p class="card-text">Total Students</p>
                    <a href="students.php" class="btn btn-outline-primary">Manage Students</a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body text-center">
                    <h2 class="text-success"><?php echo $activeCalls; ?></h2>
                    <p class="card-text">Currently Called</p>
                    <a href="../display/" target="_blank" class="btn btn-outline-success">View Display</a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="d-grid">
                                <a href="students.php?action=add" class="btn btn-primary">
                                    Add New Student
                                </a>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="d-grid">
                                <a href="users.php" class="btn btn-secondary">
                                    Manage Users
                                </a>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="d-grid">
                                <a href="calls.php" class="btn btn-info">
                                    View Call History
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Activity -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>Recent Activity</h5>
                </div>
                <div class="card-body">
                    <?php
                    $recentCalls = $call->getCallHistory(10);
                    if ($recentCalls):
                    ?>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Student</th>
                                        <th>Grade</th>
                                        <th>Called By</th>
                                        <th>Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentCalls as $callRecord): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($callRecord['name']); ?></td>
                                            <td><?php echo htmlspecialchars($callRecord['grade']); ?></td>
                                            <td><?php echo htmlspecialchars($callRecord['teacher_name']); ?></td>
                                            <td><?php echo date('M j, Y g:i A', strtotime($callRecord['called_at'])); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">No recent activity.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
