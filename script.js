// Global variables
let currentTaskId = null;

// DOM Content Loaded
document.addEventListener('DOMContentLoaded', function() {
    // Menu navigation
    document.querySelectorAll('.menu-link').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Update active menu
            document.querySelectorAll('.menu-link').forEach(l => l.classList.remove('active'));
            this.classList.add('active');
            
            // Show/hide content
            const page = this.dataset.page;
            document.querySelectorAll('.page-content').forEach(content => {
                content.classList.add('hidden');
            });
            
            const targetContent = document.getElementById(page + 'Content');
            if (targetContent) {
                targetContent.classList.remove('hidden');
            }
            
            if (page === 'tasks') {
                loadTasks();
            } else if (page === 'dashboard') {
                loadStats();
            }
        });
    });

    // Search and filter functionality
    const searchInput = document.getElementById('searchTasks');
    const statusFilter = document.getElementById('filterStatus');
    const priorityFilter = document.getElementById('filterPriority');

    if (searchInput) {
        searchInput.addEventListener('input', debounce(handleSearch, 500));
    }
    if (statusFilter) {
        statusFilter.addEventListener('change', handleSearch);
    }
    if (priorityFilter) {
        priorityFilter.addEventListener('change', handleSearch);
    }

    // Task form submission
    const taskForm = document.getElementById('taskForm');
    if (taskForm) {
        taskForm.addEventListener('submit', handleTaskSubmit);
    }

    // Modal close on outside click
    window.addEventListener('click', function(e) {
        const modal = document.getElementById('taskModal');
        if (e.target === modal) {
            closeModal();
        }
    });

    // Set minimum date for deadline input
    const deadlineInput = document.getElementById('taskDeadline');
    if (deadlineInput) {
        deadlineInput.min = new Date().toISOString().split('T')[0];
    }
});

// Debounce function for search
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Handle search and filters
function handleSearch() {
    const search = document.getElementById('searchTasks')?.value || '';
    const status = document.getElementById('filterStatus')?.value || '';
    const priority = document.getElementById('filterPriority')?.value || '';
    
    // Update URL with search parameters
    const url = new URL(window.location);
    if (search) url.searchParams.set('search', search);
    else url.searchParams.delete('search');
    
    if (status) url.searchParams.set('status', status);
    else url.searchParams.delete('status');
    
    if (priority) url.searchParams.set('priority', priority);
    else url.searchParams.delete('priority');
    
    window.history.pushState({}, '', url);
    
    // Reload tasks with filters
    loadTasks();
}

// Load tasks via AJAX
function loadTasks() {
    const search = document.getElementById('searchTasks')?.value || '';
    const status = document.getElementById('filterStatus')?.value || '';
    const priority = document.getElementById('filterPriority')?.value || '';
    
    const params = new URLSearchParams({
        action: 'search_tasks',
        search: search,
        status: status,
        priority: priority
    });
    
    fetch(`ajax.php?${params}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderTasks(data.data);
                loadStats(); // Update stats after loading tasks
            } else {
                showNotification('Error loading tasks: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error loading tasks', 'error');
        });
}

function renderTasks(tasks) {
    const tbody = document.querySelector('#tasksContent tbody');
    if (!tbody) return;

    if (tasks.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" style="text-align: center; padding: 40px; color: #7f8c8d;">
                    Tidak ada tugas yang ditemukan.
                </td>
            </tr>
        `;
        return;
    }

    tbody.innerHTML = tasks.map(task => `
        <tr>
            <td>${formatDate(task.created_at)}</td>
            <td><strong>${escapeHtml(task.title)}</strong></td>
            <td>${escapeHtml(task.description.substring(0, 50))}${task.description.length > 50 ? '...' : ''}</td>
            <td>${formatDate(task.deadline)}</td>
            <td><span class="priority-${task.priority}">${getPriorityText(task.priority)}</span></td>
            <td><span class="badge badge-${task.status}">${getStatusText(task.status)}</span></td>
            <td>
                <div class="btn-group">
                    <button class="btn-sm btn-edit" onclick="editTask(${task.id})">Edit</button>
                    <button class="btn-sm btn-delete" onclick="deleteTask(${task.id})">Hapus</button>
                </div>
            </td>
        </tr>
    `).join('');
}

// Load statistics
function loadStats() {
    fetch('ajax.php?action=get_stats')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateStats(data.data);
            }
        })
        .catch(error => {
            console.error('Error loading stats:', error);
        });
}

// Update statistics display
function updateStats(stats) {
    const elements = {
        'totalTasks': stats.total || 0,
        'pendingTasks': stats.pending || 0,
        'progressTasks': stats.progress || 0,
        'completedTasks': stats.completed || 0
    };
    
    Object.entries(elements).forEach(([id, value]) => {
        const element = document.getElementById(id);
        if (element) {
            element.textContent = value;
        }
    });
}

