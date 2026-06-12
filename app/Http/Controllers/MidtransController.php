<?php

namespace App\Http\Controllers;

use App\Services\MidtransService;
use Illuminate\Http\Request;

class MidtransController extends Controller
{
    public function __construct(private MidtransService $midtrans) {}

    public function notifikasi(Request $request)
    {
        $notif = $request->all();

        $this->midtrans->handleNotifikasi($notif);

        return response()->json(['status' => 'ok']);
    }
}
