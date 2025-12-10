<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class QrPdfController extends Controller
{
    public function generate(Request $request)
    {
        $url = $request->query('url');
        $filename = 'qr-code-' . Str::ulid() . '.svg';

        $qrContent = QrCode::format('svg')
            ->size(400)
            ->margin(2)
            ->generate($url);

        return response($qrContent)
            ->header('Content-Type', 'image/svg+xml')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}
