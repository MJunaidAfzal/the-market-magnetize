<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Services\GoogleSheetsService;
use App\Services\UserExcelService;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;
    
    protected ?string $newPassword = null;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Remove password from form data when loading for edit
        // This prevents the hashed password from being displayed
        unset($data['password']);
        
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Store the plain password before it gets transformed
        // We'll use this in afterSave if password was changed
        if (isset($data['password']) && !empty($data['password'])) {
            $this->newPassword = $data['password'];
        }
        
        return $data;
    }

    protected function afterSave(): void
    {
        $user = $this->getRecord();
        
        // If a new password was provided, save it to plain_password
        if (!empty($this->newPassword)) {
            $user->plain_password = $this->newPassword;
            $user->save();
            $user->refresh();
        }
        
        // Sync to local Excel file
        $excelService = new UserExcelService();
        $excelService->exportToFile();

        // Sync to Google Sheets
        $googleSheetsService = new GoogleSheetsService();
        
        // Update user in Google Sheet
        $googleSheetsService->updateUser($user, $user->id);
    }
}
