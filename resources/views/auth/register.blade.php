<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Registration Form with Tailwind CSS</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-white">
  <div class="min-h-screen flex items-center justify-center">
    <div class="py-6 px-4">
      <div class="grid md:grid-cols-2 items-center gap-6 max-w-6xl w-full">
        <!-- Registration Form -->
        <div class="border border-yellow-400 rounded-lg p-6 max-w-md shadow-xl max-md:mx-auto">
          <form class="space-y-6" method="POST" action="{{ route('register') }}">
            @csrf
            <div class="mb-8">
              <h3 class="text-yellow-700 text-3xl font-semibold">Register</h3>
              <p class="text-slate-500 text-sm mt-4 leading-relaxed">Create your account and get started on your journey with us.</p>
            </div>

            <!-- Full Name -->
            <div>
              <label class="text-slate-800 text-sm font-medium mb-2 block">Full Name</label>
              <input id="name" name="name" type="text"  placeholder="Enter your full name"
                     value="{{ old('name') }}"
                     class="form-control @error('name') is-invalid @enderror w-full text-sm text-slate-800 border border-yellow-400 pl-4 py-3 rounded-lg outline-yellow-600" />
              @error('name')
                <span class="invalid-feedback text-red-600 text-sm" role="alert">
                  <strong>{{ $message }}</strong>
                </span>
              @enderror
            </div>

            <!-- Email Address -->
            <div>
              <label class="text-slate-800 text-sm font-medium mb-2 block">Email</label>
              <input id="email" name="email" type="email"  placeholder="Enter your email"
                     value="{{ old('email') }}"
                     class="form-control @error('email') is-invalid @enderror w-full text-sm text-slate-800 border border-yellow-400 pl-4 py-3 rounded-lg outline-yellow-600" />
              @error('email')
                <span class="invalid-feedback text-red-600 text-sm" role="alert">
                  <strong>{{ $message }}</strong>
                </span>
              @enderror
            </div>

            <!-- Password -->
            <div>
              <label class="text-slate-800 text-sm font-medium mb-2 block">Password</label>
              <input id="password" name="password" type="password"  placeholder="Create a password"
                     class="form-control @error('password') is-invalid @enderror w-full text-sm text-slate-800 border border-yellow-400 pl-4 py-3 rounded-lg outline-yellow-600" />
              @error('password')
                <span class="invalid-feedback text-red-600 text-sm" role="alert">
                  <strong>{{ $message }}</strong>
                </span>
              @enderror
            </div>

            <!-- Confirm Password -->
            <div>
              <label class="text-slate-800 text-sm font-medium mb-2 block">Confirm Password</label>
              <input id="password-confirm" name="password_confirmation" type="password"  placeholder="Confirm your password"
                     class="w-full text-sm text-slate-800 border border-yellow-400 pl-4 py-3 rounded-lg outline-yellow-600" />
            </div>

            <!-- Register Button -->
            <div class="!mt-8">
              <button type="submit"
                      class="w-full shadow-xl py-2.5 px-4 text-[15px] font-medium tracking-wide rounded-lg text-white bg-yellow-500 hover:bg-yellow-600 focus:outline-none">
                Register
              </button>
              <p class="text-sm !mt-6 text-center text-slate-500">
                Already have an account?
                <a href="{{ route('login') }}" class="text-yellow-600 font-medium hover:underline ml-1 whitespace-nowrap">Sign in</a>
              </p>
            </div>
          </form>
        </div>

        <!-- Image Section -->
        <div class="max-md:mt-8">
          <img src="https://readymadeui.com/login-image.webp"
               class="w-full aspect-[71/50] max-md:w-4/5 mx-auto block object-cover"
               alt="register img"/>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
