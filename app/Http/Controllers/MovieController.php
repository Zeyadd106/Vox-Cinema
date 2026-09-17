<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class MovieController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function index()
    {
        $movies = Movie::latest()->get();

        // Public movie listing
        if (!request()->routeIs('admin.*')) {
            $currentMovies = Movie::where('status', 'current')->latest()->get();
            return view('movies.index', compact('movies', 'currentMovies'));
        }
        
        $stats = [
            'total_users' => \App\Models\User::count(),
            'total_bookings' => \App\Models\Booking::count(),
            'total_movies' => Movie::count(),
            'revenue' => \App\Models\Booking::where('payment_status', 'paid')->sum('total_price'),
            'recent_bookings' => \App\Models\Booking::with(['user', 'showtime.movie'])
                                    ->latest()
                                    ->take(5)
                                    ->get()
        ];

        return view('admin.movies.index', compact('movies', 'stats'));
    }

    public function create()
    {
        return view('admin.movies.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'duration' => 'required|integer|min:1|max:999',
            'genre' => 'required|string|in:Action,Comedy,Drama,Horror,Sci-Fi,Adventure,Romance,Animation,Documentary,Thriller',
            'rating' => 'required|string|in:G,PG,PG-13,R,NC-17',
            'poster' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'trailer_url' => 'required|url',
            'status' => 'required|string|in:current,coming_soon',
            'release_date' => 'required|date'
        ]);

        // Handle poster upload first
        if (!$request->hasFile('poster')) {
            return back()
                ->withErrors(['poster' => 'Poster file is required'])
                ->withInput();
        }

        // Upload the poster file
        $posterPath = $request->file('poster')->store('posters', 'public');
        if (!$posterPath) {
            return back()
                ->withErrors(['poster' => 'Failed to upload poster file'])
                ->withInput();
        }

        try {
            // Prepare movie data
            $movieData = array_merge(
                array_diff_key($validated, ['poster' => '']), // Remove poster from validated data
                ['poster_path' => $posterPath] // Add poster_path
            );

            // Validate release date for coming soon movies
            if ($movieData['status'] === 'coming_soon') {
                if (strtotime($movieData['release_date']) <= strtotime('today')) {
                    return back()
                        ->withErrors(['release_date' => 'Release date for coming soon movies must be in the future'])
                        ->withInput();
                }
            }

            // Create the movie
            $movie = Movie::create($movieData);

            return redirect()
                ->route('admin.dashboard')
                ->with('success', 'Movie created successfully.');
                
        } catch (\Exception $e) {
            // If something goes wrong, delete the uploaded file
            Storage::disk('public')->delete($posterPath);
            
            return back()
                ->withErrors(['error' => 'Failed to create movie: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function edit($id)
    {
        $movie = Movie::findOrFail($id);
        return view('admin.movies.edit', compact('movie'));
    }

    public function update(Request $request, $id)
    {
        $movie = Movie::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'duration' => 'required|integer|min:1|max:999',
            'genre' => 'required|string|in:Action,Comedy,Drama,Horror,Sci-Fi,Adventure,Romance,Animation,Documentary,Thriller',
            'rating' => 'required|string|in:G,PG,PG-13,R,NC-17',
            'poster' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
            'trailer_url' => 'required|url',
            'status' => 'required|string|in:current,coming_soon',
            'release_date' => 'required|date'
        ]);

        // Validate release date for coming soon movies
        if ($validated['status'] === 'coming_soon') {
            if (strtotime($validated['release_date']) <= strtotime('today')) {
                return back()->withErrors(['release_date' => 'Release date for coming soon movies must be in the future'])->withInput();
            }
        }

        if ($request->hasFile('poster')) {
            // Delete old poster
            if ($movie->poster_path) {
                Storage::disk('public')->delete($movie->poster_path);
            }
            $posterPath = $request->file('poster')->store('posters', 'public');
            $validated['poster_path'] = $posterPath;
        }

        // Remove the original poster field if it exists
        unset($validated['poster']);

        $movie->update($validated);

        return redirect()->route('admin.dashboard')
            ->with('success', 'Movie updated successfully.');
    }

    public function destroy($id)
    {
        $movie = Movie::findOrFail($id);
        
        // Delete poster if exists
        if ($movie->poster_path) {
            Storage::disk('public')->delete($movie->poster_path);
        }
        
        $movie->delete();

        return redirect()->route('admin.dashboard')
            ->with('success', 'Movie deleted successfully.');
    }

    public function comingSoon()
    {
        $comingSoonMovies = Movie::where('status', 'coming_soon')
            ->orderBy('release_date')
            ->get();

        return view('coming-soon', compact('comingSoonMovies'));
    }

    public function show(Movie $movie)
    {
        $movie->load(['showtimes' => function ($query) {
            $query->where('date', '>=', now()->toDateString())
                ->orderBy('date')
                ->orderBy('time');
        }]);

        // If the request is for admin, show admin view; otherwise public view
        if (request()->routeIs('admin.*')) {
            return view('admin.movies.show', compact('movie'));
        }

        return view('movies.show', compact('movie'));
    }

    public function setNotification(Request $request)
    {
        try {
            $validated = $request->validate([
                'movie_id' => 'required|exists:movies,id'
            ]);

            // Here you would typically:
            // 1. Create a notification record in the database
            // 2. Send a confirmation email
            // 3. Set up any necessary background jobs

            // For now, we'll just return a success response
            return response()->json([
                'message' => 'Notification set successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to set notification'
            ], 500);
        }
    }
}
