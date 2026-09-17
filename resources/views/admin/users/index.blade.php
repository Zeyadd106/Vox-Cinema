@extends('admin.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900">Manage Users</h1>
        <div class="flex items-center space-x-4">
            <div class="flex items-center bg-white rounded-lg shadow px-4 py-2">
                <span class="text-sm text-gray-600 mr-2">Total Users:</span>
                <span class="text-sm font-semibold text-gray-900">{{ $users->total() }}</span>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 text-gray-600 text-sm leading-normal">
                        <th class="py-3 px-6 text-left font-semibold">Name</th>
                        <th class="py-3 px-6 text-left font-semibold">Email</th>
                        <th class="py-3 px-6 text-left font-semibold">Bookings</th>
                        <th class="py-3 px-6 text-left font-semibold">Joined</th>
                        <th class="py-3 px-6 text-left font-semibold">Role</th>
                        <th class="py-3 px-6 text-left font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm">
                    @forelse($users as $user)
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="py-4 px-6">
                                <div class="flex items-center">
                                    <div class="mr-2">
                                        <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center">
                                            <span class="text-sm font-medium text-gray-600">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </span>
                                        </div>
                                    </div>
                                    <span class="font-medium">{{ $user->name }}</span>
                                </div>
                            </td>
                            <td class="py-4 px-6">{{ $user->email }}</td>
                            <td class="py-4 px-6">
                                <div class="flex items-center">
                                    <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded">
                                        {{ $user->bookings_count }} bookings
                                    </span>
                                </div>
                            </td>
                            <td class="py-4 px-6">{{ $user->created_at->format('M d, Y') }}</td>
                            <td class="py-4 px-6">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold
                                    {{ $user->is_admin ? 'bg-purple-100 text-purple-800' : 'bg-green-100 text-green-800' }}">
                                    {{ $user->is_admin ? 'Admin' : 'User' }}
                                </span>
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex items-center space-x-3">
                                    <form action="{{ route('admin.users.toggle-admin', $user) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" 
                                            class="text-sm {{ $user->is_admin ? 'text-red-600 hover:text-red-800' : 'text-blue-600 hover:text-blue-800' }} font-medium hover:underline"
                                            onclick="return confirm('Are you sure you want to {{ $user->is_admin ? 'remove admin privileges from' : 'make admin' }} {{ $user->name }}?')">
                                            {{ $user->is_admin ? 'Remove Admin' : 'Make Admin' }}
                                        </button>
                                    </form>
                                    <a href="{{ route('admin.users.show', $user) }}" 
                                       class="text-gray-600 hover:text-gray-900">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-4 px-6 text-center text-gray-500">
                                No users found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection 