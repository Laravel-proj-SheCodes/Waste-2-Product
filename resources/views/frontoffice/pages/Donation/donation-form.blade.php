<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Donation</title>
    <!-- Include your project's CSS (e.g., Tailwind, Bootstrap, or custom styles) -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">Submit a Donation</h1>

        @if (session('success'))
            <div class="bg-green-100 text-green-700 p-4 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-100 text-red-700 p-4 rounded mb-4">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('donations.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="location" class="block text-sm font-medium">Location</label>
                <input type="text" name="location" id="location" value="{{ old('location') }}" class="w-full border rounded p-2" required>
            </div>
            <div>
                <label for="product_name" class="block text-sm font-medium">Product Name</label>
                <input type="text" name="product_name" id="product_name" value="{{ old('product_name') }}" class="w-full border rounded p-2" required>
            </div>
            <div>
                <label for="quantity" class="block text-sm font-medium">Quantity</label>
                <input type="number" name="quantity" id="quantity" value="{{ old('quantity') }}" class="w-full border rounded p-2" required>
            </div>
            <div>
                <label for="type" class="block text-sm font-medium">Type</label>
                <select name="type" id="type" class="w-full border rounded p-2" required>
                    <option value="recyclable" {{ old('type') == 'recyclable' ? 'selected' : '' }}>Recyclable</option>
                    <option value="renewable" {{ old('type') == 'renewable' ? 'selected' : '' }}>Renewable</option>
                </select>
            </div>
            <div>
                <label for="description" class="block text-sm font-medium">Description (Optional)</label>
                <textarea name="description" id="description" class="w-full border rounded p-2">{{ old('description') }}</textarea>
            </div>
            <div>
                <label for="donation_date" class="block text-sm font-medium">Donation Date</label>
                <input type="date" name="donation_date" id="donation_date" value="{{ old('donation_date') }}" class="w-full border rounded p-2" required>
            </div>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Submit Donation</button>
        </form>
    </div>
</body>
</html>