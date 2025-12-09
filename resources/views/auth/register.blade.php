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
                                <input type="text" name="firstname" required 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="Enter your first name">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Middle Name</label>
                                <input type="text" name="middlename"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="Enter your middle name">
                            </div>
                        </div>
                        
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Last Name *</label>
                                <input type="text" name="lastname" required 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="Enter your last name">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Extension Name</label>
                                <select name="extension_name" 
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
                                <input type="email" name="email" required 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="your.email@example.com">
                                <p class="text-sm text-gray-500 mt-1">We'll never share your email</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Mobile Number *</label>
                                <input type="tel" name="mobile_number" required 
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
                            <input type="text" name="house_name"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="e.g., Villa Esperanza">
                        </div>
                        
                        <div class="grid md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Province *</label>
                                <select name="province" required 
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
                                <select name="municipality" required 
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        onchange="loadBarangays(this.value)">
                                    <option value="">Select Municipality</option>
                                    <option value="Bulan" selected>Bulan</option>
                                    <option value="Sorsogon City">Sorsogon City</option>
                                    <option value="Bulan">Irosin</option>
                                    <option value="Gubat">Gubat</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Barangay *</label>
                                <select name="barangay" required 
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
                                <input type="text" name="zipcode" required 
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
                            <textarea name="location" rows="3" 
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
                            <input type="text" name="username" required 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Choose a username">
                            <p class="text-sm text-gray-500 mt-1">This will be your unique identifier</p>
                        </div>
                        
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Password *</label>
                                <div class="relative">
                                    <input type="password" name="password" required 
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
                                    <input type="password" name="password_confirmation" required 
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
                                <input type="checkbox" name="terms" id="terms" required 
                                       class="mt-1 mr-3 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <label for="terms" class="text-sm text-gray-700">
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
                <a href="/login" class="text-blue-600 font-medium hover:underline">Sign in here</a>
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

    <!-- Add this button somewhere in your register page for testing -->
<button onclick="testAPI()" 
        class="fixed bottom-4 right-4 bg-red-500 text-white px-3 py-2 rounded-lg text-sm">
    <i class="fas fa-bug mr-1"></i> Test API Connection
</button>

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
            return validateStep2(); // Changed function name
        case 3:
            return validateStep3(); // Changed function name
        default:
            return true;
    }
}

// Step 2 validation (Personal Info)
function validateStep2() {
    const form = document.getElementById('personalInfoForm');
    const required = ['firstname', 'lastname', 'email', 'mobile_number'];
    
    for (let field of required) {
        const input = form.querySelector(`[name="${field}"]`);
        if (!input.value.trim()) {
            showResponse(`Please fill in ${field.replace('_', ' ')}`, 'error');
            input.focus();
            return false;
        }
    }
    
    // Validate email
    const email = form.querySelector('[name="email"]').value;
    if (!validateEmail(email)) {
        showResponse('Please enter a valid email address', 'error');
        return false;
    }
    
    // Validate mobile number format
    const mobileNumber = form.querySelector('[name="mobile_number"]').value;
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
    const form = document.getElementById('addressForm');
    const required = ['province', 'municipality', 'barangay', 'zipcode'];
    
    for (let field of required) {
        const input = form.querySelector(`[name="${field}"]`);
        if (!input.value.trim()) {
            showResponse(`Please select ${field.replace('_', ' ')}`, 'error');
            input.focus();
            return false;
        }
    }
    
    // Validate zip code format (Philippines zip codes are 4 digits)
    const zipcode = form.querySelector('[name="zipcode"]').value;
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
    let form;
    switch(step) {
        case 2:
            form = document.getElementById('personalInfoForm');
            break;
        case 3:
            form = document.getElementById('addressForm');
            break;
        case 4:
            form = document.getElementById('credentialsForm');
            break;
    }
    
    if (form) {
        const formData = new FormData(form);
        for (let [key, value] of formData.entries()) {
            registrationData[key] = value;
        }
    }
}

