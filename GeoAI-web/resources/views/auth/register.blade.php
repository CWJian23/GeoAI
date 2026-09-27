<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>GeoAI | Register</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-slate-50 text-slate-900">
        <div class="min-h-screen bg-[radial-gradient(circle_at_top_left,_rgba(34,211,238,0.18),_transparent_32%),radial-gradient(circle_at_top_right,_rgba(16,185,129,0.16),_transparent_28%)]">
            <div class="mx-auto flex min-h-screen max-w-5xl items-center justify-center px-4 py-10">
                <div class="w-full max-w-xl rounded-[32px] border border-slate-200 bg-white/90 p-8 shadow-[0_24px_100px_rgba(15,23,42,0.08)] backdrop-blur sm:p-10">
                    <div class="text-center">
                        <div class="inline-flex items-center rounded-full border border-cyan-200 bg-cyan-50 px-3 py-1 text-sm font-medium text-cyan-700">
                            Create your account
                        </div>
                        <h1 class="mt-5 text-3xl font-semibold text-slate-950 sm:text-4xl">Sign up to continue</h1>
                        <p class="mt-3 text-base leading-7 text-slate-600">
                            Register with your email or phone number, then you can log in directly next time.
                        </p>
                    </div>

                    @if ($errors->any())
                        <div class="mt-6 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('register.submit') }}" class="mt-8 space-y-5">
                        @csrf

                        <div>
                            <label for="name" class="mb-2 block text-sm font-semibold text-slate-700">Name</label>
                            <input id="name" type="text" name="name" value="{{ old('name') }}" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-cyan-500" placeholder="Enter your name" required>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700">Register with</label>
                            <div class="grid gap-3 sm:grid-cols-2">
                                <label class="flex cursor-pointer items-center justify-center gap-2 rounded-full border border-slate-200 bg-slate-50 px-3 py-3 text-sm font-medium text-slate-700">
                                    <input type="radio" name="register_method" value="email" @checked(old('register_method', 'email') === 'email') class="h-4 w-4 text-cyan-500">
                                    Email
                                </label>
                                <label class="flex cursor-pointer items-center justify-center gap-2 rounded-full border border-slate-200 bg-slate-50 px-3 py-3 text-sm font-medium text-slate-700">
                                    <input type="radio" name="register_method" value="phone" @checked(old('register_method') === 'phone') class="h-4 w-4 text-cyan-500">
                                    Phone
                                </label>
                            </div>
                        </div>

                        <div id="email-register-field">
                            <label for="email" class="mb-2 block text-sm font-semibold text-slate-700">Email address</label>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-cyan-500" placeholder="you@example.com">
                        </div>

                        <div id="phone-register-field" class="hidden">
                            <label for="phone" class="mb-2 block text-sm font-semibold text-slate-700">Phone number</label>
                            <input id="phone" type="tel" name="phone" value="{{ old('phone') }}" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-cyan-500" placeholder="+60123456789">
                        </div>

                        <div>
                            <label for="password" class="mb-2 block text-sm font-semibold text-slate-700">Password</label>
                            <input id="password" type="password" name="password" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-cyan-500" placeholder="Enter your password" required>
                        </div>

                        <div>
                            <label for="password_confirmation" class="mb-2 block text-sm font-semibold text-slate-700">Confirm password</label>
                            <input id="password_confirmation" type="password" name="password_confirmation" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-cyan-500" placeholder="Confirm your password" required>
                        </div>

                        <button type="submit" class="mt-3 w-full rounded-full bg-cyan-500 px-6 py-3 font-semibold text-white shadow-lg shadow-cyan-500/25 transition hover:bg-cyan-400">
                            Create account &amp; continue
                        </button>
                    </form>

                    <div class="mt-6 text-center text-sm text-slate-500">
                        Already have an account? <a href="{{ route('login') }}" class="font-semibold text-cyan-700 hover:text-cyan-600">Log in</a>
                    </div>
                    <div class="mt-3 text-center text-sm text-slate-500">
                        Need to go back? <a href="{{ route('home') }}" class="font-semibold text-cyan-700 hover:text-cyan-600">Return home</a>
                    </div>
                </div>
            </div>
        </div>

        <script>
            const methodInputs = document.querySelectorAll('input[name="register_method"]');
            const emailField = document.getElementById('email-register-field');
            const phoneField = document.getElementById('phone-register-field');

            const updateRegisterFields = (method) => {
                const isEmail = method === 'email';
                emailField.classList.toggle('hidden', !isEmail);
                phoneField.classList.toggle('hidden', isEmail);
            };

            methodInputs.forEach((input) => {
                input.addEventListener('change', () => updateRegisterFields(input.value));
            });

            updateRegisterFields(document.querySelector('input[name="register_method"]:checked').value);
        </script>
    </body>
</html>
