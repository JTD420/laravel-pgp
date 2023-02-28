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
                <div class="mb-4">
                    <h4 class="text-sm font-bold mb-2">Recent conversations</h4>
                    {{-- List of recent conversations --}}
                    <div class="bg-white rounded-md shadow-md overflow-hidden">
                        <ul class="flex flex-col-reverse">
                            @foreach($conversations as $conversation)
                                <li class="{{ request()->routeIs('messages.show') && request()->id == $conversation->id ? 'bg-blue-100' : '' }} hover:bg-gray-100 p-2 {{ $conversation->is_read ? '' : 'bg-slate-50' }} relative">
                                    <a href="#" data-modal="viewModal" data-message-id="{{ $conversation->id }}"
                                       class="block font-medium text-gray-900 hover:text-gray-700 open-view-modal">
                                        @if($conversation instanceof \App\Models\PGP\MessageRecipient)
                                            <img src="{{ Gravatar::get($conversation->message->sender->email) }}"
                                                 alt="{{ $conversation->message->sender->name }}"
                                                 class="inline-block ml-2 mr-2 w-8 h-8 rounded-full">
                                            <span class="ml-2">{{ $conversation->message->sender->name }}</span>
                                        @elseif($conversation instanceof \App\Models\PGP\Message)
                                            <img src="{{ Gravatar::get($conversation->sender->email) }}"
                                                 alt="{{ $conversation->sender->name }}"
                                                 class="inline-block ml-2 mr-2 w-8 h-8 rounded-full">
                                            <span class="ml-2">{{ $conversation->sender->name }}</span>
                                        @endif
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div>
                    <h4 class="text-sm font-bold mb-2">New message</h4>
                    {{-- New message form --}}
                    <form method="GET" action="{{ route(config('PGP.prefix').'.messages.send') }}">
                        @csrf
                        <div class="bg-white rounded-md shadow-md overflow-hidden">
                            <div class="p-2">
                                <input type="text" name="receiver" placeholder="Recipient"
                                       class="w-full rounded-md p-2 border border-gray-300 focus:outline-none focus:border-blue-500">
                            </div>
                            <div class="p-2">
                                <input type="text" name="subject" placeholder="Subject"
                                       class="w-full rounded-md p-2 border border-gray-300 focus:outline-none focus:border-blue-500">
                            </div>
                            <div class="p-2">
                                <textarea name="message" placeholder="Message"
                                          class="w-full rounded-md p-2 border border-gray-300 focus:outline-none focus:border-blue-500"></textarea>
                            </div>
                            <div class="p-2">
                                <button type="submit"
                                        class="btn bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                    Send message
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            {{-- Right content --}}
            <div class="lg:ml-4 lg:w-5/6">
                {{-- List of messages --}}
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    {{-- Message list header --}}
                    <div class="px-6 py-4 bg-gray-100 border-b border-gray-200 flex justify-between">
                        <div class="flex items-center">
                            <h2 class="text-xl font-semibold text-gray-700">Messages</h2>
                        </div>
                        <div class="flex items-center">
                            <button
                                class="btn bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                                type="button" id="compose" data-modal="composeModal">
                                Compose
                            </button>
                        </div>
                    </div>
                    {{-- Message list items --}}
                    <div class="px-6 py-4 bg-gray-100 flex flex-col-reverse">
                        @if(count($conversations) > 0)
                            @foreach($conversations as $message)
                                <div
                                    class="flex items-center {{ $message->is_read ? '' : 'bg-gray-200 ring-2 ring-slate-200' }} justify-between px-4 py-4 hover:bg-gray-300 {{ $message->is_read ? 'rounded-lg' : 'rounded' }}">
                                    <div class="flex items-center">
                                        @if($message instanceof \App\Models\PGP\Message)
                                            @if($message->replies->count() > 0)
                                                @foreach($message->replies->sortByDesc('created_at')->unique('sender_id')->take(3) as $reply)
                                                    <img src="{{ Gravatar::get($reply->sender->email) }}"
                                                         alt="{{ $reply->sender->name }}" class="w-8 h-8 rounded-full">
                                                @endforeach
                                            @else
                                                <img src="{{ Gravatar::get($message->sender->email) }}"
                                                     alt="{{ $message->sender->name }}" class="w-8 h-8 rounded-full">
                                                @foreach($message->recipients->unique('id')->take(2) as $recipient)
                                                    <img src="{{ Gravatar::get($recipient->email) }}"
                                                         alt="{{ $recipient->name }}" class="w-8 h-8 rounded-full">
                                                @endforeach
                                            @endif
                                            <div class="ml-4">
                                                <p class="text-sm font-semibold text-gray-700">
                                                    @if($message->replies->count() > 0)
                                                        @foreach($message->replies->sortByDesc('created_at')->unique('sender_id')->take(3) as $key => $reply)
                                                            {{ $reply->sender->name }}
                                                            @if(!($key == count($message->replies->sortByDesc('created_at')->unique('sender_id')->take(3)) - 1))
                                                                ,
                                                            @endif
                                                        @endforeach
                                                    @else
                                                        {{ $message->sender->name }} ,
                                                        @foreach($message->recipients->unique('id')->take(2) as $recipient)
                                                            {{ $recipient->name }}
                                                        @endforeach
                                                    @endif
                                                </p>
                                                <p class="text-xs font-medium text-gray-600">{{ $message->subject }}</p>
                                            </div>
                                        @elseif($message instanceof \App\Models\PGP\MessageRecipient)
                                            <img src="{{ Gravatar::get($message->latest_reply_sender_email) }}"
                                                 alt="{{ $message->message->sender->name }}"
                                                 class="w-8 h-8 rounded-full">
                                            <div class="ml-4">
                                                <p class="text-sm font-semibold text-gray-700">{{ $message->message->sender->name }}</p>
                                                <p class="text-xs font-medium text-gray-600">{{ $message->subject }}</p>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-auto flex items-center">
                                        <p class="text-xs font-medium text-gray-600">{{ $message->latest_reply_timestamp->diffForHumans() }}</p>
                                        <button
                                            class="btn bg-gray-300 hover:bg-gray-400 text-gray-700 font-bold py-1 px-3 rounded-full focus:outline-none focus:shadow-outline ml-2 view-button"
                                            type="button" data-modal="viewModal" data-message-id="{{ $message->id }}">
                                            View
                                        </button>
                                    </div>
                                </div>
                                {{-- Decrypt Conversation Modal --}}
                                <div
                                    class="modal fixed bottom-0 inset-x-0 px-4 pb-4 sm:inset-0 sm:p-0 hidden sm:items-center sm:justify-center"
                                    id="viewModal" tabindex="-1" role="dialog" aria-labelledby="viewModalLabel"
                                    aria-hidden="true" data-target="#viewModal">
                                    <div class="bg-white rounded-lg shadow-lg overflow-hidden sm:w-full sm:max-w-md">
                                        <div class="p-4">
                                            <div class="flex items-center justify-between pb-3">
                                                <h5 class="text-2xl font-bold text-gray-700" id="viewModalLabel">Decrypt
                                                    Message</h5>
                                                <button type="button"
                                                        class="modal-close cursor-pointer text-3xl font-bold leading-none hover:text-gray-500"
                                                        data-dismiss="modal" data-target="#viewModal">&times;
                                                </button>
                                            </div>
                                            <form method="POST" action="" id="decryptForm">
                                                @csrf
                                                <input type="hidden" name="messageId" id="messageId" value="">
                                                <div class="flex flex-col my-4">
                                                    <label for="password"
                                                           class="text-lg font-bold text-gray-700 block mb-2">Password</label>
                                                    <input type="password" name="password" id="password"
                                                           placeholder="Password"
                                                           class="w-full p-2 rounded-md border border-gray-300 focus:outline-none focus:border-blue-500">
                                                </div>
                                                <div class="flex justify-end">
                                                    <button type="submit" id="decryptButton"
                                                            class="btn bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                                        Decrypt
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p class="px-4 py-4 text-sm font-medium text-gray-600">You have no messages.</p>
                        @endif
                    </div>
                </div>
                {{-- Compose Conversation Modal --}}
                <div
                    class="modal fixed bottom-0 inset-x-0 px-4 pb-4 sm:inset-0 sm:p-0 hidden sm:items-center sm:justify-center"
                    id="composeModal" tabindex="-1" role="dialog" aria-labelledby="composeModalLabel" aria-hidden="true"
                    data-target="#composeModal">
                    <div class="bg-white rounded-lg shadow-lg overflow-hidden sm:w-full sm:max-w-md">
                        <div class="p-4">
                            <div class="flex items-center justify-between pb-3">
                                <h5 class="text-2xl font-bold text-gray-700" id="composeModalLabel">New Message</h5>
                                <button type="button"
                                        class="btn bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                                        data-dismiss="modal" data-target="#composeModal">Close
                                </button>
                                <button type="button"
                                        class="modal-close cursor-pointer text-3xl font-bold leading-none hover:text-gray-500"
                                        data-dismiss="modal" data-target="#composeModal">&times;
                                </button>
                            </div>
                            <form method="GET" action="{{ route(config('PGP.prefix').'.messages.send') }}">
                                @csrf
                                <div class="flex flex-col my-4">
                                    <label for="receiver"
                                           class="text-lg font-bold text-gray-700 block mb-2">Recipient</label>
                                    <input type="text" name="receiver" id="receiver" placeholder="Recipient"
                                           class="w-full p-2 rounded-md border border-gray-300 focus:outline-none focus:border-blue-500">
                                </div>
                                <div class="flex flex-col my-4">
                                    <label for="subject"
                                           class="text-lg font-bold text-gray-700 block mb-2">Subject</label>
                                    <input type="text" name="subject" id="subject" placeholder="Subject"
                                           class="w-full p-2 rounded-md border border-gray-300 focus:outline-none focus:border-blue-500">
                                </div>
                                <div class="flex flex-col my-4">
                                    <label for="message"
                                           class="text-lg font-bold text-gray-700 block mb-2">Message</label>
                                    <textarea name="message" id="message" placeholder="Message"
                                              class="w-full p-2 rounded-md border border-gray-300 focus:outline-none focus:border-blue-500"></textarea>
                                </div>
                                <div class="flex justify-end">
                                    <button type="submit"
                                            class="btn bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                        Send message
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
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
