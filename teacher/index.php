<?php
require_once '../includes/init.php';
Auth::requireRole('teacher');

$user = Auth::getUser();
$student = new Student();
$call = new Call();

// Get statistics
$totalStudents = $student->getCount();
$activeCalls = $call->getCallsCount();

$pageTitle = 'Teacher Dashboard';
include 'includes/header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <h1>Teacher Dashboard</h1>
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
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body text-center">
                    <h2 class="text-success calls-count"><?php echo $activeCalls; ?></h2>
                    <p class="card-text">Currently Called</p>
                    <a href="../display/" target="_blank" class="btn btn-outline-success">View Display</a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Search Students -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>Search & Call Students</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="searchInput" class="form-label">Search by name or grade</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="searchInput" 
                                       placeholder="Enter student name or grade..."
                                       autocomplete="off">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">&nbsp;</label>
                                <div>
                                    <button type="button" class="btn btn-primary" onclick="searchStudents()">
                                        <i class="fas fa-search me-1"></i>
                                        Search
                                    </button>
                                    <button type="button" class="btn btn-secondary" onclick="showAllStudents()">
                                        <i class="fas fa-list me-1"></i>
                                        Show All
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Search Results -->
                    <div id="searchResults">
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-search fa-2x mb-2"></i>
                            <p>Search for students to view and call them.</p>
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
                    <h5>My Recent Calls</h5>
                </div>
                <div class="card-body">
                    <?php
                    $db = Database::getInstance();
                    $stmt = $db->prepare("
                        SELECT c.*, s.name, s.grade
                        FROM calls c
                        JOIN students s ON c.student_id = s.id
                        WHERE c.teacher_id = ?
                        ORDER BY c.called_at DESC
                        LIMIT 10
                    ");
                    $stmt->execute([$user['id']]);
                    $myCalls = $stmt->fetchAll();
                    ?>
                    
                    <?php if ($myCalls): ?>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Student</th>
                                        <th>Grade</th>
                                        <th>Called At</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($myCalls as $callRecord): ?>
                                        <?php
                                        $callTime = new DateTime($callRecord['called_at']);
                                        $now = new DateTime();
                                        $expireTime = clone $callTime;
                                        $expireTime->add(new DateInterval('PT5M'));
                                        $isExpired = $now > $expireTime;
                                        ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($callRecord['name']); ?></td>
                                            <td><?php echo htmlspecialchars($callRecord['grade']); ?></td>
                                            <td><?php echo $callTime->format('M j g:i A'); ?></td>
                                            <td>
                                                <?php if ($isExpired): ?>
                                                    <span class="badge bg-secondary">Expired</span>
                                                <?php else: ?>
                                                    <span class="badge bg-success">Active</span>
                                                <?php endif; ?>
                                            </td>
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
