<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Services\GoogleSheetsService;
use App\Services\UserExcelService;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function afterCreate(): void
    {
        $user = $this->record;
        
        // Ensure plain_password is saved to database
        $user->refresh();
        
        // Sync to local Excel file
        $excelService = new UserExcelService();
        $excelService->exportToFile();

        // Sync to Google Sheets
        $googleSheetsService = new GoogleSheetsService();
        $googleSheetsService->addUser($user);
    }
}
