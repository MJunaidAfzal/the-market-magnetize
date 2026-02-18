<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Revolution\Google\Sheets\Facades\Sheets;

class GoogleSheetApiController extends Controller
{
    /**
     * Display data from Google Sheet
     */
    public function index()
    {
        $values = Sheets::spreadsheet(env('GOOGLE_SHEET_ID'))
            ->sheet('Demo')
            ->get();

        dd($values);
    }

    /**
     * Store random data in Google Sheet
     */
    public function storeRandomData()
    {
        $data = [
            ['No', 'Name', 'Email'],
            ['1', 'name1', 'mail1@example.com'],
            ['2', 'name2', 'mail2@example.com'],
            ['3', 'name3', 'mail3@example.com'],
            ['4', 'name4', 'mail4@example.com'],
            ['5', 'name5', 'mail5@example.com'],
        ];

        Sheets::spreadsheet(env('GOOGLE_SHEET_ID'))
            ->sheet('Demo')
            ->append($data);

        return redirect()->back()->with('success', 'Data added to Google Sheet successfully!');
    }
}
