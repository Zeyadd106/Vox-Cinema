@extends('layouts.app')

@section('title', 'Site Settings')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold mb-8 text-white">Site Settings</h1>

        @if(session('success'))
            <div class="bg-green-500 text-white px-4 py-2 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-500 text-white px-4 py-2 rounded mb-4">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-gray-800 rounded-lg p-6">
            <form action="{{ route('admin.settings.update') }}" method="POST">
                @csrf

                <div class="space-y-6">
                    <!-- General Settings -->
                    <div>
                        <h2 class="text-xl font-semibold mb-4 text-white border-b border-gray-700 pb-2">General Settings</h2>
                        
                        <div class="grid gap-6 mb-6">
                            <div>
                                <label for="site_name" class="block text-sm font-medium text-gray-300 mb-2">Site Name</label>
                                <input type="text" name="site_name" id="site_name" 
                                    value="{{ old('site_name', setting('site_name', 'Vox Cinemas')) }}"
                                    class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white focus:outline-none focus:border-blue-500">
                            </div>

                            <div>
                                <label for="contact_email" class="block text-sm font-medium text-gray-300 mb-2">Contact Email</label>
                                <input type="email" name="contact_email" id="contact_email" 
                                    value="{{ old('contact_email', setting('contact_email')) }}"
                                    class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white focus:outline-none focus:border-blue-500">
                            </div>

                            <div>
                                <label for="phone_number" class="block text-sm font-medium text-gray-300 mb-2">Phone Number</label>
                                <input type="text" name="phone_number" id="phone_number" 
                                    value="{{ old('phone_number', setting('phone_number')) }}"
                                    class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white focus:outline-none focus:border-blue-500">
                            </div>

                            <div>
                                <label for="address" class="block text-sm font-medium text-gray-300 mb-2">Address</label>
                                <textarea name="address" id="address" rows="3" 
                                    class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white focus:outline-none focus:border-blue-500">{{ old('address', setting('address')) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Booking Settings -->
                    <div>
                        <h2 class="text-xl font-semibold mb-4 text-white border-b border-gray-700 pb-2">Booking Settings</h2>
                        
                        <div class="grid gap-6 mb-6">
                            <div>
                                <label for="booking_fee" class="block text-sm font-medium text-gray-300 mb-2">Booking Fee ($)</label>
                                <input type="number" step="0.01" name="booking_fee" id="booking_fee" 
                                    value="{{ old('booking_fee', setting('booking_fee', '2.00')) }}"
                                    class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white focus:outline-none focus:border-blue-500">
                            </div>

                            <div>
                                <label for="tax_rate" class="block text-sm font-medium text-gray-300 mb-2">Tax Rate (%)</label>
                                <input type="number" step="0.01" name="tax_rate" id="tax_rate" 
                                    value="{{ old('tax_rate', setting('tax_rate', '10.00')) }}"
                                    class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white focus:outline-none focus:border-blue-500">
                            </div>
                        </div>
                    </div>

                    <!-- Social Media Links -->
                    <div>
                        <h2 class="text-xl font-semibold mb-4 text-white border-b border-gray-700 pb-2">Social Media Links</h2>
                        
                        <div class="grid gap-6 mb-6">
                            <div>
                                <label for="facebook" class="block text-sm font-medium text-gray-300 mb-2">Facebook URL</label>
                                <input type="url" name="social_media_links[facebook]" id="facebook" 
                                    value="{{ old('social_media_links.facebook', setting('social_media_links.facebook')) }}"
                                    class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white focus:outline-none focus:border-blue-500">
                            </div>

                            <div>
                                <label for="twitter" class="block text-sm font-medium text-gray-300 mb-2">Twitter URL</label>
                                <input type="url" name="social_media_links[twitter]" id="twitter" 
                                    value="{{ old('social_media_links.twitter', setting('social_media_links.twitter')) }}"
                                    class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white focus:outline-none focus:border-blue-500">
                            </div>

                            <div>
                                <label for="instagram" class="block text-sm font-medium text-gray-300 mb-2">Instagram URL</label>
                                <input type="url" name="social_media_links[instagram]" id="instagram" 
                                    value="{{ old('social_media_links.instagram', setting('social_media_links.instagram')) }}"
                                    class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white focus:outline-none focus:border-blue-500">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6">
                    <button type="submit" 
                        class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-gray-800">
                        Save Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 