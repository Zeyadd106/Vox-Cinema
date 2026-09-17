@extends('admin.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-900">
                Booking Details #{{ $booking->id }}
            </h1>
            <div class="flex space-x-4">
                <span class="px-3 py-1 rounded-full text-sm font-semibold
                    {{ $booking->status === 'Paid' ? 'bg-green-100 text-green-800' : 
                       ($booking->status === 'Pending' ? 'bg-yellow-100 text-yellow-800' : 
                       'bg-red-100 text-red-800') }}">
                    {{ $booking->status }}
                </span>
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

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Movie Information -->
            <div class="bg-gray-50 rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Movie Details</h2>
                <div class="space-y-3">
                    <div>
                        <span class="text-gray-600 font-medium">Title:</span>
                        <span class="text-gray-900 ml-2">{{ $booking->showtime->movie->title ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600 font-medium">Hall:</span>
                        <span class="text-gray-900 ml-2">{{ $booking->showtime->hall->name ?? 'Not Assigned' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600 font-medium">Date:</span>
                        <span class="text-gray-900 ml-2">{{ optional($booking->showtime->date)->format('M d, Y') ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600 font-medium">Time:</span>
                        <span class="text-gray-900 ml-2">{{ optional($booking->showtime->time)->format('h:i A') ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>

            <!-- Customer Information -->
            <div class="bg-gray-50 rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Customer Details</h2>
                <div class="space-y-3">
                    <div>
                        <span class="text-gray-600 font-medium">Name:</span>
                        <span class="text-gray-900 ml-2">{{ $booking->user->name ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600 font-medium">Email:</span>
                        <span class="text-gray-900 ml-2">{{ $booking->user->email ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600 font-medium">Booking Reference:</span>
                        <span class="text-gray-900 ml-2">{{ $booking->booking_reference ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600 font-medium">Booking Date:</span>
                        <span class="text-gray-900 ml-2">{{ optional($booking->created_at)->format('M d, Y h:i A') ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>

            <!-- Seats Information -->
            <div class="bg-gray-50 rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Seat Details</h2>
                <div class="space-y-3">
                    <div>
                        <span class="text-gray-600 font-medium">Selected Seats:</span>
                        <div class="mt-2 flex flex-wrap gap-2">
                            @forelse($booking->seats as $seat)
                                <span class="px-2 py-1 bg-indigo-100 text-indigo-800 rounded">
                                    {{ $seat->row }}{{ $seat->number }}
                                </span>
                            @empty
                                <span class="text-gray-500">No seats selected</span>
                            @endforelse
                        </div>
                    </div>
                    <div class="mt-4">
                        <span class="text-gray-600 font-medium">Total Seats:</span>
                        <span class="text-gray-900 ml-2">{{ $booking->seats->count() }}</span>
                    </div>
                </div>
            </div>

            <!-- Payment Information -->
            <div class="bg-gray-50 rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Payment Details</h2>
                <div class="space-y-3">
                    <div>
                        <span class="text-gray-600 font-medium">Amount:</span>
                        <span class="text-gray-900 ml-2">${{ $booking->total_price }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600 font-medium">Payment Status:</span>
                        <span class="px-2 py-1 rounded text-sm font-semibold
                            {{ $booking->payment_status === 'paid' ? 'bg-green-100 text-green-800' : 
                               ($booking->payment_status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                               'bg-red-100 text-red-800') }}">
                            {{ ucfirst($booking->payment_status) }}
                        </span>
                    </div>
                    @if($booking->payment)
                        <div>
                            <span class="text-gray-600 font-medium">Payment Method:</span>
                            <span class="text-gray-900 ml-2">{{ ucfirst($booking->payment->payment_method) }}</span>
                        </div>
                        <div>
                            <span class="text-gray-600 font-medium">Transaction ID:</span>
                            <span class="text-gray-900 ml-2">{{ $booking->payment->transaction_id }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="mt-8 flex justify-end space-x-4">
            <a href="{{ route('admin.bookings.index') }}" 
               class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                Back to Bookings
            </a>
            @if($booking->status === 'Pending')
                <form action="{{ route('admin.bookings.cancel', $booking->id) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded"
                            onclick="return confirm('Are you sure you want to cancel this booking?')">
                        Cancel Booking
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection 