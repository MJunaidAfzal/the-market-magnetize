<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UserExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return User::with('roles')->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Username',
            'Email',
            'Phone Number',
            'Date of Birth',
            'Status',
            'Role',
            'Password',
            'Created At',
        ];
    }

    /**
     * @param User $user
     * @return array
     */
    public function map($user): array
    {
        // Get plain password from database
        $plainPassword = $user->plain_password ?? '';
        
        return [
            $user->id,
            $user->name,
            $user->username ?? '',
            $user->email,
            $user->phone_number ?? '',
            $user->date_of_birth ? $user->date_of_birth->format('Y-m-d') : '',
            $user->status,
            $user->roles->first()?->name ?? '',
            $plainPassword,
            $user->created_at ? $user->created_at->format('Y-m-d') : '',
        ];
    }
}
