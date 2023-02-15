@extends(config('PGP.layout_file'))

@section(config('PGP.layout_section'))
    <div class="container mx-auto px-4 py-8">
        <div class="w-full max-w-xs mx-auto">
            <form class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4" method="POST"
                  action="{{ route(config('PGP.prefix') . '.authenticate') }}">
                @csrf
                <div class="mb-4">
                    <label for="login" class="text-gray-800 font-bold mb-2">{{ __('Username or Email') }}</label>
                    <input type="text" name="login"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                           required>
                </div>
                @if ($errors->has('username') || $errors->has('email'))
                    <span class="invalid-feedback">
                        <strong>{{ $errors->first('username') ?: $errors->first('email') }}</strong>
                    </span>
                @endif
                <div class="mb-6">
                    <label for="password" class="text-gray-800 font-bold mb-2">Password</label>
                    <input type="password" name="password"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                           required>
                </div>
                <div class="form-check mb-4">
                    <input type="checkbox" name="remember" class="form-check-input" id="remember">
                    <label for="remember" class="form-check-label text-gray-800 font-bold mb-2">Remember Me</label>
                </div>
                @if (session('warning'))
                    <div class="bg-yellow-500 text-white p-2 rounded mb-6">
                        {{ session('warning') }}
                    </div>
                @endif
                @if ($errors->any())
                    <div class="bg-red-500 rounded text-white shadow-md text-sm py-2 px-8 mb-6">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="form-group row mb-2 flex justify-between items-center">
                    <div class="col-md-8 offset-md-4">
                        <button type="submit"
                                class="btn bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            {{ __('Login') }}
                        </button>
                    </div>
                    <div class="col-md-8 offset-md-4 px-2">
                        <p>Don't have an account? <a href="{{config('PGP.prefix')}}/signup"
                                                     class="text-blue-500 hover:text-blue-700 font-bold">Sign up now</a>
                        </p>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
