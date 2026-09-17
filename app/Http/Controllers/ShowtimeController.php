<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Showtime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ShowtimeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $showtimes = Showtime::with('movie')->get();

        if (request()->wantsJson()) {
            return response()->json(['showtimes' => $showtimes]);
        }

        return view('showtimes.index', compact('showtimes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $movies = Movie::all();
        return view('showtimes.create', compact('movies'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'movie_id' => 'required|exists:movies,id',
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required',
        ]);

        if ($validator->fails()) {
            if ($request->wantsJson()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            return redirect()->back()->withErrors($validator)->withInput();
        }

        $showtime = Showtime::create([
            'movie_id' => $request->movie_id,
            'date' => $request->date,
            'time' => $request->time,
        ]);

        if ($request->wantsJson()) {
            return response()->json(['showtime' => $showtime, 'message' => 'Showtime created successfully'], 201);
        }

        return redirect()->route('admin.showtimes.index')->with('success', 'Showtime created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Showtime $showtime)
    {
        $showtime->load('movie');

        if (request()->wantsJson()) {
            return response()->json(['showtime' => $showtime]);
        }

        return view('showtimes.show', compact('showtime'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Showtime $showtime)
    {
        $movies = Movie::all();
        return view('showtimes.edit', compact('showtime', 'movies'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Showtime $showtime)
    {
        $validator = Validator::make($request->all(), [
            'movie_id' => 'sometimes|exists:movies,id',
            'date' => 'sometimes|date|after_or_equal:today',
            'time' => 'sometimes',
        ]);

        if ($validator->fails()) {
            if ($request->wantsJson()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            return redirect()->back()->withErrors($validator)->withInput();
        }

        $showtime->update($request->all());

        if ($request->wantsJson()) {
            return response()->json(['showtime' => $showtime, 'message' => 'Showtime updated successfully']);
        }

        return redirect()->route('admin.showtimes.index')->with('success', 'Showtime updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Showtime $showtime)
    {
        $showtime->delete();

        if (request()->wantsJson()) {
            return response()->json(['message' => 'Showtime deleted successfully']);
        }

        return redirect()->route('admin.showtimes.index')->with('success', 'Showtime deleted successfully');
    }

    /**
     * Get showtimes for a specific movie.
     */
    public function getShowtimesByMovie($movieId)
    {
        $showtimes = Showtime::where('movie_id', $movieId)
            ->where('date', '>=', now()->toDateString())
            ->orderBy('date')
            ->orderBy('time')
            ->get();

        return response()->json(['showtimes' => $showtimes]);
    }
}
