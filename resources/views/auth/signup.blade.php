@extends(config('PGP.layout_file'))

@section(config('PGP.layout_section'))
    <div class="container mx-auto px-4">
        <div class="flex flex-col items-center justify-center mt-20">
            <h1 class="text-3xl font-bold text-gray-800 mb-4">Sign Up</h1>
            @if ($errors->any())
                <div class="bg-red-500 text-white p-4 rounded mb-4">
                    <ul class="list-reset">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form method="POST" action="{{ route(config('PGP.prefix') . '.signup.store') }}" class="w-full max-w-md">
                @csrf
                <div class="flex flex-col mb-4">
                    <label for="name" class="text-gray-800 font-bold mb-2">Name</label>
                    <input id="name" type="text"
                           class="border-2 border-gray-400 rounded-lg py-2 px-4 w-full @error('name') border-red-500 @enderror"
                           name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
                    @error('name')
                    <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex flex-col mb-4">
                    <label for="username" class="text-gray-800 font-bold mb-2">Username</label>
                    <input id="username" type="text"
                           class="border-2 border-gray-400 rounded-lg py-2 px-4 w-full @error('username') border-red-500 @enderror"
                           name="username" value="{{ old('username') }}" required autocomplete="username" autofocus>
                    @error('username')
                    <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex flex-col mb-4">
                    <label for="email" class="text-gray-800 font-bold mb-2">Email</label>
                    <input id="email" type="email"
                           class="border-2 border-gray-400 rounded-lg py-2 px-4 w-full @error('email') border-red-500 @enderror"
                           name="email" value="{{ old('email') }}" required autocomplete="email">
                    @error('email')
                    <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex flex-col mb-4">
                    <label for="password" class="text-gray-800 font-bold mb-2">Password</label>
                    <input id="password" type="password"
                           class="border-2 border-gray-400 rounded-lg py-2 px-4 w-full @error('password') border-red-500 @enderror"
                           name="password" required autocomplete="new-password">
                    @error('password')
                    <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex flex-col mb-4">
                    <label for="password-confirm" class="text-gray-800 font-bold mb-2">Confirm Password</label>
                    <input id="password-confirm" type="password"
                           class="border-2 border-gray-400 rounded-lg py-2 px-4 w-full" name="password_confirmation"
                           required autocomplete="new-password">
                </div>
                <div class="flex items-center justify-between">
                    <button type="submit"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        {{ __('Sign Up') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
