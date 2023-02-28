@extends(config('PGP.layout_file'))

@section(config('PGP.layout_section'))
    <div class="container mx-auto">
        <div class="flex flex-col lg:flex-row">
            {{-- Left sidebar --}}
            <div class="bg-gray-200 lg:w-1/6 p-4">
                <div class="mb-4">
                    <h3 class="text-lg font-bold mb-2">Messages</h3>
                    <p class="text-sm text-gray-600">{{__('Communicate with')}} {{__('Users')}}</p>
                </div>
            </div>
            {{-- Right content --}}
            <div class="lg:ml-4 lg:w-5/6">
                {{-- List of messages --}}
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    {{-- Message list header --}}
                    <div class="px-6 py-4 bg-gray-100 border-b border-gray-200 flex justify-between">
                        <div class="flex items-center">
                            <h2 class="text-xl font-semibold text-gray-700">Messages Thread</h2>
                            <a href="{{ route(config('PGP.prefix').'.messages.index') }}"
                               class="ml-2 text-blue-500 font-medium">Back to Inbox</a>
                        </div>
                    </div>

                    <div class="px-6 py-4 border-t border-gray-200 flex justify-center">
                        <p class="font-bold text-md">Subject: </p>
                        <p class="text-gray-600">{{ $message->encrypted_subject }}</p>
                    </div>
                    <div class="px-6 py-4 bg-white">
                        <img src="{{ Gravatar::get($message->sender->email) }}" alt="{{ $message->sender->name }}"
                             class="inline-block w-8 h-8 rounded-full">
                        <p class="inline-block ml-6 text-md text-gray-600">{{ $message->encrypted_message }}</p>
                        <p class="ml-14 text-sm font-semibold text-slate-500">{{ $message->created_at->diffForHumans() }}</p>
                    </div>
                    <div class="flex flex-col-reverse">
                        @foreach($replies as $reply)
                            <div class="px-6 py-4 bg-white">
                                <img src="{{ Gravatar::get($reply->sender->email) }}" alt="{{ $reply->sender->name }}"
                                     class="inline-block w-8 h-8 rounded-full">
                                <p class="inline-block ml-6 text-md text-gray-600">{{ $reply->encrypted_message }}</p>
                                <p class="ml-14 text-sm font-semibold text-slate-500">{{ $reply->created_at->diffForHumans() }}</p>
                            </div>
                        @endforeach
                    </div>
                    <form method="post"
                          action="{{ route(config('PGP.prefix').'.messages.reply', ['id' => $message->id]) }}">
                        @csrf
                        <div class="px-6 py-4">
                            <textarea name="message" placeholder="Enter your reply"
                                      class="w-full rounded-md p-2 border border-gray-300 focus:outline-none focus:border-blue-500"></textarea>
                            <input type="hidden" name="conversation_id" value="{{ $message->id }}">
                            <input type="hidden" name="encrypted_value" value="{{ old('password') ?? $password }}">
                        </div>
                        <div class="px-6 py-4">
                            <button type="submit"
                                    class="btn bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Reply
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @if(session('success'))
            <div class="fixed bottom-0 right-0 p-4 bg-green-500 text-white rounded-lg shadow-md" role="alert">
                {{ session('success') }}
            </div>
        @endif
    </div>
@endsection
