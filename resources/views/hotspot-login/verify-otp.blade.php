<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Login OTP</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <!-- Main Content -->
        <div class="max-w-md mx-auto">
            <div class="bg-white rounded-lg shadow-xl overflow-hidden">
                <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-8 text-white text-center">
                    <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h1 class="text-2xl font-bold mb-2">Verify Your Number</h1>
                    <p class="text-blue-100">We sent a code to {{ $mobile_number }}</p>
                </div>

                <div class="p-6 md:p-8">
                    @if (session('success'))
                        <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-green-700">{{ session('success') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    @foreach ($errors->all() as $error)
                                        <p class="text-sm text-red-700">{{ $error }}</p>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('hotspot.login.verify-otp.post') }}" method="POST" id="otp-form">
                        @csrf
                        <input type="hidden" name="mobile_number" value="{{ $mobile_number }}">

                        <!-- OTP Input -->
                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Enter 6-Digit Code
                            </label>
                            <input
                                type="text"
                                name="otp_code"
                                id="otp-input"
                                maxlength="6"
                                pattern="[0-9]{6}"
                                class="w-full text-center text-3xl tracking-widest px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="000000"
                                required
                                autofocus
                            >
                        </div>

                        <!-- Timer -->
                        <div class="mb-6 text-center">
                            <p class="text-sm text-gray-600">
                                Code expires in: <span id="timer" class="font-bold text-blue-600">5:00</span>
                            </p>
                        </div>

                        <!-- Submit Button -->
                        <button
                            type="submit"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-lg transition duration-150 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                        >
                            Verify & Login
                        </button>

                        <!-- Resend OTP -->
                        <div class="mt-6 text-center">
                            <p class="text-sm text-gray-600">
                                Didn't receive the code?
                                <button
                                    type="button"
                                    id="resend-btn"
                                    class="text-blue-600 hover:text-blue-700 font-semibold disabled:text-gray-400 disabled:cursor-not-allowed"
                                    disabled
                                >
                                    Resend OTP (<span id="resend-timer">60</span>s)
                                </button>
                            </p>
                        </div>
                    </form>

                    <!-- Back Link -->
                    <div class="mt-6 text-center">
                        <a href="{{ route('hotspot.login') }}" class="text-sm text-gray-600 hover:text-gray-800">
                            ← Back to login
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // OTP Countdown Timer
        let expiresAt = {{ $expires_at }};
        let now = Math.floor(Date.now() / 1000);
        let timeLeft = expiresAt - now;

        const timerElement = document.getElementById('timer');
        
        function updateTimer() {
            if (timeLeft <= 0) {
                timerElement.textContent = 'EXPIRED';
                timerElement.classList.add('text-red-600');
                return;
            }

            let minutes = Math.floor(timeLeft / 60);
            let seconds = timeLeft % 60;
            timerElement.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
            timeLeft--;
            
            setTimeout(updateTimer, 1000);
        }

        updateTimer();

        // Resend OTP Cooldown
        let resendCooldown = 60;
        const resendBtn = document.getElementById('resend-btn');
        const resendTimer = document.getElementById('resend-timer');

        function updateResendTimer() {
            if (resendCooldown <= 0) {
                resendBtn.disabled = false;
                resendTimer.textContent = '';
                resendBtn.innerHTML = 'Resend OTP';
                return;
            }

            resendTimer.textContent = resendCooldown;
            resendCooldown--;
            setTimeout(updateResendTimer, 1000);
        }

        updateResendTimer();

        // Resend OTP Handler
        resendBtn.addEventListener('click', function() {
            // This would need to be implemented with AJAX
            alert('Resend OTP functionality coming soon!');
        });

        // Auto-submit on 6 digits
        document.getElementById('otp-input').addEventListener('input', function(e) {
            if (e.target.value.length === 6) {
                // Optional: Auto-submit form
                // document.getElementById('otp-form').submit();
            }
        });
    </script>
</body>
</html>
