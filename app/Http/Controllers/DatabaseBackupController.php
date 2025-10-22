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
            $backupName = 'backup_' . Carbon::now()->format('Y-m-d_H-i-s') . '.sql';

            $dbHost = env('DB_HOST', '127.0.0.1');
            $dbName = env('DB_DATABASE');
            $dbUser = env('DB_USERNAME');
            $dbPass = env('DB_PASSWORD');
            $dbPort = env('DB_PORT', '3306');

            if (!file_exists(storage_path('app/backups'))) {
                mkdir(storage_path('app/backups'), 0755, true);
            }

            $backupPath = storage_path('app/backups/' . $backupName);

            $dsn = "mysql:host={$dbHost};port={$dbPort};dbname={$dbName};charset=utf8mb4";
            $pdo = new \PDO($dsn, $dbUser, $dbPass);

            $tables = $pdo->query('SHOW TABLES')->fetchAll(\PDO::FETCH_COLUMN);

            $sql = "-- Database backup created on " . Carbon::now() . "\n";
            $sql .= "SET FOREIGN_KEY_CHECKS = 0;\n\n";

            foreach ($tables as $table) {
                $stmt = $pdo->query("SHOW CREATE TABLE `{$table}`");
                $createTable = $stmt->fetch(\PDO::FETCH_ASSOC);

                $createSql = isset($createTable['Create Table']) ? $createTable['Create Table'] :
                           (isset($createTable['Create View']) ? $createTable['Create View'] : null);

                if ($createSql) {
                    $sql .= "-- Table structure for `{$table}`\n";
                    $sql .= $createSql . ";\n\n";
                }

                // Get table data
                $stmt = $pdo->query("SELECT * FROM `{$table}`");
                $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

                if (!empty($rows)) {
                    $sql .= "-- Data for `{$table}`\n";
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
                    $sql .= "\n";
                }
            }

            $sql .= "SET FOREIGN_KEY_CHECKS = 1;\n";
            file_put_contents($backupPath, $sql);

            Log::info('Database backup created successfully: ' . $backupName);
            return redirect()->back()->with('success', 'Database backup created successfully!');

        } catch (\Exception $e) {
            Log::error('Database backup error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while creating the backup: ' . $e->getMessage());
        }
    }

    public function download($filename)
    {
        $filePath = storage_path('app/backups/' . $filename);

        if (file_exists($filePath)) {
            return response()->download($filePath);
        }

        return redirect()->back()->with('error', 'Backup file not found.');
    }

    public function delete($filename)
    {
        $filePath = storage_path('app/backups/' . $filename);

        if (file_exists($filePath)) {
            unlink($filePath);
            return redirect()->back()->with('success', 'Backup deleted successfully!');
        }

        return redirect()->back()->with('error', 'Backup file not found.');
    }

    private function getBackupFiles()
    {
        $backupDir = storage_path('app/backups');

        if (!file_exists($backupDir)) {
            return [];
        }

        $files = scandir($backupDir);
        $backups = [];

        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..' && pathinfo($file, PATHINFO_EXTENSION) === 'sql') {
                $filePath = $backupDir . '/' . $file;
                $backups[] = [
                    'name' => $file,
                    'size' => filesize($filePath),
                    'created_at' => Carbon::createFromTimestamp(filemtime($filePath)),
                    'size_human' => $this->formatBytes(filesize($filePath))
                ];
            }
        }

        // Sort by creation date (newest first)
        usort($backups, function($a, $b) {
            return $b['created_at'] <=> $a['created_at'];
        });

        return $backups;
    }

    public function clearDatabase(Request $request)
    {
        // Validate password
        $request->validate([
            'password' => 'required|string',
        ]);

        $providedPassword = $request->input('password');
        Log::info('User pass:'.$providedPassword);
        $correctPassword = config('app.db_clear_password');
        Log::info('Correct pass:'.$correctPassword);

        if ($providedPassword !== $correctPassword) {
            return redirect()->back()->with('error', 'Incorrect password. Database clearing aborted.');
        }

        try {
            DB::beginTransaction();

            // Get all tables except settings and users
            $tables = DB::select('SHOW TABLES');
            $databaseName = env('DB_DATABASE');
            $tableKey = 'Tables_in_' . $databaseName;

            $tablesToClear = [];
            foreach ($tables as $table) {
                $tableName = $table->$tableKey;
                // Skip settings table and users table (we'll handle users separately)
                if ($tableName !== 'settings' && $tableName !== 'users') {
                    $tablesToClear[] = $tableName;
                }
            }

            // Clear all tables except settings
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            foreach ($tablesToClear as $table) {
                DB::statement('TRUNCATE TABLE `' . $table . '`');
                Log::info('Cleared table: ' . $table);
            }
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            // Clear users table but keep user with id 1
            DB::statement('DELETE FROM users WHERE id != 1');
            Log::info('Cleared users table, kept user with id 1');

            DB::commit();

            Log::info('Database cleared successfully by user: ' . auth()->user()->name ?? 'Unknown');
            return redirect()->back()->with('success', 'Database cleared successfully! Settings and user ID 1 have been preserved.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Database clearing error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while clearing the database: ' . $e->getMessage());
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