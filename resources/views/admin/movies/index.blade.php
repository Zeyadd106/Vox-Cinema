@extends('layouts.admin')

@section('title', 'Manage Movies')

@section('styles')
<style>
    .admin-movies {
        padding-top: 120px;
        padding-bottom: var(--spacing-lg);
        width: 90%;
        max-width: 1200px;
        margin: 0 auto;
        min-height: calc(100vh - 200px);
    }

    .admin-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }

    .admin-title {
        font-size: 2.5rem;
        color: var(--color-primary);
        margin: 0;
    }

    .add-movie-btn {
        background: var(--color-primary);
        color: white;
        padding: 0.8rem 1.5rem;
        border-radius: 8px;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .add-movie-btn:hover {
        background: var(--color-primary-dark);
        transform: translateY(-2px);
    }

    .movies-table {
        width: 100%;
        border-collapse: collapse;
        background: rgba(26, 26, 26, 0.95);
        border-radius: 10px;
        overflow: hidden;
    }

    .movies-table th,
    .movies-table td {
        padding: 1rem;
        text-align: left;
        border-bottom: 1px solid #333;
    }

    .movies-table th {
        background: rgba(227, 24, 55, 0.1);
        color: var(--color-primary);
        font-weight: 600;
    }

    .movies-table tr:hover {
        background: rgba(227, 24, 55, 0.05);
    }

    .movie-poster-preview {
        width: 60px;
        height: 90px;
        object-fit: cover;
        border-radius: 4px;
    }

    .action-buttons {
        display: flex;
        gap: 0.5rem;
    }

    .edit-btn,
    .delete-btn {
        padding: 0.5rem 1rem;
        border-radius: 4px;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .edit-btn {
        background: #2563eb;
        color: white;
    }

    .delete-btn {
        background: #dc2626;
        color: white;
    }

    .edit-btn:hover,
    .delete-btn:hover {
        opacity: 0.9;
        transform: translateY(-1px);
    }

    @media (max-width: 768px) {
        .admin-movies {
            width: 95%;
            padding-top: 100px;
        }

        .admin-title {
            font-size: 2rem;
        }

        .movies-table {
            display: block;
            overflow-x: auto;
        }
    }
</style>
@endsection

@section('content')
<div class="admin-movies">
    <div class="admin-header">
        <h1 class="admin-title">Manage Movies</h1>
        <a href="{{ route('admin.movies.create') }}" class="add-movie-btn">
            <i class="fas fa-plus"></i> Add New Movie
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="table-responsive">
        <table class="movies-table">
            <thead>
                <tr>
                    <th>Poster</th>
                    <th>Title</th>
                    <th>Release Date</th>
                    <th>Duration</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($movies as $movie)
                    <tr>
                        <td>
                            <img src="{{ asset($movie->poster_url) }}" alt="{{ $movie->title }}" class="movie-poster-preview">
                        </td>
                        <td>{{ $movie->title }}</td>
                        <td>{{ $movie->release_date->format('M d, Y') }}</td>
                        <td>{{ $movie->duration }} min</td>
                        <td>{{ ucfirst($movie->status) }}</td>
                        <td class="action-buttons">
                            <a href="{{ route('admin.movies.edit', $movie) }}" class="edit-btn">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form action="{{ route('admin.movies.destroy', $movie) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="delete-btn" onclick="return confirm('Are you sure you want to delete this movie?')">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection 