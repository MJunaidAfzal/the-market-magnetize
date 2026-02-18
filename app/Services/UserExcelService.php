<?php

namespace App\Services;

use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UserExport;

class UserExcelService
{
    /**
     * Download the Excel file - generates and streams on the fly
     */
    public function download()
    {
        return Excel::download(new UserExport(), 'users.xlsx');
    }

    /**
     * Export all users to Excel file and save to disk
     */
    public function exportToFile(): string
    {
        $filePath = storage_path('app/exports/users.xlsx');
        $directory = dirname($filePath);
        
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        Excel::store(new UserExport(), 'exports/users.xlsx', 'local', \Maatwebsite\Excel\Excel::XLSX);

        return $filePath;
    }

    /**
     * Check if the Excel file exists on disk
     */
    public function fileExists(): bool
    {
        $filePath = storage_path('app/exports/users.xlsx');
        return file_exists($filePath);
    }
}
