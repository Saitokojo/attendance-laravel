<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Office;
use App\Services\QrSessionService;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrSessionController extends Controller
{
    public function __construct(private QrSessionService $qrService) {}

    public function index()
    {
        $offices = Office::where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(function (Office $office) {
                $active = $this->qrService->currentActive($office);
                $office->activeSession = $active;
                return $office;
            });

        return view('admin.qr.index', compact('offices'));
    }

    public function store(Request $request)
    {
        $request->validate(['office_id' => 'required|exists:offices,id']);

        $office = Office::findOrFail($request->office_id);
        $session = $this->qrService->startSession($office, $request->user());

        return redirect()->route('admin.qr.display', $office);
    }

    public function display(Office $office)
    {
        $session = $this->qrService->currentActive($office);

        if (! $session) {
            return redirect()->route('admin.qr.index')
                ->with('error', 'No active QR session for this office.');
        }

        $qrSvg = QrCode::format('svg')->size(300)->generate($session->token);

        return view('admin.qr.display', compact('office', 'session', 'qrSvg'));
    }
}
