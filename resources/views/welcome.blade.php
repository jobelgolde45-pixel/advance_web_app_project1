<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Tester - Accommodation System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-6 text-center text-blue-800">API Tester - Accommodation System</h1>
        
        <!-- Token Storage Section -->
        <div class="mb-8 bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold mb-4 text-gray-700">Authentication Token</h2>
            <div class="flex gap-4 items-center">
                <input type="text" id="authToken" placeholder="Bearer Token" 
                       class="flex-1 p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <button onclick="copyToken()" class="px-4 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                    <i class="fas fa-copy"></i> Copy
                </button>
                <button onclick="clearToken()" class="px-4 py-3 bg-red-500 text-white rounded-lg hover:bg-red-600">
                    <i class="fas fa-trash"></i> Clear
                </button>
            </div>
            <p class="text-sm text-gray-500 mt-2">Token will be automatically saved after login/register</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Left Column: Public Routes -->
            <div class="space-y-8">
                <!-- Authentication Section -->
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h2 class="text-xl font-semibold mb-4 text-blue-600">
                        <i class="fas fa-user-circle mr-2"></i>Authentication
                    </h2>
                    
                    
                    <!-- Password Reset -->
                    <div>
                        <h3 class="font-medium mb-2 text-gray-700">3. Password Reset</h3>
                        <div class="space-y-3">
                            <form onsubmit="submitForm(event, 'forgotPassword')" class="space-y-3">
                                <input type="email" name="email" placeholder="Email for password reset" 
                                       class="w-full p-2 border border-gray-300 rounded" required>
                                <button type="submit" class="w-full py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600">
                                    <i class="fas fa-key mr-2"></i>Send Reset Link
                                </button>
                            </form>
                            <form onsubmit="submitForm(event, 'resetPassword')" class="space-y-3">
                                <input type="email" name="email" placeholder="Email" class="w-full p-2 border border-gray-300 rounded" required>
                                <input type="password" name="password" placeholder="New Password" class="w-full p-2 border border-gray-300 rounded" required>
                                <input type="password" name="password_confirmation" placeholder="Confirm Password" class="w-full p-2 border border-gray-300 rounded" required>
                                <input type="text" name="token" placeholder="Reset Token" class="w-full p-2 border border-gray-300 rounded" required>
                                <button type="submit" class="w-full py-2 bg-purple-600 text-white rounded hover:bg-purple-700">
                                    <i class="fas fa-sync-alt mr-2"></i>Reset Password
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Public Accommodation Routes -->
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h2 class="text-xl font-semibold mb-4 text-green-600">
                        <i class="fas fa-hotel mr-2"></i>Public Accommodations
                    </h2>
                    <div class="space-y-4">
                        <button onclick="fetchData('GET', '/api/accommodations')" class="w-full py-2 bg-gray-200 rounded hover:bg-gray-300">
                            Get All Accommodations
                        </button>
                        <div class="flex gap-2">
                            <input type="text" id="searchQuery" placeholder="Search query" class="flex-1 p-2 border border-gray-300 rounded">
                            <button onclick="searchAccommodations()" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">
                                Search
                            </button>
                        </div>
                        <div class="flex gap-2">
                            <input type="number" id="accommodationId" placeholder="Accommodation ID" class="flex-1 p-2 border border-gray-300 rounded">
                            <button onclick="fetchData('GET', '/api/accommodations/' + document.getElementById('accommodationId').value)" 
                                    class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                                Get Details
                            </button>
                        </div>
                        <div class="flex gap-2">
                            <input type="number" id="amenityId" placeholder="Accommodation ID" class="flex-1 p-2 border border-gray-300 rounded">
                            <button onclick="fetchData('GET', '/api/accommodations/' + document.getElementById('amenityId').value + '/amenities')" 
                                    class="px-4 py-2 bg-purple-500 text-white rounded hover:bg-purple-600">
                                Get Amenities
                            </button>
                        </div>
                        <button onclick="fetchData('GET', '/api/municipalities/bulan')" class="w-full py-2 bg-indigo-500 text-white rounded hover:bg-indigo-600">
                            Get Bulan Municipality Data
                        </button>
                    </div>
                </div>
            </div>

            <!-- Right Column: Protected Routes -->
            <div class="space-y-8">
                <!-- User Management -->
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h2 class="text-xl font-semibold mb-4 text-purple-600">
                        <i class="fas fa-users mr-2"></i>User Management (Protected)
                    </h2>
                    <div class="space-y-3">
                        <button onclick="fetchProtected('GET', '/api/user')" class="w-full py-2 bg-purple-500 text-white rounded hover:bg-purple-600">
                            Get Current User
                        </button>
                        
                        <h4 class="font-medium mt-4">Update Profile</h4>
                        <form onsubmit="submitProtectedForm(event, 'PUT', '/api/user/profile')" class="space-y-3">
                            <input type="text" name="first_name" placeholder="First Name" class="w-full p-2 border border-gray-300 rounded">
                            <input type="text" name="last_name" placeholder="Last Name" class="w-full p-2 border border-gray-300 rounded">
                            <input type="text" name="phone_number" placeholder="Phone Number" class="w-full p-2 border border-gray-300 rounded">
                            <button type="submit" class="w-full py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                                Update Profile
                            </button>
                        </form>
                        
                        <h4 class="font-medium mt-4">Update Profile Photo</h4>
                        <form onsubmit="submitProtectedForm(event, 'POST', '/api/user/profile/photo')" class="space-y-3">
                            <input type="file" name="photo" class="w-full p-2 border border-gray-300 rounded" required>
                            <button type="submit" class="w-full py-2 bg-green-500 text-white rounded hover:bg-green-600">
                                Upload Photo
                            </button>
                        </form>
                    </div>
                </div>
                
                <!-- Owner Routes -->
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h2 class="text-xl font-semibold mb-4 text-orange-600">
                        <i class="fas fa-user-tie mr-2"></i>Owner Management (Protected)
                    </h2>
                    <div class="space-y-3">
                        <button onclick="fetchProtected('GET', '/api/owner/accommodations')" class="w-full py-2 bg-orange-500 text-white rounded hover:bg-orange-600">
                            Get Owner Accommodations
                        </button>
                        
                        <h4 class="font-medium mt-4">Create Accommodation</h4>
                        <form onsubmit="submitProtectedForm(event, 'POST', '/api/owner/accommodations')" class="space-y-2">
                            <input type="text" name="name" placeholder="Accommodation Name" class="w-full p-2 border border-gray-300 rounded" required>
                            <textarea name="description" placeholder="Description" class="w-full p-2 border border-gray-300 rounded"></textarea>
                            <input type="text" name="address" placeholder="Address" class="w-full p-2 border border-gray-300 rounded" required>
                            <input type="number" name="price_per_night" placeholder="Price per Night" class="w-full p-2 border border-gray-300 rounded" required>
                            <select name="type" class="w-full p-2 border border-gray-300 rounded">
                                <option value="hotel">Hotel</option>
                                <option value="apartment">Apartment</option>
                                <option value="guesthouse">Guesthouse</option>
                                <option value="villa">Villa</option>
                            </select>
                            <button type="submit" class="w-full py-2 bg-green-600 text-white rounded hover:bg-green-700">
                                Create Accommodation
                            </button>
                        </form>
                        
                        <h4 class="font-medium mt-4">Manage Accommodation</h4>
                        <div class="flex gap-2">
                            <input type="number" id="ownerAccommodationId" placeholder="Accommodation ID" class="flex-1 p-2 border border-gray-300 rounded">
                            <button onclick="fetchProtected('DELETE', '/api/owner/accommodations/' + document.getElementById('ownerAccommodationId').value)" 
                                    class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Reservation Management -->
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h2 class="text-xl font-semibold mb-4 text-teal-600">
                        <i class="fas fa-calendar-check mr-2"></i>Reservation Management
                    </h2>
                    <div class="space-y-3">
                        <h4 class="font-medium">Create Reservation</h4>
                        <form onsubmit="submitProtectedForm(event, 'POST', '/api/reservations')" class="space-y-2">
                            <input type="number" name="accommodation_id" placeholder="Accommodation ID" class="w-full p-2 border border-gray-300 rounded" required>
                            <input type="date" name="check_in_date" class="w-full p-2 border border-gray-300 rounded" required>
                            <input type="date" name="check_out_date" class="w-full p-2 border border-gray-300 rounded" required>
                            <input type="number" name="number_of_guests" placeholder="Number of Guests" class="w-full p-2 border border-gray-300 rounded" required>
                            <button type="submit" class="w-full py-2 bg-teal-500 text-white rounded hover:bg-teal-600">
                                Create Reservation
                            </button>
                        </form>
                        
                        <div class="flex gap-2 mt-4">
                            <input type="number" id="reservationId" placeholder="Reservation ID" class="flex-1 p-2 border border-gray-300 rounded">
                            <button onclick="fetchProtected('GET', '/api/reservations/' + document.getElementById('reservationId').value)" 
                                    class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                                Get Details
                            </button>
                            <button onclick="fetchProtected('PUT', '/api/reservations/' + document.getElementById('reservationId').value + '/cancel')" 
                                    class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Admin Routes -->
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h2 class="text-xl font-semibold mb-4 text-red-600">
                        <i class="fas fa-user-shield mr-2"></i>Admin Routes (Protected)
                    </h2>
                    <div class="space-y-3">
                        <button onclick="fetchProtected('GET', '/api/admin/users')" class="w-full py-2 bg-red-500 text-white rounded hover:bg-red-600">
                            Get All Users
                        </button>
                        <button onclick="fetchProtected('GET', '/api/admin/accommodations')" class="w-full py-2 bg-red-600 text-white rounded hover:bg-red-700">
                            Get All Accommodations
                        </button>
                        
                        <div class="flex gap-2">
                            <input type="number" id="adminUserId" placeholder="User ID" class="flex-1 p-2 border border-gray-300 rounded">
                            <select id="userStatus" class="p-2 border border-gray-300 rounded">
                                <option value="active">Active</option>
                                <option value="suspended">Suspended</option>
                                <option value="banned">Banned</option>
                            </select>
                            <button onclick="updateUserStatus()" class="px-4 py-2 bg-purple-500 text-white rounded hover:bg-purple-600">
                                Update Status
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Notifications -->
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h2 class="text-xl font-semibold mb-4 text-indigo-600">
                        <i class="fas fa-bell mr-2"></i>Notifications (Protected)
                    </h2>
                    <div class="space-y-3">
                        <button onclick="fetchProtected('GET', '/api/notifications')" class="w-full py-2 bg-indigo-500 text-white rounded hover:bg-indigo-600">
                            Get Notifications
                        </button>
                        <div class="flex gap-2">
                            <input type="number" id="notificationId" placeholder="Notification ID" class="flex-1 p-2 border border-gray-300 rounded">
                            <button onclick="fetchProtected('PUT', '/api/notifications/' + document.getElementById('notificationId').value + '/mark-read')" 
                                    class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">
                                Mark as Read
                            </button>
                        </div>
                        <button onclick="fetchProtected('PUT', '/api/notifications/mark-all-read')" class="w-full py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                            Mark All as Read
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Response Display -->
        <div class="mt-8 bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold mb-4 text-gray-700">
                <i class="fas fa-code mr-2"></i>API Response
            </h2>
            <pre id="response" class="bg-gray-900 text-green-400 p-4 rounded-lg overflow-auto max-h-96"></pre>
        </div>
    </div>

    <script>
        // Load token from localStorage
        let authToken = localStorage.getItem('authToken') || '';
        document.getElementById('authToken').value = authToken;
        
        // Common headers
        const headers = {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        };
        
        // Copy token to clipboard
        function copyToken() {
            const tokenInput = document.getElementById('authToken');
            tokenInput.select();
            document.execCommand('copy');
            alert('Token copied to clipboard!');
        }
        
        // Clear token
        function clearToken() {
            localStorage.removeItem('authToken');
            authToken = '';
            document.getElementById('authToken').value = '';
            showResponse('Token cleared');
        }
        
        // Show response in the response panel
        function showResponse(data) {
            const responseElement = document.getElementById('response');
            if (typeof data === 'object') {
                responseElement.textContent = JSON.stringify(data, null, 2);
            } else {
                responseElement.textContent = data;
            }
        }
        
        // Submit form for public endpoints
        async function submitForm(event, endpoint) {
            event.preventDefault();
            const formData = new FormData(event.target);
            const data = Object.fromEntries(formData.entries());
            
            try {
                const response = await fetch(`/api/${endpoint}`, {
                    method: 'POST',
                    headers: headers,
                    body: JSON.stringify(data)
                });
                
                const result = await response.json();
                showResponse(result);
                
                // Save token if it's in the response
                if (result.token) {
                    authToken = `Bearer ${result.token}`;
                    localStorage.setItem('authToken', authToken);
                    document.getElementById('authToken').value = authToken;
                }
                if (result.access_token) {
                    authToken = `Bearer ${result.access_token}`;
                    localStorage.setItem('authToken', authToken);
                    document.getElementById('authToken').value = authToken;
                }
            } catch (error) {
                showResponse({ error: error.message });
            }
        }
        
        // Fetch data from public endpoints
        async function fetchData(method, url) {
            try {
                const response = await fetch(url, {
                    method: method,
                    headers: headers
                });
                const result = await response.json();
                showResponse(result);
            } catch (error) {
                showResponse({ error: error.message });
            }
        }
        
        // Search accommodations
        function searchAccommodations() {
            const query = document.getElementById('searchQuery').value;
            fetchData('GET', `/api/accommodations/search?query=${encodeURIComponent(query)}`);
        }
        
        // Update user status
        function updateUserStatus() {
            const userId = document.getElementById('adminUserId').value;
            const status = document.getElementById('userStatus').value;
            
            fetchProtected('PUT', `/api/admin/users/${userId}/status`, { status: status });
        }
        
        // Submit form for protected endpoints
        async function submitProtectedForm(event, method, url) {
            event.preventDefault();
            
            if (!authToken) {
                showResponse({ error: 'No authentication token found. Please login first.' });
                return;
            }
            
            const form = event.target;
            const formData = new FormData(form);
            
            // Handle file uploads differently
            if (form.querySelector('input[type="file"]')) {
                const fileData = new FormData(form);
                
                try {
                    const response = await fetch(url, {
                        method: method,
                        headers: {
                            'Authorization': authToken
                        },
                        body: fileData
                    });
                    const result = await response.json();
                    showResponse(result);
                } catch (error) {
                    showResponse({ error: error.message });
                }
            } else {
                const data = Object.fromEntries(formData.entries());
                
                try {
                    const response = await fetch(url, {
                        method: method,
                        headers: {
                            ...headers,
                            'Authorization': authToken
                        },
                        body: JSON.stringify(data)
                    });
                    const result = await response.json();
                    showResponse(result);
                } catch (error) {
                    showResponse({ error: error.message });
                }
            }
        }
        
        // Fetch data from protected endpoints
        async function fetchProtected(method, url, data = null) {
            if (!authToken) {
                showResponse({ error: 'No authentication token found. Please login first.' });
                return;
            }
            
            try {
                const options = {
                    method: method,
                    headers: {
                        ...headers,
                        'Authorization': authToken
                    }
                };
                
                if (data) {
                    options.body = JSON.stringify(data);
                }
                
                const response = await fetch(url, options);
                const result = await response.json();
                showResponse(result);
            } catch (error) {
                showResponse({ error: error.message });
            }
        }
        
        // Sample data for testing
        const sampleData = {
            register: {
                first_name: "Juan",
                last_name: "Dela Cruz",
                email: "juan.delacruz@example.com",
                password: "password123",
                password_confirmation: "password123",
                phone_number: "09123456789",
                user_type: "guest"
            },
            login: {
                email: "juan.delacruz@example.com",
                password: "password123"
            }
        };
        
        // Initialize forms with sample data
        document.addEventListener('DOMContentLoaded', function() {
            console.log('API Tester ready! Sample data loaded.');
            console.log('Register with:', sampleData.register);
            console.log('Login with:', sampleData.login);
        });
    </script>
</body>
</html>