<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - Accommodation System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .card-shadow {
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }
        .input-focus:focus {
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        .floating-label {
            transition: all 0.2s ease;
        }
        .floating-input:focus + .floating-label,
        .floating-input:not(:placeholder-shown) + .floating-label {
            transform: translateY(-1.5rem) scale(0.85);
            color: #667eea;
        }
        .social-btn {
            transition: all 0.3s ease;
        }
        .social-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body class="gradient-bg min-h-screen flex items-center justify-center p-4">
    <div class="max-w-6xl w-full">
        <div class="flex flex-col lg:flex-row rounded-2xl overflow-hidden card-shadow">
            
            <!-- Left Side - Welcome & Info -->
            <div class="lg:w-1/2 bg-white p-8 lg:p-12">
                <!-- Logo and Header -->
                <div class="mb-8">
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-home text-indigo-600 text-2xl"></i>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-800">Accommodation System</h1>
                            <p class="text-gray-600">Bulan, Sorsogon</p>
                        </div>
                    </div>
                    <h2 class="text-3xl font-bold text-gray-800 mb-2">Welcome Back!</h2>
                    <p class="text-gray-600">Sign in to your account to continue</p>
                </div>

                <!-- Login Form -->
                <form id="loginForm" class="space-y-6">
                    <!-- Email/Username Field -->
                    <div class="relative">
                        <input type="text" id="identifier" name="identifier" 
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:outline-none floating-input"
                               placeholder=" "
                               autocomplete="username">
                        <label for="identifier" class="floating-label absolute left-4 top-3 text-gray-500 bg-white px-1">
                            <i class="fas fa-envelope mr-2"></i>Email or Username
                        </label>
                        <div class="absolute right-3 top-3">
                            <i class="fas fa-user text-gray-400"></i>
                        </div>
                    </div>

                    <!-- Password Field -->
                    <div class="relative">
                        <input type="password" id="password" name="password"
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:outline-none floating-input"
                               placeholder=" "
                               autocomplete="current-password">
                        <label for="password" class="floating-label absolute left-4 top-3 text-gray-500 bg-white px-1">
                            <i class="fas fa-lock mr-2"></i>Password
                        </label>
                        <button type="button" onclick="togglePassword()" 
                                class="absolute right-3 top-3 text-gray-500 hover:text-gray-700">
                            <i id="passwordIcon" class="fas fa-eye"></i>
                        </button>
                    </div>

                    <!-- Remember Me & Forgot Password -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input type="checkbox" id="remember" name="remember" 
                                   class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                            <label for="remember" class="ml-2 text-sm text-gray-700">
                                Remember me
                            </label>
                        </div>
                        <a href="#" onclick="showForgotPassword()" 
                           class="text-sm text-indigo-600 hover:text-indigo-800 hover:underline">
                            Forgot password?
                        </a>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" id="submitBtn"
                            class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-3 rounded-xl font-semibold hover:from-indigo-700 hover:to-purple-700 transition-all duration-300 flex items-center justify-center">
                        <i class="fas fa-sign-in-alt mr-2"></i>
                        <span>Sign In</span>
                        <span id="loadingSpinner" class="ml-2 hidden">
                            <i class="fas fa-spinner fa-spin"></i>
                        </span>
                    </button>

                    

                    <!-- Register Link -->
                    <div class="text-center pt-6 border-t border-gray-200">
                        <p class="text-gray-600">
                            Don't have an account?
                            <a href="/" class="text-indigo-600 font-semibold hover:text-indigo-800 hover:underline">
                                Create account
                            </a>
                        </p>
                    </div>
                </form>
            </div>

            <!-- Right Side - Hero Image & Features -->
            <div class="lg:w-1/2 bg-gradient-to-br from-indigo-700 to-purple-800 p-8 lg:p-12 text-white hidden lg:block">
                <div class="h-full flex flex-col justify-between">
                    <!-- Hero Content -->
                    <div>
                        <div class="mb-12">
                            <div class="inline-block px-4 py-2 bg-white/10 backdrop-blur-sm rounded-full mb-6">
                                <span class="text-sm font-semibold">Bulan Accommodation Portal</span>
                            </div>
                            <h2 class="text-4xl font-bold mb-6">Find Your Perfect Stay in Bulan</h2>
                            <p class="text-indigo-100 text-lg mb-8">
                                Connect with local property owners or find your next rental in beautiful Bulan, Sorsogon.
                            </p>
                        </div>

                        <!-- Features -->
                        <div class="space-y-8">
                            <div class="flex items-start space-x-4">
                                <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-search text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="font-bold text-xl mb-2">Discover Accommodations</h3>
                                    <p class="text-indigo-200">Browse verified properties with detailed amenities and reviews.</p>
                                </div>
                            </div>

                            <div class="flex items-start space-x-4">
                                <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-home text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="font-bold text-xl mb-2">List Your Property</h3>
                                    <p class="text-indigo-200">Property owners can easily list and manage their accommodations.</p>
                                </div>
                            </div>

                            <div class="flex items-start space-x-4">
                                <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-shield-alt text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="font-bold text-xl mb-2">Secure Platform</h3>
                                    <p class="text-indigo-200">Verified users and secure payment processing for peace of mind.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Testimonials -->
                    <div class="mt-12 pt-8 border-t border-white/20">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 rounded-full bg-white/10 flex items-center justify-center">
                                <i class="fas fa-user text-xl"></i>
                            </div>
                            <div>
                                <p class="italic">"Found my perfect apartment in Zone 1 within days!"</p>
                                <p class="text-sm text-indigo-200 mt-1">— Maria Santos, Tenant</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Demo Accounts Panel -->
        <div class="mt-8 bg-white/10 backdrop-blur-sm rounded-2xl p-6 card-shadow">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-white font-bold text-lg">
                    <i class="fas fa-vial mr-2"></i>Demo Accounts (Testing Only)
                </h3>
                <button onclick="toggleDemoAccounts()" class="text-white hover:text-indigo-200">
                    <i id="demoToggleIcon" class="fas fa-chevron-down"></i>
                </button>
            </div>
            
            <div id="demoAccounts" class="hidden">
                <div class="grid md:grid-cols-3 gap-4">
                    <!-- Tenant Account -->
                    <div class="bg-white/5 rounded-xl p-4 border border-white/10">
                        <div class="flex items-center mb-3">
                            <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-user text-white text-sm"></i>
                            </div>
                            <h4 class="font-semibold text-white">Tenant Account</h4>
                        </div>
                        <div class="space-y-2">
                            <p class="text-indigo-200 text-sm">Username: <span class="text-white font-mono">juan.tenant</span></p>
                            <p class="text-indigo-200 text-sm">Password: <span class="text-white font-mono">Password123</span></p>
                            <button onclick="fillDemoCredentials('tenant')" 
                                    class="w-full mt-2 bg-white/20 hover:bg-white/30 text-white py-2 rounded-lg text-sm transition-colors">
                                Use This Account
                            </button>
                        </div>
                    </div>

                    <!-- Owner Account -->
                    <div class="bg-white/5 rounded-xl p-4 border border-white/10">
                        <div class="flex items-center mb-3">
                            <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-home text-white text-sm"></i>
                            </div>
                            <h4 class="font-semibold text-white">Owner Account</h4>
                        </div>
                        <div class="space-y-2">
                            <p class="text-indigo-200 text-sm">Username: <span class="text-white font-mono">maria.owner</span></p>
                            <p class="text-indigo-200 text-sm">Password: <span class="text-white font-mono">Password123</span></p>
                            <button onclick="fillDemoCredentials('owner')" 
                                    class="w-full mt-2 bg-white/20 hover:bg-white/30 text-white py-2 rounded-lg text-sm transition-colors">
                                Use This Account
                            </button>
                        </div>
                    </div>

                    <!-- Admin Account -->
                    <div class="bg-white/5 rounded-xl p-4 border border-white/10">
                        <div class="flex items-center mb-3">
                            <div class="w-8 h-8 bg-red-500 rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-user-shield text-white text-sm"></i>
                            </div>
                            <h4 class="font-semibold text-white">Admin Account</h4>
                        </div>
                        <div class="space-y-2">
                            <p class="text-indigo-200 text-sm">Username: <span class="text-white font-mono">admin</span></p>
                            <p class="text-indigo-200 text-sm">Password: <span class="text-white font-mono">Admin123</span></p>
                            <button onclick="fillDemoCredentials('admin')" 
                                    class="w-full mt-2 bg-white/20 hover:bg-white/30 text-white py-2 rounded-lg text-sm transition-colors">
                                Use This Account
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Forgot Password Modal -->
    <div id="forgotPasswordModal" class="fixed inset-0 bg-black/50 flex items-center justify-center p-4 hidden z-50">
        <div class="bg-white rounded-2xl max-w-md w-full p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-gray-800">Reset Password</h3>
                <button onclick="hideForgotPassword()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="forgotPasswordForm" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-envelope mr-2"></i>Enter your email
                    </label>
                    <input type="email" id="resetEmail" name="email" required
                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:outline-none"
                           placeholder="you@example.com">
                </div>
                
                <div class="text-sm text-gray-600 mb-4">
                    We'll send you a link to reset your password.
                </div>
                
                <div class="flex space-x-3">
                    <button type="button" onclick="hideForgotPassword()"
                            class="flex-1 px-4 py-3 border-2 border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" id="resetSubmitBtn"
                            class="flex-1 px-4 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700">
                        <span>Send Reset Link</span>
                        <span id="resetLoading" class="ml-2 hidden">
                            <i class="fas fa-spinner fa-spin"></i>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Notification Toast -->
    <div id="notification" 
         class="fixed top-4 right-4 max-w-sm bg-white rounded-xl shadow-lg border p-4 transform translate-x-full transition-transform duration-300 z-50">
        <div class="flex items-start">
            <div id="notificationIcon" class="flex-shrink-0 mr-3 mt-1"></div>
            <div class="flex-1">
                <h4 id="notificationTitle" class="font-semibold"></h4>
                <p id="notificationMessage" class="text-sm mt-1 text-gray-600"></p>
            </div>
            <button onclick="hideNotification()" class="ml-4 text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div id="notificationProgress" class="h-1 bg-gray-200 rounded-full mt-3 overflow-hidden">
            <div class="h-full bg-indigo-500 rounded-full w-0"></div>
        </div>
    </div>

    
    <script>
    // Demo credentials
    const demoAccounts = {
        tenant: {
            identifier: 'juan.tenant',
            password: 'Password123'
        },
        owner: {
            identifier: 'maria.owner',
            password: 'Password123'
        },
        admin: {
            identifier: 'admin',
            password: 'Admin123'
        }
    };

    // Form submission
    document.getElementById('loginForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const submitBtn = document.getElementById('submitBtn');
        const loadingSpinner = document.getElementById('loadingSpinner');
        
        // Get form data
        const identifier = document.getElementById('identifier').value;
        const password = document.getElementById('password').value;
        const remember = document.getElementById('remember').checked;
        
        // Validate
        if (!identifier || !password) {
            showNotification('Please fill in all fields', 'error');
            return;
        }
        
        // Show loading
        submitBtn.disabled = true;
        loadingSpinner.classList.remove('hidden');
        
        try {
            // Prepare login data
            const loginData = {
                login: identifier, // Send as identifier
                password: password,
                remember: remember
            };
            
            // Get CSRF token from meta tag (if using Laravel)
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            
            // Headers configuration
            const headers = {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            };
            
            // Add CSRF token if available
            if (csrfToken) {
                headers['X-CSRF-TOKEN'] = csrfToken;
            }
            
            // Send login request
            const response = await fetch('/api/login', {
                method: 'POST',
                headers: headers,
                body: JSON.stringify(loginData)
            });
            
            // Parse response
            const result = await response.json();
            
            if (response.ok) {
                showNotification('Login successful! Redirecting...', 'success');
                
                // Save token if present
                if (result.token || result.access_token) {
                    const token = result.token || result.access_token;
                    localStorage.setItem('authToken', token.startsWith('Bearer ') ? token : `Bearer ${token}`);
                    localStorage.setItem('userData', JSON.stringify(result.user || {}));
                    
                    // Remember me
                    if (remember) {
                        localStorage.setItem('rememberedUser', identifier);
                    } else {
                        localStorage.removeItem('rememberedUser');
                    }
                }
                
                // Redirect based on user type
                setTimeout(() => {
                    const userType = result.user?.user_type || result.user?.role || 'tenant';
                    const redirects = {
                        'admin': '/admin/dashboard',
                        'owner': '/owner/dashboard',
                        'tenant': '/tenant/dashboard'
                    };
                    
                    window.location.href = redirects[userType.toLowerCase()] || '/dashboard';
                }, 1500);
                
            } else {
                // Handle different error types
                if (response.status === 422 && result.errors) {
                    // Validation errors
                    const errorMessages = Object.values(result.errors).flat().join(', ');
                    showNotification(errorMessages, 'error');
                } else if (response.status === 401) {
                    showNotification(result.message || 'Invalid credentials. Please check your username/email and password.', 'error');
                } else {
                    showNotification(result.message || 'Login failed. Please try again.', 'error');
                }
                submitBtn.disabled = false;
                loadingSpinner.classList.add('hidden');
            }
            
        } catch (error) {
            console.error('Login error:', error);
            showNotification('Network error. Please check your connection and try again.', 'error');
            submitBtn.disabled = false;
            loadingSpinner.classList.add('hidden');
        }
    });

    // Forgot password form
    document.getElementById('forgotPasswordForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const submitBtn = document.getElementById('resetSubmitBtn');
        const loadingSpinner = document.getElementById('resetLoading');
        const email = document.getElementById('resetEmail').value;
        
        if (!validateEmail(email)) {
            showNotification('Please enter a valid email address', 'error');
            return;
        }
        
        // Show loading
        submitBtn.disabled = true;
        loadingSpinner.classList.remove('hidden');
        
        try {
            // Get CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            
            const headers = {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            };
            
            if (csrfToken) {
                headers['X-CSRF-TOKEN'] = csrfToken;
            }
            
            const response = await fetch('/api/password/forgot', {
                method: 'POST',
                headers: headers,
                body: JSON.stringify({ email: email })
            });
            
            const result = await response.json();
            
            if (response.ok) {
                showNotification('Password reset link sent! Check your email.', 'success');
                hideForgotPassword();
            } else {
                if (response.status === 422 && result.errors) {
                    const errorMessages = Object.values(result.errors).flat().join(', ');
                    showNotification(errorMessages, 'error');
                } else {
                    showNotification(result.message || 'Failed to send reset link', 'error');
                }
            }
            
        } catch (error) {
            console.error('Forgot password error:', error);
            showNotification('Network error. Please try again.', 'error');
        } finally {
            submitBtn.disabled = false;
            loadingSpinner.classList.add('hidden');
        }
    });

    // Utility functions
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const icon = document.getElementById('passwordIcon');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.className = 'fas fa-eye-slash';
        } else {
            passwordInput.type = 'password';
            icon.className = 'fas fa-eye';
        }
    }

    function fillDemoCredentials(type) {
        const account = demoAccounts[type];
        if (account) {
            document.getElementById('identifier').value = account.identifier;
            document.getElementById('password').value = account.password;
            document.getElementById('remember').checked = true;
            
            showNotification(`${type.charAt(0).toUpperCase() + type.slice(1)} credentials loaded`, 'info');
        }
    }

    function toggleDemoAccounts() {
        const demoDiv = document.getElementById('demoAccounts');
        const icon = document.getElementById('demoToggleIcon');
        
        if (demoDiv.classList.contains('hidden')) {
            demoDiv.classList.remove('hidden');
            icon.className = 'fas fa-chevron-up';
        } else {
            demoDiv.classList.add('hidden');
            icon.className = 'fas fa-chevron-down';
        }
    }

    function showForgotPassword() {
        document.getElementById('forgotPasswordModal').classList.remove('hidden');
    }

    function hideForgotPassword() {
        document.getElementById('forgotPasswordModal').classList.add('hidden');
        document.getElementById('forgotPasswordForm').reset();
    }

    function socialLogin(provider) {
        showNotification(`Connecting with ${provider}... (Demo)`, 'info');
        // In real app: window.location.href = `/auth/${provider}`;
    }

    function validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    // Fixed Notification system
    function showNotification(message, type = 'info') {
        const notification = document.getElementById('notification');
        const icon = document.getElementById('notificationIcon');
        const title = document.getElementById('notificationTitle');
        const messageEl = document.getElementById('notificationMessage');
        const progressBar = notification.querySelector('#notificationProgress div');
        
        const config = {
            success: {
                icon: '<i class="fas fa-check-circle text-green-500 text-xl"></i>',
                title: 'Success',
                bgColor: 'green'
            },
            error: {
                icon: '<i class="fas fa-exclamation-circle text-red-500 text-xl"></i>',
                title: 'Error',
                bgColor: 'red'
            },
            info: {
                icon: '<i class="fas fa-info-circle text-blue-500 text-xl"></i>',
                title: 'Info',
                bgColor: 'blue'
            },
            warning: {
                icon: '<i class="fas fa-exclamation-triangle text-yellow-500 text-xl"></i>',
                title: 'Warning',
                bgColor: 'yellow'
            }
        };
        
        const cfg = config[type] || config.info;
        
        // Clear previous notification classes
        notification.classList.remove(
            'bg-green-50', 'border-green-200',
            'bg-red-50', 'border-red-200',
            'bg-blue-50', 'border-blue-200',
            'bg-yellow-50', 'border-yellow-200'
        );
        
        // Add new classes individually
        if (cfg.bgColor === 'green') {
            notification.classList.add('bg-green-50', 'border-green-200');
            title.className = 'font-semibold text-green-700';
        } else if (cfg.bgColor === 'red') {
            notification.classList.add('bg-red-50', 'border-red-200');
            title.className = 'font-semibold text-red-700';
        } else if (cfg.bgColor === 'blue') {
            notification.classList.add('bg-blue-50', 'border-blue-200');
            title.className = 'font-semibold text-blue-700';
        } else if (cfg.bgColor === 'yellow') {
            notification.classList.add('bg-yellow-50', 'border-yellow-200');
            title.className = 'font-semibold text-yellow-700';
        }
        
        icon.innerHTML = cfg.icon;
        title.textContent = cfg.title;
        messageEl.textContent = message;
        
        notification.classList.remove('translate-x-full');
        
        // Reset and animate progress bar
        progressBar.style.width = '0%';
        void progressBar.offsetWidth; // Trigger reflow
        progressBar.style.width = '100%';
        progressBar.style.transition = 'width 5s linear';
        
        // Auto-hide after 5 seconds
        setTimeout(() => {
            hideNotification();
        }, 5000);
    }

    function hideNotification() {
        const notification = document.getElementById('notification');
        notification.classList.add('translate-x-full');
        
        // Reset progress bar
        const progressBar = notification.querySelector('#notificationProgress div');
        progressBar.style.transition = 'none';
        progressBar.style.width = '0%';
    }

    // Auto-fill remembered user
    document.addEventListener('DOMContentLoaded', function() {
        const rememberedUser = localStorage.getItem('rememberedUser');
        if (rememberedUser) {
            document.getElementById('identifier').value = rememberedUser;
            document.getElementById('remember').checked = true;
        }
        
        // Check URL for errors
        const urlParams = new URLSearchParams(window.location.search);
        const error = urlParams.get('error');
        const message = urlParams.get('message');
        
        if (error) {
            showNotification(message || 'Please check your credentials', 'error');
        }
        
        // Show welcome message
        setTimeout(() => {
            showNotification('Welcome! Try demo accounts for quick testing.', 'info');
        }, 1000);
        
        // Add CSRF token to all forms
        addCsrfTokenToForms();
    });

    // Add CSRF token to forms
    function addCsrfTokenToForms() {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (csrfToken) {
            // Add hidden input to forms
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                if (!form.querySelector('input[name="_token"]')) {
                    const tokenInput = document.createElement('input');
                    tokenInput.type = 'hidden';
                    tokenInput.name = '_token';
                    tokenInput.value = csrfToken;
                    form.appendChild(tokenInput);
                }
            });
        }
    }

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl+Shift+T for tenant demo
        if (e.ctrlKey && e.shiftKey && e.key === 'T') {
            e.preventDefault();
            fillDemoCredentials('tenant');
        }
        // Ctrl+Shift+O for owner demo
        if (e.ctrlKey && e.shiftKey && e.key === 'O') {
            e.preventDefault();
            fillDemoCredentials('owner');
        }
        // Ctrl+Shift+A for admin demo
        if (e.ctrlKey && e.shiftKey && e.key === 'A') {
            e.preventDefault();
            fillDemoCredentials('admin');
        }
        // Escape to close modals
        if (e.key === 'Escape') {
            hideForgotPassword();
        }
        // Enter to submit form (if focused)
        if (e.key === 'Enter' && !e.target.closest('#forgotPasswordModal')) {
            // Let the form handle it naturally
        }
    });

    // Add floating label functionality
    document.querySelectorAll('.floating-input').forEach(input => {
        input.addEventListener('blur', function() {
            if (this.value.trim() !== '') {
                this.classList.add('has-value');
            } else {
                this.classList.remove('has-value');
            }
        });
        
        input.addEventListener('input', function() {
            if (this.value.trim() !== '') {
                this.classList.add('has-value');
            } else {
                this.classList.remove('has-value');
            }
        });
        
        // Initialize on page load
        if (input.value.trim() !== '') {
            input.classList.add('has-value');
        }
    });

    // Form validation helpers
    function validateFormField(field, type = 'text') {
        const value = field.value.trim();
        
        if (!value) {
            return 'This field is required';
        }
        
        if (type === 'email' && !validateEmail(value)) {
            return 'Please enter a valid email address';
        }
        
        if (type === 'password' && value.length < 6) {
            return 'Password must be at least 6 characters';
        }
        
        return null; // No error
    }

    // Add input validation on blur
    document.getElementById('identifier').addEventListener('blur', function() {
        const error = validateFormField(this, this.value.includes('@') ? 'email' : 'text');
        if (error) {
            this.classList.add('border-red-500');
        } else {
            this.classList.remove('border-red-500');
        }
    });

    document.getElementById('password').addEventListener('blur', function() {
        const error = validateFormField(this, 'password');
        if (error) {
            this.classList.add('border-red-500');
        } else {
            this.classList.remove('border-red-500');
        }
    });

    // Add a helper to check if user is already logged in
    async function checkAuthStatus() {
        const token = localStorage.getItem('authToken');
        if (!token) return false;
        
        try {
            const response = await fetch('/api/user', {
                headers: {
                    'Authorization': token,
                    'Accept': 'application/json'
                }
            });
            
            if (response.ok) {
                const user = await response.json();
                // Redirect to appropriate dashboard if already logged in
                const userType = user.user_type || user.role || 'tenant';
                const redirects = {
                    'admin': '/admin/dashboard',
                    'owner': '/owner/dashboard',
                    'tenant': '/tenant/dashboard'
                };
                
                const redirectUrl = redirects[userType.toLowerCase()];
                if (redirectUrl && !window.location.pathname.includes(redirectUrl)) {
                    window.location.href = redirectUrl;
                }
                return true;
            }
        } catch (error) {
            console.log('Auth check failed, user not logged in');
        }
        
        return false;
    }

    // Check auth status on page load
    checkAuthStatus();
</script>

</body>
</html>