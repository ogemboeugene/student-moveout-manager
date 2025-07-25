    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/main.js"></script>
    
    <!-- Teacher specific scripts -->
    <script>
        let searchDebouncer;
        
        // Search functionality
        function searchStudents() {
            const query = document.getElementById('searchInput').value.trim();
            if (!query) {
                NotificationManager.warning('Please enter a search term.');
                return;
            }
            
            performSearch(query);
        }
        
        function showAllStudents() {
            document.getElementById('searchInput').value = '';
            performSearch('');
        }
        
        function performSearch(query) {
            const resultsContainer = document.getElementById('searchResults');
            
            // Show loading
            resultsContainer.innerHTML = '<div class="text-center py-4"><div class="spinner"></div> Searching...</div>';
            
            const url = '../api/search-students.php' + (query ? '?q=' + encodeURIComponent(query) : '');
            
            AjaxHelper.get(url)
                .then(data => {
                    displaySearchResults(data.students);
                })
                .catch(error => {
                    console.error('Search failed:', error);
                    resultsContainer.innerHTML = '<div class="alert alert-danger">Search failed. Please try again.</div>';
                });
        }
        
        function displaySearchResults(students) {
            const resultsContainer = document.getElementById('searchResults');
            
            if (students.length === 0) {
                resultsContainer.innerHTML = `
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-user-slash fa-2x mb-2"></i>
                        <p>No students found.</p>
                    </div>
                `;
                return;
            }
            
            let html = '<div class="row">';
            
            students.forEach(student => {
                const isCalled = student.is_called;
                html += `
                    <div class="col-md-6 col-lg-4 mb-3">
                        <div class="card ${isCalled ? 'border-warning' : ''}">
                            <div class="card-body">
                                <h6 class="card-title">${escapeHtml(student.name)}</h6>
                                <p class="card-text text-muted">${escapeHtml(student.grade)}</p>
                                ${isCalled ? 
                                    '<span class="badge bg-warning text-dark mb-2">Already Called</span>' : 
                                    ''
                                }
                                <div class="d-grid">
                                    <button type="button" 
                                            class="btn btn-${isCalled ? 'secondary' : 'success'}" 
                                            onclick="callStudent(${student.id}, '${escapeHtml(student.name)}')"
                                            ${isCalled ? 'disabled' : ''}>
                                        <i class="fas fa-phone me-1"></i>
                                        ${isCalled ? 'Called' : 'Call Student'}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            html += '</div>';
            resultsContainer.innerHTML = html;
        }
        
        function callStudent(studentId, studentName) {
            if (!confirm(`Are you sure you want to call ${studentName}?`)) {
                return;
            }
            
            const button = event.target;
            LoadingManager.show(button, 'Calling...');
            
            AjaxHelper.post('../api/call-student.php', { student_id: studentId })
                .then(data => {
                    if (data.success) {
                        NotificationManager.success(`${studentName} has been called successfully!`);
                        // Refresh search results
                        const query = document.getElementById('searchInput').value.trim();
                        performSearch(query);
                        // Update stats
                        refreshStats();
                    } else {
                        NotificationManager.error(data.message || 'Failed to call student.');
                    }
                })
                .catch(error => {
                    console.error('Call failed:', error);
                    NotificationManager.error('Failed to call student. Please try again.');
                })
                .finally(() => {
                    LoadingManager.hide(button);
                });
        }
        
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
        
        // Auto-refresh stats
        function refreshStats() {
            AjaxHelper.get('../api/stats.php')
                .then(data => {
                    const callsElement = document.querySelector('.calls-count');
                    if (callsElement) {
                        callsElement.textContent = data.active_calls;
                    }
                })
                .catch(error => {
                    console.log('Stats refresh failed:', error);
                });
        }
        
        // Initialize search with debouncing
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            searchDebouncer = new SearchDebouncer(performSearch, 500);
            
            searchInput.addEventListener('input', function() {
                const query = this.value.trim();
                if (query.length >= 2 || query.length === 0) {
                    searchDebouncer.search(query);
                }
            });
            
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    searchStudents();
                }
            });
            
            // Refresh stats every 30 seconds
            setInterval(refreshStats, 30000);
        });
    </script>
</body>
</html>
