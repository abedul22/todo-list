<!-- Task Modal -->
<div id="taskModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="modalTitle">Tambah Tugas</h2>
            <span class="close" onclick="closeModal()">&times;</span>
        </div>
        <form id="taskForm">
            <input type="hidden" id="taskId">
            <div class="form-group">
                <label for="taskTitle">Judul Tugas <span style="color: red;">*</span></label>
                <input type="text" id="taskTitle" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="taskDeadline">Deadline <span style="color: red;">*</span></label>
                <input type="date" id="taskDeadline" class="form-control" required>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="taskDescription">Deskripsi</label>
                    <textarea id="taskDescription" class="form-control" placeholder="Deskripsi tugas (opsional)"></textarea>
                </div>
                <div class="form-group">
                    <label for="taskPriority">Prioritas</label>
                    <select id="taskPriority" class="form-control" required>
                        <option value="low">Rendah</option>
                        <option value="medium" selected>Sedang</option>
                        <option value="high">Tinggi</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="taskStatus">Status</label>
                <select id="taskStatus" class="form-control" required>
                    <option value="pending" selected>Pending</option>
                    <option value="progress">Dalam Progress</option>
                    <option value="completed">Selesai</option>
                </select>
            </div>
            <div style="display: flex; gap: 10px; margin-top: 20px;">
                <button type="submit" class="btn-primary" style="flex: 1;">Simpan</button>
                <button type="button" onclick="closeModal()" style="flex: 1; background: #95a5a6; color: white; border: none; padding: 12px; border-radius: 8px; cursor: pointer;">Batal</button>
            </div>
        </form>
    </div>
</div>