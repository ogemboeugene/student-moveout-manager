<?php
require_once '../includes/init.php';

$call = new Call();

// Clean up expired calls
$call->removeExpired();

// Get active calls
$activeCalls = $call->getActiveCalls();

$pageTitle = 'Display Screen';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - Student Move-Out Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <meta http-equiv="refresh" content="10">
</head>
<body class="display-container">
    <div class="container-fluid">
        <div class="display-header">
            <h1>Student Move-Out</h1>
            <p class="subtitle">Students Called for Move-Out Process</p>
            <div class="row mt-3">
                <div class="col-6">
                    <small id="lastUpdate">Last Updated: <?php echo date('g:i:s A'); ?></small>
                </div>
                <div class="col-6 text-end">
                    <small id="callCount"><?php echo count($activeCalls); ?> Active Call<?php echo count($activeCalls) !== 1 ? 's' : ''; ?></small>
                </div>
            </div>
        </div>
        
        <div id="calledStudents">
            <?php if ($activeCalls): ?>
                <div class="row">
                    <?php foreach ($activeCalls as $call): ?>
                        <?php
                        $timeElapsed = $call['elapsed_seconds'];
                        $remainingTime = 300 - $timeElapsed; // 5 minutes = 300 seconds
                        $progressPercent = min(100, ($timeElapsed / 300) * 100);
                        ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="student-card">
                                <div class="student-name">
                                    <?php echo htmlspecialchars($call['name']); ?>
                                </div>
                                <div class="student-grade">
                                    <?php echo htmlspecialchars($call['grade']); ?>
                                </div>
                                <div class="student-time">
                                    Called <?php echo $timeElapsed; ?>s ago by <?php echo htmlspecialchars($call['teacher_name']); ?>
                                </div>
                                
                                <!-- Progress bar -->
                                <div class="progress mt-3" style="height: 8px;">
                                    <div class="progress-bar bg-warning" 
                                         style="width: <?php echo $progressPercent; ?>%"
                                         role="progressbar"></div>
                                </div>
                                
                                <?php if ($remainingTime > 0): ?>
                                    <small class="text-muted">
                                        Expires in <?php echo gmdate('i:s', $remainingTime); ?>
                                    </small>
                                <?php else: ?>
                                    <small class="text-warning">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        Expired
                                    </small>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="no-students">
                    <i class="fas fa-user-clock"></i>
                    <h3>No Students Called</h3>
                    <p>Students will appear here when called by teachers.</p>
                    <small class="text-muted">
                        This screen refreshes automatically every 10 seconds.
                    </small>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Dark Mode Toggle -->
    <button class="dark-mode-toggle" onclick="DarkModeManager.toggle()" title="Toggle Dark Mode">
        🌙
    </button>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/main.js"></script>
    
    <script>
        // Auto-refresh functionality with AJAX
        let autoRefresh;
        let isRefreshing = false;
        
        function refreshDisplay() {
            if (isRefreshing) return;
            isRefreshing = true;
            
            AjaxHelper.get('../api/called-students.php')
                .then(data => {
                    updateDisplay(data.students);
                    updateLastUpdate();
                })
                .catch(error => {
                    console.error('Refresh failed:', error);
                    // Fall back to page refresh on error
                    setTimeout(() => {
                        window.location.reload();
                    }, 5000);
                })
                .finally(() => {
                    isRefreshing = false;
                });
        }
        
        function updateDisplay(students) {
            const container = document.getElementById('calledStudents');
            const countElement = document.getElementById('callCount');
            
            if (students.length === 0) {
                container.innerHTML = `
                    <div class="no-students">
                        <i class="fas fa-user-clock"></i>
                        <h3>No Students Called</h3>
                        <p>Students will appear here when called by teachers.</p>
                        <small class="text-muted">
                            This screen refreshes automatically every 10 seconds.
                        </small>
                    </div>
                `;
            } else {
                let html = '<div class="row">';
                
                students.forEach(student => {
                    const timeElapsed = student.elapsed_seconds;
                    const remainingTime = 300 - timeElapsed;
                    const progressPercent = Math.min(100, (timeElapsed / 300) * 100);
                    
                    html += `
                        <div class="col-md-6 col-lg-4">
                            <div class="student-card">
                                <div class="student-name">
                                    ${escapeHtml(student.name)}
                                </div>
                                <div class="student-grade">
                                    ${escapeHtml(student.grade)}
                                </div>
                                <div class="student-time">
                                    Called ${timeElapsed}s ago by ${escapeHtml(student.teacher_name)}
                                </div>
                                
                                <div class="progress mt-3" style="height: 8px;">
                                    <div class="progress-bar bg-warning" 
                                         style="width: ${progressPercent}%"
                                         role="progressbar"></div>
                                </div>
                                
                                ${remainingTime > 0 ? 
                                    `<small class="text-muted">
                                        Expires in ${formatTime(remainingTime)}
                                    </small>` :
                                    `<small class="text-warning">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        Expired
                                    </small>`
                                }
                            </div>
                        </div>
                    `;
                });
                
                html += '</div>';
                container.innerHTML = html;
            }
            
            // Update count
            countElement.textContent = `${students.length} Active Call${students.length !== 1 ? 's' : ''}`;
        }
        
        function updateLastUpdate() {
            const now = new Date();
            document.getElementById('lastUpdate').textContent = 
                `Last Updated: ${now.toLocaleTimeString()}`;
        }
        
        function formatTime(seconds) {
            const minutes = Math.floor(seconds / 60);
            const remainingSeconds = seconds % 60;
            return `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`;
        }
        
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
        
        // Initialize auto-refresh
        document.addEventListener('DOMContentLoaded', function() {
            // Start auto-refresh every 10 seconds
            autoRefresh = new AutoRefresh(refreshDisplay, 10000);
            autoRefresh.start();
            
            // Pause refresh when page is not visible
            document.addEventListener('visibilitychange', function() {
                if (document.hidden) {
                    autoRefresh.stop();
                } else {
                    autoRefresh.start();
                }
            });
            
            // Initialize dark mode
            DarkModeManager.init();
        });
        
        // Handle page unload
        window.addEventListener('beforeunload', function() {
            if (autoRefresh) {
                autoRefresh.stop();
            }
        });
    </script>
</body>
</html>
