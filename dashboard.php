<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-number"><?= $stats['total'] ?></div>
        <div class="stat-label">Total Tugas</div>
    </div>
    <div class="stat-card">
        <div class="stat-number"><?= $stats['pending'] ?></div>
        <div class="stat-label">Pending</div>
    </div>
    <div class="stat-card">
        <div class="stat-number"><?= $stats['progress'] ?></div>
        <div class="stat-label">Dalam Progress</div>
    </div>
    <div class="stat-card">
        <div class="stat-number"><?= $stats['completed'] ?></div>
        <div class="stat-label">Selesai</div>
    </div>
</div>

<div class="content-card">
    <div class="card-header">
        <h3>Ringkasan Sistem</h3>
    </div>
    <div class="card-body">
        <h4>Selamat datang di Sistem Manajemen Tugas</h4>
        <p style="margin: 15px 0;">Sistem ini membantu Anda mengelola tugas-tugas dengan fitur-fitur berikut:</p>
        <ul style="margin-left: 20px; color: #7f8c8d;">
            <li>📊 Dashboard dengan statistik real-time</li>
            <li>➕ Tambah tugas dengan informasi lengkap</li>
            <li>✏️ Edit dan update status tugas</li>
            <li>🗑️ Hapus tugas yang tidak diperlukan</li>
            <li>🔍 Pencarian dan filter berdasarkan status/prioritas</li>
            <li>📅 Tracking deadline dan prioritas tugas</li>
        </ul>
        <p style="margin-top: 20px; padding: 15px; background: #f8f9fa; border-radius: 5px;">
            <strong>Tips:</strong> Gunakan menu "Kelola Tugas" untuk melihat semua tugas dan mengelola data tugas Anda.
        </p>
    </div>
</div>