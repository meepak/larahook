<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Proviso</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        clifford: '#da373d',
                    }
                }
            }
        }
    </script>
</head>

</head>
<body class="bg-gray-100">

@include('partials.header')

<main class="container mx-auto mt-10">
    <section class="bg-white rounded-lg shadow-lg p-6 mb-10">
        <h2 class="text-2xl font-semibold text-indigo-700 mb-4">Latest Announcements</h2>
        <ul class="list-disc pl-6 space-y-2">
            <li>Thank you for trying out our home-grown webhook alternative.</li>
            <li>This project is a continuation of our store-requests project, completely rewritten as a server application.</li>
            <li>Multi-user support, authentication, and individual storage support.</li>
            <li>All submissions must be made through your designated subdomain.</li>
        </ul>
    </section>

    <section class="grid grid-cols-1 md:grid-cols-2 gap-10 bg-white rounded-lg shadow-lg p-6">
        <!-- Login Form -->
        <div>
            <h2 class="text-2xl font-semibold text-indigo-700 mb-4">Login</h2>
            <form action="/login" method="POST" class="space-y-6">
                @csrf
                <div class="flex flex-col">
                    <label for="email" class="text-lg font-medium text-gray-600 mb-2">Email Address</label>
                    <input type="email" name="email" id="loginEmail" placeholder="youremail@company.com"
                           class="border border-gray-300 p-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                           required>
                </div>
                <div class="flex flex-col">
                    <label for="otpInput" class="text-lg font-medium text-gray-600 mb-2">Authentication Code</label>
                    <input type="text" name="otp_input" id="otpInput" placeholder="Enter 2FA Code"
                           class="border border-gray-300 p-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                           required>
                </div>
                <div class="text-right">
                    <button type="submit"
                            class="bg-indigo-600 text-white font-semibold py-3 px-6 rounded-lg shadow-md hover:bg-indigo-700 focus:outline-none">
                        Log In
                    </button>
                </div>
            </form>
        </div>
        <!-- Registration Form -->
        <div>
            <h2 class="text-2xl font-semibold text-indigo-700 mb-4">Register</h2>
            <form action="/register" method="POST" class="space-y-6">
                @csrf
                <div class="flex flex-col">
                    <label for="email" class="text-lg font-medium text-gray-600 mb-2">Email Address</label>
                    <input type="email" name="email" id="email" placeholder="youremail@company.com"
                           class="border border-gray-300 p-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                           required>
                </div>
                <div class="text-right">
                    <button type="submit"
                            class="bg-indigo-600 text-white font-semibold py-3 px-6 rounded-lg shadow-md hover:bg-indigo-700 focus:outline-none">
                        Register & Get 2FA Code
                    </button>
                </div>
            </form>
        </div>
    </section>
</main>

@include('partials.footer')

</body>
</html>
