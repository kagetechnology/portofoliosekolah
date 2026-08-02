@extends('layouts.base')

@section('body')
    @include('layouts.partials.public-nav')

    <main class="mx-auto max-w-6xl px-4 py-8">
        @if (session('status'))
            <div class="mb-4 rounded border border-green-300 bg-green-50 px-4 py-2 text-green-800">
                {{ session('status') }}
            </div>
        @endif
        @yield('content')
    </main>

    @include('layouts.partials.footer')
@endsection