// Open modal for add/edit
function openModal(mode, taskId = null) {
    const modal = document.getElementById('taskModal');
    const modalTitle = document.getElementById('modalTitle');
    const form = document.getElementById('taskForm');
    
    if (mode === 'add') {
        modalTitle.textContent = 'Tambah Tugas';
        form.reset();
        document.getElementById('taskId').value = '';
        currentTaskId = null;
        
        // Set default date to today
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('taskDeadline').value = today;
    } else if (mode === 'edit') {
        modalTitle.textContent = 'Edit Tugas';
        currentTaskId = taskId;
        loadTaskForEdit(taskId);
    }
    
    modal.style.display = 'block';
}

// Load task data for editing
function loadTaskForEdit(taskId) {
    fetch(`ajax.php?action=get_task&id=${taskId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const task = data.data;
                document.getElementById('taskId').value = task.id;
                document.getElementById('taskTitle').value = task.title;
                document.getElementById('taskDescription').value = task.description;
                document.getElementById('taskDeadline').value = task.deadline;
                document.getElementById('taskPriority').value = task.priority;
                document.getElementById('taskStatus').value = task.status;
            } else {
                showNotification('Error loading task: ' + data.message, 'error');
                closeModal();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error loading task', 'error');
            closeModal();
        });
}

// Close modal
function closeModal() {
    document.getElementById('taskModal').style.display = 'none';
    currentTaskId = null;
}

// Handle task form submission
function handleTaskSubmit(e) {
    e.preventDefault();
    
    const formData = new FormData();
    const taskData = {
        title: document.getElementById('taskTitle').value.trim(),
        description: document.getElementById('taskDescription').value.trim(),
        deadline: document.getElementById('taskDeadline').value,
        priority: document.getElementById('taskPriority').value,
        status: document.getElementById('taskStatus').value
    };
    
    // Validation
    if (!taskData.title) {
        showNotification('Judul tugas harus diisi', 'error');
        return;
    }
    
    if (!taskData.deadline) {
        showNotification('Deadline harus diisi', 'error');
        return;
    }
    
    // Check if deadline is not in the past
    const selectedDate = new Date(taskData.deadline);
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    
    if (selectedDate < today) {
        showNotification('Deadline tidak boleh di masa lalu', 'error');
        return;
    }
    
    // Prepare form data
    Object.entries(taskData).forEach(([key, value]) => {
        formData.append(key, value);
    });
    
    if (currentTaskId) {
        formData.append('action', 'update_task');
        formData.append('id', currentTaskId);
    } else {
        formData.append('action', 'add_task');
    }
    
    // Submit form
    fetch('ajax.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            closeModal();
            loadTasks();
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Terjadi kesalahan saat menyimpan tugas', 'error');
    });
}

// Edit task
function editTask(taskId) {
    openModal('edit', taskId);
}

// Delete task
function deleteTask(taskId) {
    if (!confirm('Apakah Anda yakin ingin menghapus tugas ini?')) {
        return;
    }
    
    const formData = new FormData();
    formData.append('action', 'delete_task');
    formData.append('id', taskId);
    
    fetch('ajax.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            loadTasks();
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Terjadi kesalahan saat menghapus tugas', 'error');
    });
}

// Utility functions
function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, m => map[m]);
}

function formatDate(dateString) {
    const date = new Date(dateString);
    const day = date.getDate().toString().padStart(2, '0');
    const month = (date.getMonth() + 1).toString().padStart(2, '0');
    const year = date.getFullYear();
    return `${day}/${month}/${year}`;
}

function getPriorityText(priority) {
    const priorities = {
        'low': 'Rendah',
        'medium': 'Sedang',
        'high': 'Tinggi'
    };
    return priorities[priority] || priority;
}

function getStatusText(status) {
    const statuses = {
        'pending': 'Pending',
        'progress': 'Dalam Progress',
        'completed': 'Selesai'
    };
    return statuses[status] || status;
}

function showNotification(message, type) {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `alert alert-${type === 'success' ? 'success' : 'error'}`;
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        min-width: 300px;
        animation: slideIn 0.3s ease;
    `;
    
    // Add animation styles
    if (!document.getElementById('notificationStyles')) {
        const style = document.createElement('style');
        style.id = 'notificationStyles';
        style.textContent = `
            @keyframes slideIn {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
            @keyframes slideOut {
                from { transform: translateX(0); opacity: 1; }
                to { transform: translateX(100%); opacity: 0; }
            }
        `;
        document.head.appendChild(style);
    }
    
    document.body.appendChild(notification);
    
    // Remove notification after 3 seconds
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 3000);
}