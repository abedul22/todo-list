Admin To-Do List System
Sistem manajemen tugas berbasis web dengan PHP, MySQL, dan JavaScript. Website ini memiliki fitur lengkap untuk mengelola tugas-tugas dengan antarmuka yang modern dan responsif.

🚀 Fitur Utama
Autentikasi Admin: Login system dengan session management
Dashboard Statistik: Real-time statistics dengan card layout
CRUD Tugas Lengkap: Create, Read, Update, Delete tugas
Form Input Komprehensif: Judul, deskripsi, deadline, prioritas, status
Pencarian & Filter: Search berdasarkan judul/deskripsi, filter status dan prioritas
Badge Status: Visual indicators untuk status dan prioritas
Design Responsif: Mobile-friendly dengan sidebar navigation
AJAX Operations: Operasi tanpa refresh halaman
Notifikasi Real-time: Success/error notifications
📋 Requirements
PHP: 7.4 atau lebih tinggi
MySQL: 5.7 atau lebih tinggi / MariaDB 10.3+
Web Server: Apache/Nginx
Browser: Modern browsers (Chrome, Firefox, Safari, Edge)
🛠️ Instalasi
1. Download & Extract
bash
git clone https://github.com/yourusername/admin-todo-list.git
cd admin-todo-list
2. Setup Database
Buat database MySQL baru
Import file install.sql ke database:
sql
mysql -u username -p database_name < install.sql
3. Konfigurasi Database
Edit file includes/database.php:

php
define('DB_HOST', 'localhost');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
define('DB_NAME', 'todo_admin');
4. Upload Files
Upload semua file ke web server Anda (htdocs/www/public_html)

5. Set Permissions
bash
chmod 755 assets/
chmod 755 includes/
chmod 755 pages/
📁 Struktur File
admin-todo-list/
├── index.php              # Main entry point
├── ajax.php               # AJAX handler for CRUD operations
├── install.sql            # Database setup script
├── README.md              # Documentation
├── assets/
│   ├── css/
│   │   └── style.css      # Main stylesheet
│   └── js/
│       └── script.js      # Main JavaScript file
├── includes/
│   ├── database.php       # Database connection
│   ├── functions.php      # Core functions
│   ├── sidebar.php        # Sidebar navigation
│   ├── topbar.php         # Top navigation bar
│   └── modal.php          # Task modal form
└── pages/
    ├── dashboard.php      # Dashboard content
    └── tasks.php          # Tasks management page
🎯 Cara Penggunaan
Login
URL: http://yoursite.com/
Username: admin
Password: admin
Dashboard
Lihat statistik tugas (Total, Pending, Progress, Completed)
Overview sistem dan tips penggunaan
Mengelola Tugas
Tambah Tugas: Klik tombol "Tambah Tugas"
Isi judul tugas (required)
Tambahkan deskripsi (optional)
Pilih deadline (required)
Set prioritas: Rendah/Sedang/Tinggi
Set status: Pending/Progress/Completed
Edit Tugas: Klik tombol "Edit" pada tabel
Ubah informasi tugas
Update status sesuai progress
Hapus Tugas: Klik tombol "Hapus"
Konfirmasi penghapusan
Data akan dihapus permanent
Pencarian:
Gunakan search box untuk cari berdasarkan judul/deskripsi
Filter berdasarkan status atau prioritas
Kombinasi multiple filter
🎨 Kustomisasi
Mengubah Theme
Edit file assets/css/style.css:

css
/* Primary Color */
background: linear-gradient(135deg, #YOUR_COLOR, #YOUR_COLOR2);

/* Card Shadow */
box-shadow: 0 5px 15px rgba(0,0,0,0.1);
Menambah Field
Update database schema
Modifikasi includes/functions.php
Update form di includes/modal.php
Sesuaikan JavaScript di assets/js/script.js
Custom Notifications
Customize notification di JavaScript:

javascript
function showNotification(message, type) {
    // Your custom notification logic
}
🔒 Keamanan
SQL Injection Protection: Prepared statements
XSS Prevention: Input sanitization
CSRF Protection: Session validation
Input Validation: Server-side & client-side validation
Password Hashing: bcrypt (untuk expansion)
📱 Responsif Design
Desktop: Full sidebar + main content
Tablet: Collapsible sidebar
Mobile: Hidden sidebar dengan toggle button
Cards: Responsive grid layout
Tables: Horizontal scroll pada mobile
🚀 Performance
AJAX Loading: No page refresh
Debounced Search: Efficient search queries
Optimized Queries: Indexed database columns
Minified Assets: Compressed CSS/JS (production)
Caching: Browser caching headers
🛠️ Development
Local Development
bash
# Start local server
php -S localhost:8000

# Access application
http://localhost:8000
Database Migration
Untuk update database schema:

sql
-- Add new column
ALTER TABLE tasks ADD COLUMN new_field VARCHAR(255);

-- Update existing data
UPDATE tasks SET new_field = 'default_value';
🐛 Troubleshooting
Database Connection Error
Cek kredensial database di includes/database.php
Pastikan MySQL service running
Verify database exists dan user memiliki permission
404 Not Found
Cek file permissions (755 untuk folder, 644 untuk file)
Pastikan .htaccess configured (jika menggunakan Apache)
Verify web server configuration
JavaScript Errors
Buka browser console untuk detail error
Cek path file JavaScript benar
Pastikan AJAX endpoint accessible
Session Issues
Cek PHP session configuration
Verify session directory writable
Check session cookie settings
📈 Future Enhancements
 Multi-user support
 Email notifications
 File attachments
 Advanced reporting
 API endpoints
 Dark mode theme
 Export/Import functionality
 Task categories/tags
 Due date reminders
 Activity audit logs
📄 License
This project is licensed under the MIT License - see the LICENSE file for details.

🤝 Contributing
Fork the project
Create feature branch (git checkout -b feature/AmazingFeature)
Commit changes (git commit -m 'Add AmazingFeature')
Push to branch (git push origin feature/AmazingFeature)
Open Pull Request
📞 Support
Jika ada pertanyaan atau masalah:

Buat issue di GitHub repository
Email: support@todolist.com
Documentation: Check README.md
🙏 Credits
Design inspiration: Modern admin dashboard trends
Icons: Text-based icons for simplicity
Color scheme: Blue-white minimalist theme
Framework: Vanilla PHP/JavaScript (no dependencies)
Made with ❤️ for efficient task management

