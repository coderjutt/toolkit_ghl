<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>{{ env('APP_NAME','Xortlogix Toolkit') }}</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            navy: '#001b4c',
            gold: '#001b4c',
            'gold-light': '#d4b15e',
            'navy-light': '#0a2a66',
          }
        }
      }
    }
  </script>
  <link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #f0f4f9 0%, #e6edf7 100%);
    }

    .navy-bg {
      background: #d2d8e2;
    }

    .gold-bg {
      background: #001b4c;
    }

    .gold-text {
      color: #001b4c;
    }

    .navy-text {
      color: #001b4c;
    }

    .login-card {
      box-shadow: 0 10px 30px rgba(0, 27, 76, 0.15);
      border: none;
      border-radius: 16px;
      overflow: hidden;
    }

    .input-focus:focus {
      border-color: #001b4c;
     box-shadow: 0 0 0 3px rgba(0, 27, 76, 0.2);
    }

    .btn-gold {
      background: #001b4c;
      transition: all 0.3s ease;
    }

    .btn-gold:hover {
      background: #001b4c;;
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(10, 7, 1, 0.3);
    }

    .decoration {
      position: absolute;
      bottom: -30px;
      right: -30px;
      width: 200px;
      height: 200px;
      border-radius: 50%;
      background: rgba(178, 140, 53, 0.08);
    }

    .feature-icon {
      width: 50px;
      height: 50px;
      border-radius: 12px;
      background: rgba(178, 140, 53, 0.1);
      display: flex;
      align-items: center;
      justify-content: center;
      color: #e0e0e0;
      font-size: 20px;
    }

    .logo-icon {
      width: 50px;
      height: 50px;
      border-radius: 12px;
      background: #001b4c;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 24px;
    }
  </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
  <div class="w-full max-w-6xl">
    <div class="grid md:grid-cols-2 gap-8 items-center">
      <!-- Login Form -->
      <div class="login-card bg-white p-8 max-w-md w-full mx-auto">
        <form class="space-y-6" method="POST" action="{{ route('login') }}">
          @csrf
          <div class="mb-8">
            <div class="flex items-center mb-6">
              <div class="logo-icon">
                <i class="fas fa-chart-line"></i>
              </div>
              <h1 class="text-2xl font-bold navy-text ml-3">{{ env('APP_NAME','Xortlogix Toolkit') }}</h1>
            </div>
            <h3 class="text-3xl font-bold navy-text">Sign in</h3>
            <p class="text-gray-600 text-sm mt-2 leading-relaxed">
              Sign in to your account and explore a world of possibilities. Your journey begins here.
            </p>
          </div>

          <!-- Email -->
          <div>
            <label class="text-sm font-medium text-gray-700 block mb-2">Email</label>
            <div class="relative flex items-center">
              <input name="email" type="email" value="{{ old('email') }}"
                class="w-full text-sm border border-gray-300 pl-4 pr-10 py-3 rounded-lg input-focus"
                placeholder="Enter your email"/>
              <i class="fas fa-envelope text-gray-400 absolute right-4"></i>
            </div>
            @error('email')
              <span class="text-red-500 text-sm block mt-1">{{ $message }}</span>
            @enderror
          </div>

          <!-- Password -->
          <div>
            <label class="text-sm font-medium text-gray-700 block mb-2">Password</label>
            <div class="relative flex items-center">
              <input name="password" type="password" id="password"
                class="w-full text-sm border border-gray-300 pl-4 pr-10 py-3 rounded-lg input-focus"
                placeholder="Enter your password"/>
              <i class="fas fa-lock text-gray-400 absolute right-4"></i>
            </div>
            @error('password')
              <span class="text-red-500 text-sm block mt-1">{{ $message }}</span>
            @enderror
          </div>

          <!-- Remember and Forgot -->
          <div class="flex flex-wrap items-center justify-between gap-4 text-sm text-gray-600">
            <div class="flex items-center">
              <input id="remember-me" type="checkbox" name="remember"
                class="h-4 w-4 border-gray-300 rounded focus:ring-gold text-gold"
                {{ old('remember') ? 'checked' : '' }} />
              <label for="remember-me" class="ml-2">Remember Me</label>
            </div>
            <a href="#" class="text-gold hover:text-navy-light font-medium hover:underline">Forgot your password?</a>
          </div>

          <!-- Submit -->
          <div class="!mt-10">
            <button type="submit"
                class="w-full py-3.5 text-base font-semibold rounded-lg text-white btn-gold">
              {{ __('Login') }}
            </button>
          </div>
        </form>
      </div>

      <!-- Content Section -->
      <div class="max-md:mt-8">
        <div class="bg-gradient-to-br from-navy to-navy-light rounded-2xl p-8 text-white h-full relative overflow-hidden">
          <div class="relative z-10">
            <h2 class="text-3xl font-bold mb-4">Welcome to {{ env('APP_NAME','Xortlogix Toolkit') }}</h2>
            <p class="text-gray-200 mb-8 max-w-md">
              Access your personalized analytics, metrics, and business insights in one powerful platform designed for growth and efficiency.
            </p>

            <div class="space-y-6">
              <div class="flex items-start">
                <div class="feature-icon mr-4 mt-1">
                  <i class="fas fa-chart-pie"></i>
                </div>
                <div>
                  <h3 class="font-semibold text-lg mb-1">Advanced Analytics</h3>
                  <p class="text-gray-300 text-sm">Track performance with real-time data visualization</p>
                </div>
              </div>

              <div class="flex items-start">
                <div class="feature-icon mr-4 mt-1">
                  <i class="fas fa-shield-alt"></i>
                </div>
                <div>
                  <h3 class="font-semibold text-lg mb-1">Secure Platform</h3>
                  <p class="text-gray-300 text-sm">Enterprise-grade security for your sensitive data</p>
                </div>
              </div>

              <div class="flex items-start">
                <div class="feature-icon mr-4 mt-1">
                  <i class="fas fa-bolt"></i>
                </div>
                <div>
                  <h3 class="font-semibold text-lg mb-1">Fast & Responsive</h3>
                  <p class="text-gray-300 text-sm">Optimized for speed and seamless user experience</p>
                </div>
              </div>
            </div>
          </div>
          <div class="decoration"></div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>

  <script>
    // Simple animation for form elements
    document.addEventListener('DOMContentLoaded', function() {
      const inputs = document.querySelectorAll('input');
      inputs.forEach(input => {
        input.addEventListener('focus', function() {
          this.parentElement.classList.add('ring-2', 'ring-gold', 'ring-opacity-30');
        });

        input.addEventListener('blur', function() {
          this.parentElement.classList.remove('ring-2', 'ring-gold', 'ring-opacity-30');
        });
      });
    });
  </script>
</body>
</html>
