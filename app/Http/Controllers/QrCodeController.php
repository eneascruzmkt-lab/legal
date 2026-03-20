<?php

namespace App\Http\Controllers;

use App\Models\QrCode;
use Illuminate\Http\Request;
use Inertia\Inertia;
use SimpleSoftwareIO\QrCode\Facades\QrCode as QrCodeGenerator;

class QrCodeController extends Controller
{
    public function index()
    {
        $restaurant = auth()->user()->restaurant;

        $qrCodes = $restaurant
            ? $restaurant->qrCodes()->latest()->get()
            : collect();

        return Inertia::render('QrCodes/Index', [
            'qrCodes' => $qrCodes,
            'restaurantSlug' => $restaurant?->slug,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'label' => 'required|string|max:255',
            'style_config' => 'nullable|array',
        ]);

        $restaurant = auth()->user()->restaurant;
        $url = config('app.url') . '/' . $restaurant->slug;

        QrCode::create([
            'restaurant_id' => $restaurant->id,
            'label' => $request->label,
            'style_config' => $request->style_config ?? [],
            'url' => $url,
        ]);

        return redirect()->back()->with('success', 'QR Code criado com sucesso.');
    }

    public function destroy(QrCode $qrCode)
    {
        $restaurant = auth()->user()->restaurant;
        abort_if($qrCode->restaurant_id !== $restaurant->id, 403);

        $qrCode->delete();

        return redirect()->back()->with('success', 'QR Code excluído com sucesso.');
    }

    public function download(QrCode $qrCode)
    {
        $restaurant = auth()->user()->restaurant;
        abort_if($qrCode->restaurant_id !== $restaurant->id, 403);

        $qrImage = QrCodeGenerator::format('png')
            ->size(500)
            ->generate($qrCode->url);

        return response($qrImage, 200, [
            'Content-Type' => 'image/png',
            'Content-Disposition' => 'attachment; filename="qrcode-' . $qrCode->label . '.png"',
        ]);
    }
}
