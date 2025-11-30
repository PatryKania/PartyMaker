<x-filament-panels::layout.base>
    <div class="custom-auth-wrapper">

        <div class="auth-left auth-column">
            {{ $slot }}
        </div>
        <div class="auth-right auth-column">
            <div class="auth-column-content">
                <h1 class="title">{{__('Create unforgettable moments!')}}</h1>
                <h2 class="sub-title">{{__('Organize your event quickly and stress-free.')}}</h2>
                <p class="text">
                    {{__('Our app is your reliable assistant for planning any kind of celebration. Whether you’re organizing an intimate birthday party, a christening, your dream wedding, or a professional corporate event - we have everything you need to make the preparation process smooth and stress-free.')}}
                </p>
                <p class="text-bold">
                    {{__('Log in today and discover how enjoyable planning the perfect event can be!')}}
                </p>
            </div>

        </div>
    </div>
</x-filament-panels::layout.base>