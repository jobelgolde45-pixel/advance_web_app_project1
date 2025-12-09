<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Accommodation System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }
        .tab-button {
            transition: all 0.3s ease;
        }
        .tab-button.active {
            background-color: #3b82f6;
            color: white;
        }
        .step-indicator {
            position: relative;
        }
        .step-indicator:not(:last-child):after {
            content: '';
            position: absolute;
            right: -1rem;
            top: 50%;
            transform: translateY(-50%);
            width: 2rem;
            height: 2px;
            background-color: #d1d5db;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-50 min-h-screen">
    <div class="container mx-auto px-4 py-8 max-w-6xl">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-blue-800 mb-2">
                <i class="fas fa-home mr-2"></i>Accommodation System
            </h1>
            <p class="text-gray-600">Create your account to book accommodations or list your property</p>
        </div>

        <!-- Registration Card -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <!-- Progress Steps -->
            <div class="bg-gray-50 px-6 py-4 border-b">
                <div class="flex justify-between items-center max-w-2xl mx-auto">
                    <div class="step-indicator flex flex-col items-center">
                        <div class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center font-semibold mb-1">
                            1
                        </div>
                        <span class="text-sm font-medium text-gray-700">Account Type</span>
                    </div>
                    <div class="step-indicator flex flex-col items-center">
                        <div class="w-8 h-8 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center font-semibold mb-1">
                            2
                        </div>
                        <span class="text-sm font-medium text-gray-500">Personal Info</span>
                    </div>
                    <div class="step-indicator flex flex-col items-center">
                        <div class="w-8 h-8 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center font-semibold mb-1">
                            3
                        </div>
                        <span class="text-sm font-medium text-gray-500">Address</span>
                    </div>
                    <div class="flex flex-col items-center">
                        <div class="w-8 h-8 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center font-semibold mb-1">
                            4
                        </div>
                        <span class="text-sm font-medium text-gray-500">Credentials</span>
                    </div>
                </div>
            </div>

            <div class="p-6 md:p-8">
                <!-- Account Type Selection -->
                <div id="step1" class="tab-content active">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">Select Account Type</h2>
                    
                    <div class="grid md:grid-cols-3 gap-6 mb-8">
                        <!-- Tenant Card -->
                        <div class="border-2 border-blue-200 rounded-xl p-6 text-center hover:border-blue-400 hover:shadow-md transition-all cursor-pointer"
                             onclick="selectAccountType('Tenant')">
                            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-user text-blue-600 text-2xl"></i>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-800 mb-2">Tenant</h3>
                            <p class="text-gray-600 mb-4">Looking for accommodations to rent</p>
                            <ul class="text-left text-sm text-gray-600 space-y-1">
                                <li><i class="fas fa-check text-green-500 mr-2"></i>Browse accommodations</li>
                                <li><i class="fas fa-check text-green-500 mr-2"></i>Make reservations</li>
                                <li><i class="fas fa-check text-green-500 mr-2"></i>Manage bookings</li>
                            </ul>
                        </div>
                        
                        <!-- Owner Card -->
                        <div class="border-2 border-green-200 rounded-xl p-6 text-center hover:border-green-400 hover:shadow-md transition-all cursor-pointer"
                             onclick="selectAccountType('Owner')">
                            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-home text-green-600 text-2xl"></i>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-800 mb-2">Property Owner</h3>
                            <p class="text-gray-600 mb-4">Want to list your accommodations</p>
                            <ul class="text-left text-sm text-gray-600 space-y-1">
                                <li><i class="fas fa-check text-green-500 mr-2"></i>List properties</li>
                                <li><i class="fas fa-check text-green-500 mr-2"></i>Manage reservations</li>
                                <li><i class="fas fa-check text-green-500 mr-2"></i>Receive payments</li>
                                <li class="text-orange-600"><i class="fas fa-exclamation-circle mr-2"></i>Requires DTI Permit</li>
                            </ul>
                        </div>
                        
                        <!-- Admin Card (if applicable) -->
                        <div class="border-2 border-purple-200 rounded-xl p-6 text-center hover:border-purple-400 hover:shadow-md transition-all cursor-pointer"
                             onclick="selectAccountType('Admin')">
                            <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-user-shield text-purple-600 text-2xl"></i>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-800 mb-2">Administrator</h3>
                            <p class="text-gray-600 mb-4">System administration access</p>
                            <ul class="text-left text-sm text-gray-600 space-y-1">
                                <li><i class="fas fa-check text-green-500 mr-2"></i>Manage users</li>
                                <li><i class="fas fa-check text-green-500 mr-2"></i>View reports</li>
                                <li><i class="fas fa-check text-green-500 mr-2"></i>System configuration</li>
                                <li class="text-red-600"><i class="fas fa-lock mr-2"></i>By invitation only</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="flex justify-end">
                        <button onclick="nextStep()" 
                                class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                            Continue <i class="fas fa-arrow-right ml-2"></i>
                        </button>
                    </div>
                </div>

                <!-- Personal Information Form -->
                <div id="step2" class="tab-content">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">Personal Information</h2>
                    
                    <form id="personalInfoForm" class="space-y-6">
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">First Name *</label>
                                <input type="text" name="firstname" id="firstname" required 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="Enter your first name">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Middle Name</label>
                                <input type="text" name="middlename" id="middlename"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="Enter your middle name">
                            </div>
                        </div>
                        
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Last Name *</label>
                                <input type="text" name="lastname" id="lastname" required 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="Enter your last name">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Extension Name</label>
                                <select name="extension_name" id="extension_name"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">None</option>
                                    <option value="Jr.">Jr.</option>
                                    <option value="Sr.">Sr.</option>
                                    <option value="II">II</option>
                                    <option value="III">III</option>
                                    <option value="IV">IV</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email Address *</label>
                                <input type="email" name="email" id="email" required 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="your.email@example.com">
                                <p class="text-sm text-gray-500 mt-1">We'll never share your email</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Mobile Number *</label>
                                <input type="tel" name="mobile_number" id="mobile_number" required 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="0912 345 6789">
                            </div>
                        </div>
                        
                        <!-- DTI Permit Section (for Owners) -->
                        <div id="dtiSection" class="hidden">
                            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-yellow-700">
                                            As a property owner, you need to upload a valid DTI permit for verification.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">DTI Permit *</label>
                                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg">
                                    <div class="space-y-1 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <div class="flex text-sm text-gray-600">
                                            <label for="dti_permit" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                                <span>Upload a file</span>
                                                <input id="dti_permit" name="dti_permit" type="file" class="sr-only">
                                            </label>
                                            <p class="pl-1">or drag and drop</p>
                                        </div>
                                        <p class="text-xs text-gray-500">
                                            PNG, JPG, PDF up to 10MB
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex justify-between">
                            <button type="button" onclick="prevStep()" 
                                    class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium">
                                <i class="fas fa-arrow-left mr-2"></i>Back
                            </button>
                            <button type="button" onclick="validatePersonalInfo()" 
                                    class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                                Continue <i class="fas fa-arrow-right ml-2"></i>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Address Information Form -->
                <div id="step3" class="tab-content">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">Address Information</h2>
                    
                    <form id="addressForm" class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">House/Building Name</label>
                            <input type="text" name="house_name" id="house_name"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="e.g., Villa Esperanza">
                        </div>
                        
                        <div class="grid md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Province *</label>
                                <select name="province" id="province" required 
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        onchange="loadMunicipalities(this.value)">
                                    <option value="">Select Province</option>
                                    <option value="Sorsogon" selected>Sorsogon</option>
                                    <option value="Albay">Albay</option>
                                    <option value="Camarines Sur">Camarines Sur</option>
                                    <option value="Camarines Norte">Camarines Norte</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Municipality/City *</label>
                                <select name="municipality" id="municipality" required 
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        onchange="loadBarangays(this.value)">
                                    <option value="">Select Municipality</option>
                                    <option value="Bulan" selected>Bulan</option>
                                    <option value="Sorsogon City">Sorsogon City</option>
                                    <option value="Irosin">Irosin</option>
                                    <option value="Gubat">Gubat</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Barangay *</label>
                                <select name="barangay" id="barangay" required 
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select Barangay</option>
                                    <option value="Zone 1">Zone 1</option>
                                    <option value="Zone 2">Zone 2</option>
                                    <option value="Zone 3">Zone 3</option>
                                    <option value="Zone 4">Zone 4</option>
                                    <option value="Zone 5">Zone 5</option>
                                    <option value="Zone 6">Zone 6</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Zip Code *</label>
                                <input type="text" name="zipcode" id="zipcode" required 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="4706">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Profile Photo</label>
                                <div class="flex items-center space-x-4">
                                    <div class="w-16 h-16 rounded-full bg-gray-200 overflow-hidden border-2 border-gray-300">
                                        <img id="profilePreview" src="" alt="" class="w-full h-full object-cover hidden">
                                    </div>
                                    <div>
                                        <input type="file" name="profile" id="profile" accept="image/*" 
                                               class="hidden" onchange="previewProfileImage(event)">
                                        <button type="button" onclick="document.getElementById('profile').click()" 
                                                class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium">
                                            <i class="fas fa-camera mr-2"></i>Upload Photo
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Location Map (Optional) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Location (Optional)</label>
                            <textarea name="location" id="location" rows="3" 
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                      placeholder="Additional location details or landmarks"></textarea>
                        </div>
                        
                        <div class="flex justify-between">
                            <button type="button" onclick="prevStep()" 
                                    class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium">
                                <i class="fas fa-arrow-left mr-2"></i>Back
                            </button>
                            <button type="button" onclick="validateAddressInfo()" 
                                    class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                                Continue <i class="fas fa-arrow-right ml-2"></i>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Credentials Form -->
                <div id="step4" class="tab-content">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">Account Credentials</h2>
                    
                    <form id="credentialsForm" class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Username *</label>
                            <input type="text" name="username" id="username" required 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Choose a username">
                            <p class="text-sm text-gray-500 mt-1">This will be your unique identifier</p>
                        </div>
                        
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Password *</label>
                                <div class="relative">
                                    <input type="password" name="password" id="password" required 
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 pr-10"
                                           placeholder="Enter password">
                                    <button type="button" onclick="togglePassword('password')" 
                                            class="absolute right-3 top-3 text-gray-500 hover:text-gray-700">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <div class="mt-2 space-y-1">
                                    <div class="flex items-center">
                                        <div class="w-2 h-2 rounded-full bg-gray-300 mr-2"></div>
                                        <span class="text-xs text-gray-600">At least 8 characters</span>
                                    </div>
                                    <div class="flex items-center">
                                        <div class="w-2 h-2 rounded-full bg-gray-300 mr-2"></div>
                                        <span class="text-xs text-gray-600">One uppercase letter</span>
                                    </div>
                                    <div class="flex items-center">
                                        <div class="w-2 h-2 rounded-full bg-gray-300 mr-2"></div>
                                        <span class="text-xs text-gray-600">One number</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Confirm Password *</label>
                                <div class="relative">
                                    <input type="password" name="password_confirmation" id="password_confirmation" required 
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 pr-10"
                                           placeholder="Confirm password">
                                    <button type="button" onclick="togglePassword('password_confirmation')" 
                                            class="absolute right-3 top-3 text-gray-500 hover:text-gray-700">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Terms and Conditions -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="flex items-start">
                                <input type="checkbox" name="terms_agreed" id="terms_agreed" required 
                                       class="mt-1 mr-3 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <label for="terms_agreed" class="text-sm text-gray-700">
                                    I agree to the <a href="#" class="text-blue-600 hover:underline">Terms of Service</a> and <a href="#" class="text-blue-600 hover:underline">Privacy Policy</a>. 
                                    I understand that my information will be used in accordance with the system's policies and for verification purposes.
                                </label>
                            </div>
                        </div>
                        
                        <div class="flex justify-between">
                            <button type="button" onclick="prevStep()" 
                                    class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium">
                                <i class="fas fa-arrow-left mr-2"></i>Back
                            </button>
                            <button type="button" onclick="submitRegistration()" 
                                    class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium">
                                <i class="fas fa-user-plus mr-2"></i>Create Account
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Login Link -->
        <div class="text-center mt-6">
            <p class="text-gray-600">
                Already have an account? 
                <a href="/login-page" class="text-blue-600 font-medium hover:underline">Sign in here</a>
            </p>
        </div>

        <!-- Response Message -->
        <div id="responseMessage" class="fixed bottom-4 right-4 max-w-sm hidden">
            <div class="bg-white rounded-lg shadow-lg border p-4">
                <div class="flex items-start">
                    <div id="responseIcon" class="flex-shrink-0 mr-3"></div>
                    <div>
                        <h4 id="responseTitle" class="font-semibold"></h4>
                        <p id="responseText" class="text-sm mt-1"></p>
                    </div>
                    <button onclick="hideResponse()" class="ml-4 text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden user_type field for form submission -->
    <input type="hidden" id="user_type" value="">

    <script>
        // Registration Data Object
        let registrationData = {
            user_type: '',
            firstname: '',
            middlename: '',
            lastname: '',
            extension_name: '',
            province: 'Sorsogon',
            municipality: 'Bulan',
            barangay: '',
            zipcode: '',
            email: '',
            mobile_number: '',
            dti_permit: '',
            location: '',
            profile: '',
            house_name: '',
            username: '',
            password: '',
            status: 'Pending',
            view: 'No'
        };

        let currentStep = 1;

        // Navigation Functions
        function selectAccountType(type) {
            registrationData.user_type = type;
            document.getElementById('user_type').value = type;
            
            // Highlight selected card
            document.querySelectorAll('[onclick^="selectAccountType"]').forEach(card => {
                card.classList.remove('border-blue-400', 'border-green-400', 'border-purple-400', 'shadow-md');
                card.classList.add('border-blue-200', 'border-green-200', 'border-purple-200');
            });
            
            // Highlight selected
            const colorMap = {
                'Tenant': 'blue',
                'Owner': 'green',
                'Admin': 'purple'
            };
            
            event.currentTarget.classList.remove(`border-${colorMap[type]}-200`);
            event.currentTarget.classList.add(`border-${colorMap[type]}-400`, 'shadow-md');
            
            // Show DTI section for owners
            const dtiSection = document.getElementById('dtiSection');
            if (type === 'Owner') {
                dtiSection.classList.remove('hidden');
            } else {
                dtiSection.classList.add('hidden');
            }
        }

        function nextStep() {
            if (currentStep === 1 && !registrationData.user_type) {
                showResponse('Please select an account type', 'error');
                return;
            }
            
            changeStep(currentStep + 1);
        }

        function prevStep() {
            changeStep(currentStep - 1);
        }

        function changeStep(step) {
            if (step > currentStep) {
                if (!validateCurrentStep()) {
                    return;
                }
            }
            
            // Hide current step
            document.getElementById(`step${currentStep}`).classList.remove('active');
            
            // Update progress indicators
            updateProgressIndicators(currentStep, step);
            
            // Show new step
            document.getElementById(`step${step}`).classList.add('active');
            currentStep = step;
            
            // Scroll to top of form
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function updateProgressIndicators(oldStep, newStep) {
            const indicators = document.querySelectorAll('.step-indicator');
            
            // Update numbers and colors
            indicators.forEach((indicator, index) => {
                const numberDiv = indicator.querySelector('div');
                const textSpan = indicator.querySelector('span');
                
                if (index < newStep - 1) {
                    // Completed steps
                    numberDiv.className = 'w-8 h-8 rounded-full bg-green-500 text-white flex items-center justify-center font-semibold mb-1';
                    textSpan.className = 'text-sm font-medium text-gray-700';
                } else if (index === newStep - 1) {
                    // Current step
                    numberDiv.className = 'w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center font-semibold mb-1';
                    textSpan.className = 'text-sm font-medium text-gray-700';
                } else {
                    // Future steps
                    numberDiv.className = 'w-8 h-8 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center font-semibold mb-1';
                    textSpan.className = 'text-sm font-medium text-gray-500';
                }
            });
        }

        function validateCurrentStep() {
            switch(currentStep) {
                case 1:
                    return !!registrationData.user_type;
                case 2:
                    return validateStep2();
                case 3:
                    return validateStep3();
                default:
                    return true;
            }
        }

        // Step 2 validation (Personal Info)
        function validateStep2() {
            const required = ['firstname', 'lastname', 'email', 'mobile_number'];
            
            for (let field of required) {
                const input = document.getElementById(field);
                if (!input || !input.value.trim()) {
                    showResponse(`Please fill in ${field.replace('_', ' ')}`, 'error');
                    input?.focus();
                    return false;
                }
            }
            
            // Validate email
            const email = document.getElementById('email').value;
            if (!validateEmail(email)) {
                showResponse('Please enter a valid email address', 'error');
                return false;
            }
            
            // Validate mobile number format
            const mobileNumber = document.getElementById('mobile_number').value;
            if (!validateMobileNumber(mobileNumber)) {
                showResponse('Please enter a valid mobile number (e.g., 09123456789)', 'error');
                return false;
            }
            
            // Save data
            saveStepData(2);
            return true;
        }

        // Step 3 validation (Address Info)
        function validateStep3() {
            const required = ['province', 'municipality', 'barangay', 'zipcode'];
            
            for (let field of required) {
                const input = document.getElementById(field);
                if (!input || !input.value.trim()) {
                    showResponse(`Please select ${field.replace('_', ' ')}`, 'error');
                    input?.focus();
                    return false;
                }
            }
            
            // Validate zip code format (Philippines zip codes are 4 digits)
            const zipcode = document.getElementById('zipcode').value;
            if (!/^\d{4}$/.test(zipcode)) {
                showResponse('Please enter a valid 4-digit zip code', 'error');
                return false;
            }
            
            // Save data
            saveStepData(3);
            return true;
        }

        // Separate function for button click validation
        function validatePersonalInfo() {
            if (validateStep2()) {
                changeStep(3);
            }
        }

        function validateAddressInfo() {
            if (validateStep3()) {
                changeStep(4);
            }
        }

        function saveStepData(step) {
            // Save data to registrationData object
            const fields = {
                2: ['firstname', 'middlename', 'lastname', 'extension_name', 'email', 'mobile_number'],
                3: ['province', 'municipality', 'barangay', 'zipcode', 'house_name', 'location'],
                4: ['username', 'password', 'password_confirmation']
            };

            if (fields[step]) {
                fields[step].forEach(field => {
                    const element = document.getElementById(field);
                    if (element) {
                        registrationData[field] = element.value;
                    }
                });
            }
        }

        async function submitRegistration() {
            // Validate step 4 fields
            const requiredFields = ['username', 'password', 'password_confirmation'];
            for (let field of requiredFields) {
                const input = document.getElementById(field);
                if (!input || !input.value.trim()) {
                    showResponse(`Please fill in ${field.replace('_', ' ')}`, 'error');
                    input?.focus();
                    return;
                }
            }

            // Check if passwords match
            const password = document.getElementById('password').value;
            const passwordConfirmation = document.getElementById('password_confirmation').value;
            if (password !== passwordConfirmation) {
                showResponse('Passwords do not match', 'error');
                return;
            }

            // Check terms agreement
            const termsAgreed = document.getElementById('terms_agreed');
            if (!termsAgreed || !termsAgreed.checked) {
                showResponse('You must agree to the terms and conditions', 'error');
                return;
            }

            // Collect all form data
            const formData = new FormData();

            // User type
            formData.append('user_type', registrationData.user_type || document.getElementById('user_type').value);

            // Personal info
            formData.append('firstname', document.getElementById('firstname').value);
            formData.append('middlename', document.getElementById('middlename').value);
            formData.append('lastname', document.getElementById('lastname').value);
            formData.append('extension_name', document.getElementById('extension_name').value);
            formData.append('email', document.getElementById('email').value);
            formData.append('mobile_number', document.getElementById('mobile_number').value);

            // Address info
            formData.append('province', document.getElementById('province').value);
            formData.append('municipality', document.getElementById('municipality').value);
            formData.append('barangay', document.getElementById('barangay').value);
            formData.append('zipcode', document.getElementById('zipcode').value);
            formData.append('house_name', document.getElementById('house_name').value);
            formData.append('location', document.getElementById('location').value);

            // Credentials
            formData.append('username', document.getElementById('username').value);
            formData.append('password', document.getElementById('password').value);
            formData.append('password_confirmation', document.getElementById('password_confirmation').value);
            formData.append('terms_agreed', termsAgreed.checked ? '1' : '');

            // DTI permit for owners
            if (registrationData.user_type === 'Owner') {
                const dtiPermitInput = document.getElementById('dti_permit');
                if (dtiPermitInput && dtiPermitInput.files.length > 0) {
                    formData.append('dti_permit', dtiPermitInput.files[0]);
                }
            }

            // Profile photo
            const profileInput = document.getElementById('profile');
            if (profileInput && profileInput.files.length > 0) {
                formData.append('profile', profileInput.files[0]);
            }

            // Show loading
            showResponse('Creating your account...', 'loading');

            // Send to backend
            try {
                let response = await fetch("/api/register", {
                    method: "POST",
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });

                let result = await response.json();

                if (response.ok) {
                    showResponse('Account created successfully! Redirecting to login...', 'success');
                    // Redirect to login after 2 seconds
                    setTimeout(() => {
                        window.location.href = '/login';
                    }, 2000);
                } else {
                    // Show validation errors
                    if (result.errors) {
                        const errorMessages = Object.values(result.errors).flat().join(', ');
                        showResponse(`Registration failed: ${errorMessages}`, 'error');
                    } else {
                        showResponse(`Registration failed: ${result.message || 'Unknown error'}`, 'error');
                    }
                }
            } catch (error) {
                console.error('Registration error:', error);
                showResponse('Something went wrong. Please try again.', 'error');
            }
        }

        function validateEmail(email) {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(email);
        }

        function validateMobileNumber(mobile) {
            // Philippine mobile number format: 09XXXXXXXXX or +639XXXXXXXXX
            const re = /^(09|\+639)\d{9}$/;
            // Remove spaces and dashes for validation
            const cleanMobile = mobile.replace(/[\s\-]/g, '');
            return re.test(cleanMobile);
        }

        function previewProfileImage(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('profilePreview');
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                }
                reader.readAsDataURL(file);
            }
        }

        function togglePassword(fieldName) {
            const input = document.getElementById(fieldName);
            if (!input) return;
            
            const button = input.nextElementSibling;
            const icon = button.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'fas fa-eye-slash';
            } else {
                input.type = 'password';
                icon.className = 'fas fa-eye';
            }
        }

        function loadMunicipalities(province) {
            console.log('Loading municipalities for:', province);
        }

        function loadBarangays(municipality) {
            console.log('Loading barangays for:', municipality);
        }

        function showResponse(message, type = 'info') {
            const responseDiv = document.getElementById('responseMessage');
            const iconDiv = document.getElementById('responseIcon');
            const title = document.getElementById('responseTitle');
            const text = document.getElementById('responseText');
            
            const config = {
                success: { 
                    icon: '<i class="fas fa-check-circle text-green-500 text-xl"></i>',
                    title: 'Success!',
                    color: 'text-green-700'
                },
                error: { 
                    icon: '<i class="fas fa-exclamation-circle text-red-500 text-xl"></i>',
                    title: 'Error!',
                    color: 'text-red-700'
                },
                info: { 
                    icon: '<i class="fas fa-info-circle text-blue-500 text-xl"></i>',
                    title: 'Info',
                    color: 'text-blue-700'
                },
                loading: { 
                    icon: '<i class="fas fa-spinner fa-spin text-blue-500 text-xl"></i>',
                    title: 'Processing...',
                    color: 'text-blue-700'
                }
            };
            
            const cfg = config[type] || config.info;
            
            iconDiv.innerHTML = cfg.icon;
            title.textContent = cfg.title;
            title.className = cfg.color;
            text.textContent = message;
            
            responseDiv.classList.remove('hidden');
            
            // Auto-hide for non-error messages
            if (type !== 'error' && type !== 'loading') {
                setTimeout(() => {
                    hideResponse();
                }, 5000);
            }
        }

        function hideResponse() {
            document.getElementById('responseMessage').classList.add('hidden');
        }

        function fillSampleData() {
            const sampleData = {
                user_type: 'Tenant',
                firstname: 'Juan',
                middlename: 'Santos',
                lastname: 'Dela Cruz',
                extension_name: 'Jr.',
                province: 'Sorsogon',
                municipality: 'Bulan',
                barangay: 'Zone 1',
                zipcode: '4706',
                email: 'juan.delacruz@example.com',
                mobile_number: '09123456789',
                house_name: 'Villa Esperanza',
                username: 'juan.delacruz',
                password: 'Password123',
                password_confirmation: 'Password123'
            };
            
            // Fill forms with sample data
            selectAccountType(sampleData.user_type);
            
            setTimeout(() => {
                Object.keys(sampleData).forEach(key => {
                    const input = document.getElementById(key);
                    if (input) {
                        input.value = sampleData[key];
                    }
                });
                
                // Check terms
                const terms = document.getElementById('terms_agreed');
                if (terms) {
                    terms.checked = true;
                }
                
                showResponse('Sample data loaded. You can now test the form.', 'info');
            }, 100);
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Add dev button for testing
            const devButton = document.createElement('button');
            devButton.className = 'fixed bottom-4 left-4 bg-gray-800 text-white px-3 py-2 rounded-lg text-sm opacity-50 hover:opacity-100';
            devButton.innerHTML = '<i class="fas fa-vial mr-1"></i> Load Sample Data';
            devButton.onclick = fillSampleData;
            document.body.appendChild(devButton);
            
            // Set default values
            const province = document.getElementById('province');
            const municipality = document.getElementById('municipality');
            if (province) province.value = 'Sorsogon';
            if (municipality) municipality.value = 'Bulan';
            
            // Focus on first name field
            const firstname = document.getElementById('firstname');
            if (firstname) firstname.focus();
        });
    </script>
</body>
</html>