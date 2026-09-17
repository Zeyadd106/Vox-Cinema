<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Booking;
use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class AdminController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Display the admin dashboard.
     */
    public function dashboard()
    {
        // Get statistics for the dashboard
        $stats = [
            'total_users' => User::count(),
            'total_bookings' => Booking::count(),
            'total_movies' => Movie::count(),
            'recent_bookings' => Booking::with(['user', 'showtime.movie'])
                                      ->latest()
                                      ->take(5)
                                      ->get(),
            'revenue' => Booking::where('payment_status', 'paid')
                               ->sum('total_price'),
        ];

        return view('admin.dashboard', compact('stats'));
    }

    /**
     * Display a listing of users.
     */
    public function index()
    {
        $users = User::withCount('bookings')
                    ->latest()
                    ->paginate(10);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        $user->load(['bookings.showtime.movie']);
        return view('admin.users.show', compact('user'));
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);

        $user->update($validated);
        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }

    /**
     * Display a listing of bookings.
     */
    public function bookings()
    {
        $bookings = Booking::with(['user', 'showtime.movie'])
                          ->latest()
                          ->paginate(10);

        return view('admin.bookings.index', compact('bookings'));
    }

    /**
     * Toggle user admin status.
     */
    public function toggleUserAdmin(User $user)
    {
        // Prevent admin from removing their own admin status
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot modify your own admin status.');
        }

        $user->is_admin = !$user->is_admin;
        $user->save();

        return back()->with('success', 
            $user->is_admin 
                ? 'User has been granted admin privileges.' 
                : 'Admin privileges have been revoked from the user.'
        );
    }

    /**
     * Display the settings page.
     */
    public function settings()
    {
        return view('admin.settings');
    }

    /**
     * Update the site settings.
     */
    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'site_name' => 'required|string|max:255',
            'contact_email' => 'required|email',
            'phone_number' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'social_media_links' => 'nullable|array',
            'booking_fee' => 'required|numeric|min:0',
            'tax_rate' => 'required|numeric|min:0|max:100',
        ]);

        // Update settings in database or config
        foreach ($validated as $key => $value) {
            setting([$key => $value]);
        }

        return back()->with('success', 'Settings updated successfully.');
    }
}
