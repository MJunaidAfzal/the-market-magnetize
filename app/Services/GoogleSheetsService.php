<?php

namespace App\Services;

use App\Models\User;
use Revolution\Google\Sheets\Facades\Sheets;

class GoogleSheetsService
{
    private string $spreadsheetId;
    private string $sheetName = 'Users';

    private array $headerRow = ['id', 'name', 'username', 'email', 'password', 'phone_number', 'date_of_birth', 'status', 'roles', 'created_at', 'updated_at'];

    public function __construct()
    {
        $this->spreadsheetId = env('GOOGLE_SHEET_ID');
    }

    /**
     * Check if the sheet is empty (no data)
     */
    private function isSheetEmpty(): bool
    {
        try {
            $values = Sheets::spreadsheet($this->spreadsheetId)
                ->sheet($this->sheetName)
                ->first();
            
            return empty($values);
        } catch (\Exception $e) {
            // If sheet doesn't exist or error, consider it empty
            return true;
        }
    }

    /**
     * Ensure header row exists
     */
    private function ensureHeaderRow(): void
    {
        if ($this->isSheetEmpty()) {
            Sheets::spreadsheet($this->spreadsheetId)
                ->sheet($this->sheetName)
                ->append([$this->headerRow]);
        }
    }

    /**
     * Sync all users to Google Sheet
     */
    public function syncAllUsers(): void
    {
        $users = User::all();
        $data = [];

        // Header row
        $data[] = $this->headerRow;

        foreach ($users as $user) {
            $data[] = [
                $user->id,
                $user->name,
                $user->username,
                $user->email,
                $user->plain_password ?? '',
                $user->phone_number ?? '',
                $user->date_of_birth ? $user->date_of_birth->format('Y-m-d') : '',
                $user->status ?? 'active',
                $user->getRoleNames()->implode(', '),
                $user->created_at ? $user->created_at->format('Y-m-d H:i:s') : '',
                $user->updated_at ? $user->updated_at->format('Y-m-d H:i:s') : '',
            ];
        }

        // Clear existing data and write new data
        Sheets::spreadsheet($this->spreadsheetId)
            ->sheet($this->sheetName)
            ->clear()
            ->append($data);
    }

    /**
     * Add a single user to Google Sheet
     */
    public function addUser(User $user): void
    {
        // Ensure header row exists first
        $this->ensureHeaderRow();

        $rowData = [
            $user->id,
            $user->name,
            $user->username,
            $user->email,
            $user->plain_password ?? '',
            $user->phone_number ?? '',
            $user->date_of_birth ? $user->date_of_birth->format('Y-m-d') : '',
            $user->status ?? 'active',
            $user->getRoleNames()->implode(', '),
            $user->created_at ? $user->created_at->format('Y-m-d H:i:s') : '',
            $user->updated_at ? $user->updated_at->format('Y-m-d H:i:s') : '',
        ];

        Sheets::spreadsheet($this->spreadsheetId)
            ->sheet($this->sheetName)
            ->append([$rowData]);
    }

    /**
     * Update a user in Google Sheet by user ID
     */
    public function updateUser(User $user, int $userId): void
    {
        // Get all data from sheet
        $values = Sheets::spreadsheet($this->spreadsheetId)
            ->sheet($this->sheetName)
            ->all();

        $headerRow = $this->headerRow;
        $updatedData = [];
        $userFound = false;

        foreach ($values as $index => $row) {
            // Skip header row, keep it
            if ($index === 0) {
                $updatedData[] = $row;
                continue;
            }
            
            // Check if this is the user we want to update
            if (isset($row[0]) && $row[0] == $userId) {
                // Update this row with new data
                $updatedData[] = [
                    $user->id,
                    $user->name,
                    $user->username,
                    $user->email,
                    $user->plain_password ?? '',
                    $user->phone_number ?? '',
                    $user->date_of_birth ? $user->date_of_birth->format('Y-m-d') : '',
                    $user->status ?? 'active',
                    $user->getRoleNames()->implode(', '),
                    $user->created_at ? $user->created_at->format('Y-m-d H:i:s') : '',
                    $user->updated_at ? $user->updated_at->format('Y-m-d H:i:s') : '',
                ];
                $userFound = true;
            } else {
                // Keep existing row as is
                $updatedData[] = $row;
            }
        }

        // If user not found in sheet, add them
        if (!$userFound) {
            $updatedData[] = [
                $user->id,
                $user->name,
                $user->username,
                $user->email,
                $user->plain_password ?? '',
                $user->phone_number ?? '',
                $user->date_of_birth ? $user->date_of_birth->format('Y-m-d') : '',
                $user->status ?? 'active',
                $user->getRoleNames()->implode(', '),
                $user->created_at ? $user->created_at->format('Y-m-d H:i:s') : '',
                $user->updated_at ? $user->updated_at->format('Y-m-d H:i:s') : '',
            ];
        }

        // Clear and rewrite all data
        $sheet = Sheets::spreadsheet($this->spreadsheetId)
            ->sheet($this->sheetName);
        
        $sheet->clear();
        $sheet->append($updatedData);
    }

    /**
     * Find user row number in Google Sheet by user ID
     */
    public function findUserRowNumber(int $userId): ?int
    {
        try {
            $values = Sheets::spreadsheet($this->spreadsheetId)
                ->sheet($this->sheetName)
                ->get();

            foreach ($values as $index => $row) {
                // Skip header row
                if ($index === 0) continue;
                
                // Check if first column (id) matches
                if (isset($row[0]) && $row[0] == $userId) {
                    return $index + 1; // Return 1-indexed row number
                }
            }
        } catch (\Exception $e) {
            // Sheet might not exist yet
        }

        return null;
    }

    /**
     * Check if Users sheet exists, if not create it
     */
    public function ensureUsersSheetExists(): void
    {
        $this->ensureHeaderRow();
    }
}
