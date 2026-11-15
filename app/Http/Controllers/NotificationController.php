<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Menandai satu notifikasi sebagai sudah dibaca lalu mengarahkan ke URL tujuan.
     */
    public function read(Request $request, string $notification): RedirectResponse
    {
        $item = $request->user()
            ->notifications()
            ->whereKey($notification)
            ->firstOrFail();

        if ($item->read_at === null) {
            $item->markAsRead();
        }

        $targetUrl = data_get($item->data, 'action_url', route('dashboard'));

        return redirect()->to($targetUrl);
    }

    /**
     * Menandai seluruh notifikasi pengguna sebagai sudah dibaca.
     */
    public function readAll(Request $request): RedirectResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        return back()->with('success', 'Semua notifikasi telah ditandai sudah dibaca.');
    }
}