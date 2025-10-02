<?php
// Get filter parameters
$search = $_GET['search'] ?? '';
$statusFilter = $_GET['status'] ?? '';
$priorityFilter = $_GET['priority'] ?? '';

// Get filtered tasks
$filteredTasks = getTasks($search, $statusFilter, $priorityFilter);
?>

<div class="content-card">
    <div class="card-header">
        <h3>Daftar Tugas</h3>
        <button class="btn-add" onclick="openModal('add')">Tambah Tugas</button>
    </div>
    <div class="card-body">
        <div class="filters">
            <div class="search-box">
                <input type="text" id="searchTasks" class="form-control" placeholder="Cari tugas..." 
                       value="<?= htmlspecialchars($search) ?>">
            </div>
            <select id="filterStatus" class="filter-select">
                <option value="">Semua Status</option>
                <option value="pending" <?= $statusFilter === 'pending' ? 'selected' : '' ?>>Pending</option>
                <option value="progress" <?= $statusFilter === 'progress' ? 'selected' : '' ?>>Dalam Progress</option>
                <option value="completed" <?= $statusFilter === 'completed' ? 'selected' : '' ?>>Selesai</option>
            </select>
            <select id="filterPriority" class="filter-select">
                <option value="">Semua Prioritas</option>
                <option value="high" <?= $priorityFilter === 'high' ? 'selected' : '' ?>>Tinggi</option>
                <option value="medium" <?= $priorityFilter === 'medium' ? 'selected' : '' ?>>Sedang</option>
                <option value="low" <?= $priorityFilter === 'low' ? 'selected' : '' ?>>Rendah</option>
            </select>
        </div>

        <?php if (empty($filteredTasks)): ?>
            <div style="text-align: center; padding: 40px; color: #7f8c8d;">
                <p>Tidak ada tugas yang ditemukan.</p>
                <?php if (!empty($search) || !empty($statusFilter) || !empty($priorityFilter)): ?>
                    <p>Coba ubah filter pencarian Anda.</p>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <table class="tasks-table">
                <thead>
                    <tr>
                        <th>Tanggal Pembuatan</th> <!-- Kolom baru -->
                        <th>Judul</th>
                        <th>Deskripsi</th> <!-- Pindahkan deskripsi ke sini -->
                        <th>Deadline</th>
                        <th>Prioritas</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($filteredTasks as $task): ?>
                        <tr>
                            <td><?= formatDate($task['created_at']) ?></td> <!-- Tampilkan tanggal pembuatan -->
                            <td><strong><?= htmlspecialchars($task['title']) ?></strong></td>
                            <td>
                                <?php 
                                $description = htmlspecialchars($task['description']);
                                echo strlen($description) > 50 ? substr($description, 0, 50) . '...' : $description;
                                ?>
                            </td>
                            <td><?= formatDate($task['deadline']) ?></td>
                            <td>
                                <span class="priority-<?= $task['priority'] ?>">
                                    <?= getPriorityText($task['priority']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-<?= $task['status'] ?>">
                                    <?= getStatusText($task['status']) ?>
                                </span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <button class="btn-sm btn-edit" onclick="editTask(<?= $task['id'] ?>)">Edit</button>
                                    <button class="btn-sm btn-delete" onclick="deleteTask(<?= $task['id'] ?>)">Hapus</button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>
