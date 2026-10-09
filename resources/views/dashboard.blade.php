<x-app-layout>
    @if(Auth::user()->isAdmin())
        @include('dashboard.admin')
    @else
        @include('dashboard.employee')
    @endif
</x-app-layout>
