<?php

namespace App\Http\Controllers;

use App\Services\UserExcelService;
use Illuminate\Http\Request;

class DownloadExcelController extends Controller
{
    /**
     * Download the users Excel file
     */
    public function download(Request $request)
    {
        $excelService = new UserExcelService();
        
        // The service's download method handles file generation if it doesn't exist
        return $excelService->download();
    }
}
