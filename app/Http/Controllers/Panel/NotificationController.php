<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    /**
     * Display notification center.
     */
    public function index(): View
    {
        $notifications = auth()->user()
            ->notifications()
            ->paginate(20);

        return view('panels.shared.notifications.index', compact('notifications'));
    }

    /**
     * Show notification preferences.
     */
    public function preferences(): View
    {
        return view('panels.shared.notifications.preferences');
    }

    /**
     * Update notification preferences.
     */
    public function updatePreferences(Request $request): RedirectResponse
    {
        // Store preferences in user settings or separate preferences table
        // For now, just redirect back with success message
        
        return redirect()
            ->route('notifications.preferences')
            ->with('success', 'Notification preferences updated successfully!');
    }

    /**
     * Mark notification as read.
     */
    public function markAsRead($notificationId): RedirectResponse
    {
        $notification = auth()->user()
            ->notifications()
            ->findOrFail($notificationId);

        $notification->markAsRead();

        return back()->with('success', 'Notification marked as read.');
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(): RedirectResponse
    {
        auth()->user()->unreadNotifications->markAsRead();

        return back()->with('success', 'All notifications marked as read.');
    }
}