async function submitRegistration() {
    const form = document.getElementById('credentialsForm');
    
    // Validate credentials
    const username = form.querySelector('[name="username"]').value;
    const password = form.querySelector('[name="password"]').value;
    const confirmPassword = form.querySelector('[name="password_confirmation"]').value;
    
    if (!username || !password) {
        showResponse('Please fill in all required fields', 'error');
        return;
    }
    
    if (password !== confirmPassword) {
        showResponse('Passwords do not match', 'error');
        return;
    }
    
    if (password.length < 8) {
        showResponse('Password must be at least 8 characters', 'error');
        return;
    }
    
    // Check for at least one uppercase letter
    if (!/[A-Z]/.test(password)) {
        showResponse('Password must contain at least one uppercase letter', 'error');
        return;
    }
    
    // Check for at least one number
    if (!/\d/.test(password)) {
        showResponse('Password must contain at least one number', 'error');
        return;
    }
    
    if (!form.querySelector('#terms').checked) {
        showResponse('Please agree to the terms and conditions', 'error');
        return;
    }
    
    // Save credentials
    saveStepData(4);
    
    // Generate user_id (format: TYPE-YYYYMMDD-XXXX)
    const date = new Date().toISOString().slice(0,10).replace(/-/g, '');
    const random = Math.floor(Math.random() * 10000).toString().padStart(4, '0');
    registrationData.user_id = `${registrationData.user_type.charAt(0)}-${date}-${random}`;
    registrationData.date_registered = new Date().toISOString().slice(0,10);
    
    // Show loading
    showResponse('Creating your account...', 'loading');
    
    try {
        // Prepare form data for file uploads
        const formData = new FormData();
        for (const key in registrationData) {
            // Skip empty values to avoid sending empty strings
            if (registrationData[key] !== '') {
                formData.append(key, registrationData[key]);
            }
        }
        
        // Add file uploads if present
        const dtiFile = document.getElementById('dti_permit');
        if (dtiFile && dtiFile.files[0]) {
            formData.append('dti_permit_file', dtiFile.files[0]);
        }
        
        const profileFile = document.getElementById('profile');
        if (profileFile && profileFile.files[0]) {
            formData.append('profile_file', profileFile.files[0]);
        }
        
        console.log('Sending registration data:', Object.fromEntries(formData.entries()));
        
        // Submit to API - Use correct endpoint
        const response = await fetch('/api/register', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                // Don't set Content-Type for FormData - let browser set it with boundary
            },
            body: formData
        });
        
        console.log('Response status:', response.status);
        console.log('Response headers:', [...response.headers.entries()]);
        
        const result = await response.json().catch(async (e) => {
            // If JSON parsing fails, try to get text response
            const text = await response.text();
            console.error('Failed to parse JSON:', text);
            throw new Error(`Server response: ${text}`);
        });
        
        console.log('Response data:', result);
        
        if (response.ok) {
            showResponse('Account created successfully! Redirecting to login...', 'success');
            
            // Auto-login or redirect
            setTimeout(() => {
                window.location.href = '/login';
            }, 2000);
        } else {
            const errorMessage = result.message || 
                               result.error || 
                               `Registration failed (Status: ${response.status})`;
            showResponse(errorMessage, 'error');
        }
    } catch (error) {
        console.error('Registration error:', error);
        showResponse(`Registration failed: ${error.message}`, 'error');
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
    const input = document.querySelector(`[name="${fieldName}"]`);
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
    if (type !== 'error') {
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
        const forms = ['personalInfoForm', 'addressForm', 'credentialsForm'];
        forms.forEach(formId => {
            const form = document.getElementById(formId);
            if (form) {
                Object.keys(sampleData).forEach(key => {
                    const input = form.querySelector(`[name="${key}"]`);
                    if (input) {
                        input.value = sampleData[key];
                    }
                });
            }
        });
        
        // Check terms
        document.getElementById('terms').checked = true;
        
        showResponse('Sample data loaded. You can now test the form.', 'info');
    }, 100);
}

document.addEventListener('DOMContentLoaded', function() {
    const devButton = document.createElement('button');
    devButton.className = 'fixed bottom-4 left-4 bg-gray-800 text-white px-3 py-2 rounded-lg text-sm opacity-50 hover:opacity-100';
    devButton.innerHTML = '<i class="fas fa-vial mr-1"></i> Load Sample Data';
    devButton.onclick = fillSampleData;
    document.body.appendChild(devButton);
    
    document.querySelector('[name="province"]').value = 'Sorsogon';
    document.querySelector('[name="municipality"]').value = 'Bulan';
    
    document.querySelector('[name="firstname"]')?.focus();
});

async function testAPI() {
    try {
        console.log('Testing API connection...');
        
        // Test 1: Check if API endpoint exists
        const testResponse = await fetch('/api/register', {
            method: 'OPTIONS', // Preflight request
        });
        console.log('OPTIONS response:', testResponse.status);
        
        // Test 2: Check with simple POST
        const testData = {
            firstname: 'Test',
            lastname: 'User',
            email: 'test@example.com',
            password: 'Password123',
            password_confirmation: 'Password123',
            user_type: 'Tenant'
        };
        
        const response = await fetch('/api/register', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(testData)
        });
        
        console.log('POST response status:', response.status);
        const result = await response.json();
        console.log('POST response:', result);
        
        showResponse(`API Test: ${response.status} - ${JSON.stringify(result)}`, 'info');
        
    } catch (error) {
        console.error('API Test Error:', error);
        showResponse(`API Test Failed: ${error.message}`, 'error');
    }
}
    </script>
</body>
</html>