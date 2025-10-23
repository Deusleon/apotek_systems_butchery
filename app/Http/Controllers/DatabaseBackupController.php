<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DatabaseBackupController extends Controller
{
    public function index()
    {
        // Get list of existing backups
        $backups = $this->getBackupFiles();

        return view('tools.database_backup', compact('backups'));
    }

    public function clearIndex()
    {
        // Get list of existing backups for reference
        $backups = $this->getBackupFiles();

        return view('tools.database_clear', compact('backups'));
    }

    public function create(Request $request)
    {
        try {
            // Validate request
            $request->validate([], []); // Add validation rules if needed

            $backupName = 'backup_' . Carbon::now()->format('Y-m-d_H-i-s') . '.sql';

            // Validate environment variables
            $dbHost = env('DB_HOST', '127.0.0.1');
            $dbName = env('DB_DATABASE');
            $dbUser = env('DB_USERNAME');
            $dbPass = env('DB_PASSWORD');
            $dbPort = env('DB_PORT', '3306');

            if (empty($dbName) || empty($dbUser)) {
                Log::error('Database configuration incomplete for backup');
                return redirect()->back()->with('error', 'Database configuration is incomplete. Please check environment settings.');
            }

            // Create backup directory with error handling
            $backupDir = storage_path('app/backups');
            if (!file_exists($backupDir)) {
                if (!mkdir($backupDir, 0755, true)) {
                    Log::error('Failed to create backup directory: ' . $backupDir);
                    return redirect()->back()->with('error', 'Failed to create backup directory. Please check permissions.');
                }
            }

            if (!is_writable($backupDir)) {
                Log::error('Backup directory is not writable: ' . $backupDir);
                return redirect()->back()->with('error', 'Backup directory is not writable. Please check permissions.');
            }

            $backupPath = storage_path('app/backups/' . $backupName);

            // Test database connection
            try {
                $dsn = "mysql:host={$dbHost};port={$dbPort};dbname={$dbName};charset=utf8mb4";
                $pdo = new \PDO($dsn, $dbUser, $dbPass, [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                    \PDO::ATTR_EMULATE_PREPARES => false,
                ]);
            } catch (\PDOException $e) {
                Log::error('Database connection failed for backup: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Database connection failed. Please check database settings.');
            }

            // Get tables with error handling
            try {
                $tables = $pdo->query('SHOW TABLES')->fetchAll(\PDO::FETCH_COLUMN);
            } catch (\PDOException $e) {
                Log::error('Failed to retrieve table list for backup: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Failed to retrieve database table list for backup.');
            }

            if (empty($tables)) {
                Log::warning('No tables found in database for backup');
                return redirect()->back()->with('error', 'No tables found in database to backup.');
            }

            $sql = "-- Database backup created on " . Carbon::now() . " by user: " . (auth()->user()->name ?? 'Unknown') . "\n";
            $sql .= "SET FOREIGN_KEY_CHECKS = 0;\n\n";

            $processedTables = 0;
            $skippedTables = 0;

            foreach ($tables as $table) {
                try {
                    // Get table structure
                    $stmt = $pdo->prepare("SHOW CREATE TABLE `{$table}`");
                    $stmt->execute();
                    $createTable = $stmt->fetch(\PDO::FETCH_ASSOC);

                    $createSql = isset($createTable['Create Table']) ? $createTable['Create Table'] :
                                (isset($createTable['Create View']) ? $createTable['Create View'] : null);

                    if ($createSql) {
                        $sql .= "-- Table structure for `{$table}`\n";
                        $sql .= $createSql . ";\n\n";
                    }

                    // Get table data with memory-safe approach
                    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM `{$table}`");
                    $stmt->execute();
                    $rowCount = $stmt->fetch()['count'];

                    if ($rowCount > 0) {
                        // For large tables, we might want to limit or warn
                        if ($rowCount > 100000) { // Arbitrary large table threshold
                            Log::warning("Large table detected: {$table} with {$rowCount} rows");
                        }

                        $sql .= "-- Data for `{$table}` ({$rowCount} rows)\n";

                        // Get data in chunks to avoid memory issues
                        $offset = 0;
                        $chunkSize = 1000;

                        while ($offset < $rowCount) {
                            $stmt = $pdo->prepare("SELECT * FROM `{$table}` LIMIT {$chunkSize} OFFSET {$offset}");
                            $stmt->execute();
                            $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

                            if (!empty($rows)) {
                                $columns = array_keys($rows[0]);
                                $columnNames = '`' . implode('`, `', $columns) . '`';

                                foreach ($rows as $row) {
                                    $values = array_map(function($value) use ($pdo) {
                                        if ($value === null) {
                                            return 'NULL';
                                        }
                                        return $pdo->quote($value);
                                    }, $row);

                                    $sql .= "INSERT INTO `{$table}` ({$columnNames}) VALUES (" . implode(', ', $values) . ");\n";
                                }
                            }

                            $offset += $chunkSize;
                        }
                        $sql .= "\n";
                    }

                    $processedTables++;

                } catch (\PDOException $e) {
                    Log::warning('Failed to backup table: ' . $table . ' - ' . $e->getMessage());
                    $skippedTables++;
                    continue;
                }
            }

            $sql .= "SET FOREIGN_KEY_CHECKS = 1;\n";

            // Write file with error handling
            try {
                if (file_put_contents($backupPath, $sql) === false) {
                    Log::error('Failed to write backup file: ' . $backupPath);
                    return redirect()->back()->with('error', 'Failed to write backup file. Please check disk space and permissions.');
                }
            } catch (\Exception $e) {
                Log::error('Exception writing backup file: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Failed to save backup file.');
            }

            $fileSize = filesize($backupPath);
            Log::info("Database backup created successfully: {$backupName} ({$processedTables} tables processed, {$skippedTables} skipped, " . $this->formatBytes($fileSize) . ")");

            $message = "Database backup created successfully! Processed {$processedTables} tables.";
            if ($skippedTables > 0) {
                $message .= " {$skippedTables} tables were skipped (see logs for details).";
            }

            return redirect()->back()->with('success', $message);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            Log::error('Unexpected error during backup creation: ' . $e->getMessage() . ' at line ' . $e->getLine() . ' in ' . $e->getFile());
            return redirect()->back()->with('error', 'An unexpected error occurred during backup creation. Please contact system administrator.');
        }
    }

    public function download($filename)
    {
        try {
            // Validate filename to prevent directory traversal
            if (empty($filename) || !preg_match('/^[a-zA-Z0-9_\-\.]+$/', $filename)) {
                Log::warning('Invalid backup filename requested: ' . $filename);
                return redirect()->back()->with('error', 'Invalid backup filename.');
            }

            $filePath = storage_path('app/backups/' . $filename);

            // Check if file exists and is readable
            if (!file_exists($filePath)) {
                Log::warning('Backup file not found: ' . $filename);
                return redirect()->back()->with('error', 'Backup file not found.');
            }

            if (!is_readable($filePath)) {
                Log::error('Backup file not readable: ' . $filename);
                return redirect()->back()->with('error', 'Backup file is not accessible.');
            }

            // Check file size (prevent downloading extremely large files)
            $fileSize = filesize($filePath);
            if ($fileSize > 500 * 1024 * 1024) { // 500MB limit
                Log::warning('Backup file too large for download: ' . $filename . ' (' . $this->formatBytes($fileSize) . ')');
                return redirect()->back()->with('error', 'Backup file is too large to download.');
            }

            Log::info('Backup file downloaded: ' . $filename . ' by user: ' . (auth()->user()->name ?? 'Unknown'));
            return response()->download($filePath);

        } catch (\Exception $e) {
            Log::error('Error downloading backup file: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while downloading the backup file.');
        }
    }

    public function delete($filename)
    {
        try {
            // Validate filename to prevent directory traversal
            if (empty($filename) || !preg_match('/^[a-zA-Z0-9_\-\.]+$/', $filename)) {
                Log::warning('Invalid backup filename for deletion: ' . $filename);
                return redirect()->back()->with('error', 'Invalid backup filename.');
            }

            $filePath = storage_path('app/backups/' . $filename);

            // Check if file exists
            if (!file_exists($filePath)) {
                Log::warning('Backup file not found for deletion: ' . $filename);
                return redirect()->back()->with('error', 'Backup file not found.');
            }

            // Check if file is writable
            if (!is_writable($filePath)) {
                Log::error('Backup file not writable for deletion: ' . $filename);
                return redirect()->back()->with('error', 'Backup file cannot be deleted. Permission denied.');
            }

            // Get file info before deletion for logging
            $fileSize = filesize($filePath);
            $fileSizeHuman = $this->formatBytes($fileSize);

            if (unlink($filePath)) {
                Log::info('Backup file deleted: ' . $filename . ' (' . $fileSizeHuman . ') by user: ' . (auth()->user()->name ?? 'Unknown'));
                return redirect()->back()->with('success', 'Backup deleted successfully!');
            } else {
                Log::error('Failed to delete backup file: ' . $filename);
                return redirect()->back()->with('error', 'Failed to delete backup file.');
            }

        } catch (\Exception $e) {
            Log::error('Error deleting backup file: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while deleting the backup file.');
        }
    }

    private function getBackupFiles()
    {
        try {
            $backupDir = storage_path('app/backups');

            if (!file_exists($backupDir)) {
                Log::info('Backup directory does not exist, creating: ' . $backupDir);
                if (!mkdir($backupDir, 0755, true)) {
                    Log::error('Failed to create backup directory: ' . $backupDir);
                    return [];
                }
                return [];
            }

            if (!is_readable($backupDir)) {
                Log::error('Backup directory not readable: ' . $backupDir);
                return [];
            }

            $files = scandir($backupDir);
            if ($files === false) {
                Log::error('Failed to read backup directory: ' . $backupDir);
                return [];
            }

            $backups = [];

            foreach ($files as $file) {
                if ($file === '.' || $file === '..') {
                    continue;
                }

                // Validate filename pattern
                if (!preg_match('/^backup_\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2}\.sql$/', $file)) {
                    Log::warning('Invalid backup filename found: ' . $file);
                    continue;
                }

                $filePath = $backupDir . '/' . $file;

                if (!file_exists($filePath)) {
                    Log::warning('Backup file does not exist: ' . $filePath);
                    continue;
                }

                try {
                    $fileSize = filesize($filePath);
                    $fileTime = filemtime($filePath);

                    if ($fileSize === false || $fileTime === false) {
                        Log::warning('Could not get file info for: ' . $file);
                        continue;
                    }

                    $backups[] = [
                        'name' => $file,
                        'size' => $fileSize,
                        'created_at' => Carbon::createFromTimestamp($fileTime),
                        'size_human' => $this->formatBytes($fileSize)
                    ];
                } catch (\Exception $e) {
                    Log::warning('Error processing backup file: ' . $file . ' - ' . $e->getMessage());
                    continue;
                }
            }

            // Sort by creation date (newest first)
            usort($backups, function($a, $b) {
                try {
                    return $b['created_at'] <=> $a['created_at'];
                } catch (\Exception $e) {
                    Log::warning('Error sorting backup files: ' . $e->getMessage());
                    return 0;
                }
            });

            return $backups;

        } catch (\Exception $e) {
            Log::error('Error getting backup files: ' . $e->getMessage());
            return [];
        }
    }

    public function clearDatabase(Request $request)
    {
        try {
            // Validate password with detailed error handling
            $request->validate([
                'password' => 'required|string|min:1|max:255',
            ], [
                'password.required' => 'Password is required for database clearing.',
                'password.string' => 'Password must be a valid string.',
                'password.min' => 'Password cannot be empty.',
                'password.max' => 'Password is too long.',
            ]);

            $providedPassword = trim($request->input('password'));
            $correctPassword = config('app.db_clear_password');

            // Validate password configuration
            if (empty($correctPassword)) {
                Log::error('Database clear password not configured in config/app.php');
                return redirect()->back()->with('error', 'Database clearing is not properly configured. Please contact system administrator.');
            }

            if ($providedPassword !== $correctPassword) {
                Log::warning('Failed database clear attempt with incorrect password by user: ' . (auth()->user()->name ?? 'Unknown') . ' (ID: ' . (auth()->id() ?? 'Unknown') . ')');
                return redirect()->back()->with('error', 'Incorrect password. Database clearing aborted.');
            }

            // Check database connection
            try {
                DB::connection()->getPdo();
            } catch (\Exception $e) {
                Log::error('Database connection failed: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Database connection failed. Please try again later.');
            }

            DB::beginTransaction();

            // Get all tables (exclude views and other non-table objects)
            try {
                $tables = DB::select("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'");
                $databaseName = env('DB_DATABASE');
                $tableKey = 'Tables_in_' . $databaseName;

                if (empty($tables)) {
                    Log::warning('No tables found in database');
                    DB::rollBack();
                    return redirect()->back()->with('error', 'No tables found in database to clear.');
                }
            } catch (\Exception $e) {
                Log::error('Failed to retrieve table list: ' . $e->getMessage());
                DB::rollBack();
                return redirect()->back()->with('error', 'Failed to retrieve database table list.');
            }

            $tablesToClear = [];
            foreach ($tables as $table) {
                $tableName = $table->$tableKey ?? null;
                if (!$tableName) {
                    Log::warning('Invalid table object structure encountered');
                    continue;
                }

                // Skip critical system tables
                if ($tableName !== 'settings' && $tableName !== 'users' &&
                    $tableName !== 'inv_stores' && $tableName !== 'roles' &&
                    $tableName !== 'permissions' && $tableName !== 'role_has_permissions' &&
                    $tableName !== 'model_has_roles' && $tableName !== 'model_has_permissions') {
                    $tablesToClear[] = $tableName;
                }
            }

            // Clear operational tables
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            $clearedTables = 0;
            $failedTables = 0;

            foreach ($tablesToClear as $table) {
                try {
                    // Verify table exists before attempting to truncate
                    $tableExists = DB::select("SHOW TABLES LIKE ?", [$table]);
                    if (empty($tableExists)) {
                        Log::warning('Table does not exist, skipping: ' . $table);
                        continue;
                    }

                    DB::statement('TRUNCATE TABLE `' . $table . '`');
                    Log::info('Cleared table: ' . $table);
                    $clearedTables++;
                } catch (\Exception $e) {
                    Log::warning('Could not clear table: ' . $table . ' - ' . $e->getMessage());
                    $failedTables++;
                    continue;
                }
            }
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            // Handle selective clearing with error handling
            $selectiveOperations = [
                ['table' => 'users', 'condition' => 'id != 1', 'description' => 'users table (kept user ID 1)'],
                ['table' => 'roles', 'condition' => 'id != 1', 'description' => 'roles table (kept role ID 1)'],
                ['table' => 'inv_stores', 'condition' => 'id NOT IN (1, 2)', 'description' => 'stores table (kept stores ID 1 & 2)'],
                ['table' => 'model_has_roles', 'condition' => 'role_id != 1', 'description' => 'user-role relationships (kept role ID 1)'],
            ];

            foreach ($selectiveOperations as $operation) {
                try {
                    $countBefore = DB::table($operation['table'])->count();
                    DB::statement('DELETE FROM `' . $operation['table'] . '` WHERE ' . $operation['condition']);
                    $countAfter = DB::table($operation['table'])->count();
                    $deletedCount = $countBefore - $countAfter;
                    Log::info('Cleared ' . $operation['description'] . ' - deleted ' . $deletedCount . ' records');
                } catch (\Exception $e) {
                    Log::error('Failed to clear ' . $operation['description'] . ': ' . $e->getMessage());
                    DB::rollBack();
                    return redirect()->back()->with('error', 'Failed to clear ' . $operation['description'] . '. Operation aborted.');
                }
            }

            // Handle role_has_permissions separately
            try {
                $countBefore = DB::table('role_has_permissions')->count();
                DB::statement('DELETE FROM role_has_permissions WHERE role_id NOT IN (SELECT role_id FROM model_has_roles WHERE model_id = 1 AND model_type = "App\\\\User")');
                $countAfter = DB::table('role_has_permissions')->count();
                $deletedCount = $countBefore - $countAfter;
                Log::info('Cleared role_has_permissions except for user ID 1 - deleted ' . $deletedCount . ' records');
            } catch (\Exception $e) {
                Log::error('Failed to clear role_has_permissions: ' . $e->getMessage());
                DB::rollBack();
                return redirect()->back()->with('error', 'Failed to clear role permissions. Operation aborted.');
            }

            DB::commit();

            $userName = auth()->user()->name ?? 'Unknown';
            $userId = auth()->id() ?? 'Unknown';

            Log::info("Database cleared successfully by user: {$userName} (ID: {$userId}). Cleared {$clearedTables} tables, {$failedTables} tables failed.");

            $message = "Database cleared successfully! Cleared {$clearedTables} tables.";
            if ($failedTables > 0) {
                $message .= " {$failedTables} tables could not be cleared (see logs for details).";
            }
            $message .= " Critical system data has been preserved.";

            return redirect()->back()->with('success', $message);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Database query error during clearing: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Database error occurred during clearing. Please check logs for details.');
        } catch (\Exception $e) {
            Log::error('Unexpected error during database clearing: ' . $e->getMessage() . ' at line ' . $e->getLine() . ' in ' . $e->getFile());
            return redirect()->back()->with('error', 'An unexpected error occurred during database clearing. Please contact system administrator.');
        }
    }

    private function formatBytes($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, 2) . ' ' . $units[$pow];
    }
}