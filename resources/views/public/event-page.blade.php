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
<body class="bg-gray-50">
    <main class="max-w-4xl mx-auto my-10 bg-white shadow-sm rounded-xl overflow-hidden">
        @if($page->main_banner)
            <div class="w-full h-96">
                <img src="{{ asset('storage/' . $page->main_banner) }}" 
                     alt="Banner" 
                     class="w-full h-full object-cover">
            </div>
        @endif
        <div class="p-8">
            <article class="prose prose-lg max-w-none">
                {!! $page->content !!}
            </article>
        </div>
    </main>
</body>
</html>