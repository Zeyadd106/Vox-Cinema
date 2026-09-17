@extends('admin.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h1 class="text-2xl font-bold mb-6">Add New Movie</h1>

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.movies.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="duration" class="block text-sm font-medium text-gray-700">Duration (minutes)</label>
                    <input type="number" name="duration" id="duration" value="{{ old('duration') }}" required min="1" max="999"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="genre" class="block text-sm font-medium text-gray-700">Genre</label>
                    <select name="genre" id="genre" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Select a genre</option>
                        @foreach(['Action', 'Comedy', 'Drama', 'Horror', 'Sci-Fi', 'Adventure', 'Romance', 'Animation', 'Documentary', 'Thriller'] as $genre)
                            <option value="{{ $genre }}" {{ old('genre') == $genre ? 'selected' : '' }}>{{ $genre }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="rating" class="block text-sm font-medium text-gray-700">Rating</label>
                    <select name="rating" id="rating" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Select a rating</option>
                        @foreach(['G', 'PG', 'PG-13', 'R', 'NC-17'] as $rating)
                            <option value="{{ $rating }}" {{ old('rating') == $rating ? 'selected' : '' }}>{{ $rating }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="status" id="status" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="current" {{ old('status') == 'current' ? 'selected' : '' }}>Currently Showing</option>
                        <option value="coming_soon" {{ old('status') == 'coming_soon' ? 'selected' : '' }}>Coming Soon</option>
                    </select>
                </div>

                <div>
                    <label for="release_date" class="block text-sm font-medium text-gray-700">Release Date</label>
                    <input type="date" name="release_date" id="release_date" value="{{ old('release_date') }}" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="trailer_url" class="block text-sm font-medium text-gray-700">Trailer URL</label>
                    <input type="url" name="trailer_url" id="trailer_url" value="{{ old('trailer_url') }}" required
                           placeholder="https://www.youtube.com/watch?v=..."
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="poster" class="block text-sm font-medium text-gray-700">Movie Poster</label>
                    <input type="file" name="poster" id="poster" required accept="image/jpeg,image/png,image/jpg,image/gif"
                           class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    <p class="mt-1 text-sm text-gray-500">Max file size: 2MB. Accepted formats: JPEG, PNG, JPG, GIF</p>
                </div>
            </div>

            <div class="col-span-2">
                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                <textarea name="description" id="description" rows="4" required
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description') }}</textarea>
            </div>

            <div class="flex justify-end space-x-4">
                <a href="{{ route('admin.movies.index') }}" 
                   class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                    Cancel
                </a>
                <button type="submit" 
                        class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                    Create Movie
                </button>
            </div>
        </form>
    </div>
</div>
@endsection 