<?php
require_once '../includes/init.php';
Auth::requireRole('dean');

$call = new Call();

// Clean up expired calls
$call->removeExpired();

// Get call history
$callHistory = $call->getCallHistory(100);

$pageTitle = 'Call History';
include 'includes/header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <h1>Call History</h1>
            <p class="lead">View all student calls and activity logs.</p>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>Recent Calls (<?php echo count($callHistory); ?> records)</h5>
                </div>
                <div class="card-body">
                    <?php if ($callHistory): ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Student</th>
                                        <th>Grade</th>
                                        <th>Called By</th>
                                        <th>Called At</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($callHistory as $callRecord): ?>
                                        <?php
                                        $callTime = new DateTime($callRecord['called_at']);
                                        $now = new DateTime();
                                        $expireTime = clone $callTime;
                                        $expireTime->add(new DateInterval('PT5M'));
                                        $isExpired = $now > $expireTime;
                                        ?>
                                        <tr>
                                            <td><?php echo $callRecord['id']; ?></td>
                                            <td><?php echo htmlspecialchars($callRecord['name']); ?></td>
                                            <td><?php echo htmlspecialchars($callRecord['grade']); ?></td>
                                            <td><?php echo htmlspecialchars($callRecord['teacher_name']); ?></td>
                                            <td><?php echo $callTime->format('M j, Y g:i A'); ?></td>
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
                        <div class="text-center py-4">
                            <i class="fas fa-phone fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No call history found.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
