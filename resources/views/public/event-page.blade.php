<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        {{ $page->event->name }} | {{ $page->event->type->getLabel() }}
    </title>
    <script src="https://cdn.tailwindcss.com?plugins=typography"></script>
</head>

<body class="" style="background-color:{{$page->event->color}}0d">
    <main class="w-100 max-w-[1920px] overflow-hidden mx-auto">
        @if($page->main_banner)
        <div class="w-full h-screen md:h-[70vh] relative">
            <img src="{{ asset('storage/' . $page->main_banner) }}"
                alt="Banner"
                class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-black/50 flex flex-col items-center justify-center text-white p-6">

                <div class="text-center">

                    <p class="text-lg md:text-2xl uppercase tracking-[0.3em] font-normal mb-2">
                        Już tylko
                    </p>
                    <div class="relative inline-block">
                        <span x-text="days" class="text-8xl md:text-[10rem] font-black leading-none drop-shadow-[0_10px_10px_rgba(0,0,0,0.5)]">
                            {{ now()->startOfDay()->diffInDays($page->event->date) }}
                        </span>
                        <span class="text-8xl md:text-[10rem] font-black leading-none drop-shadow-[0_10px_10px_rgba(0,0,0,0.5)]">
                            dni
                        </span>
                    </div>
                    <div class="w-48 h-1 bg-white mx-auto mt-4 rounded-full"></div>
                </div>

            </div>
        </div>
        @endif
        @if($page->content)
        <div class="max-w-7xl mx-auto my-20">
            <article class="prose prose-lg max-w-none">
                {!! $page->content !!}
            </article>
        </div>
        @endif


        @if($page->down_content && $page->down_img)
        <div class="max-w-7xl mx-auto my-20 flex flex-col md:flex-row items-center gap-10">
            <div class="w-full md:w-1/2">
                <img src="{{ asset('storage/' . $page->down_img) }}"
                    alt="Image"
                    class="w-full h-auto rounded-lg shadow-md object-cover">
            </div>

            <div class="w-full md:w-1/2 prose max-w-none">
                {!! $page->down_content !!}
            </div>
        </div>
        @elseif($page->down_content)


        @elseif($page->down_img)

        @endif
    </main>
    <footer>
        <div class="w-100 pt-20" style="background-color:{{$page->event->color}}1a">
            <div class="max-w-7xl mx-auto flex flex-col items-center text-center pb-20">
                <h3 class="text-2xl md:text-3xl font-bold mb-3 text-center ">
                    {{ __('Are you ready?') }}
                </h3>
                <p class="text-md md:text-xl mx-auto leading-relaxed text-center mb-6">
                    {{ __('We’ve prepared all the essential information in the guest panel. Click below to have everything at your fingertips!') }}
                </p>
                <a href="{{$eventURL}}"
                    class="group relative inline-flex items-center justify-center px-10 py-5 font-bold text-black transition-all duration-300 rounded-xl hover:bg-gray-100 hover:shadow-[0_0_30px_rgba(255,255,255,0.4)] active:scale-95 " style="background-color:{{$page->event->color}}">
                    <span class="relative">{{ __('Check it out') }}</span>
                </a>
            </div>
            <hr class="border-black  mx-auto max-w-7xl">
            <div class="w-100 pt-4 pb-4 flex flex-col items-center text-center text-black">
                © PartyMaker {{date('Y')}}
            </div>
        </div>
    </footer>
</body>

</html>