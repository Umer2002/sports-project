<!-- Flag Carousel using flag-icon-css -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/6.6.6/css/flag-icons.min.css">

<section class="py-5 bg-dark text-center">
    <div class="container-fluid">
        <div class="d-flex justify-content-center gap-3 overflow-auto px-3 emoji-flag-carousel">
            @php
                $countries = ['fr', 'de', 'in', 'cn', 'es', 'ad', 'jp', 'it', 'ca', 'sg', 'br', 'by', 'se', 'ch', 'dk'];
            @endphp

            @foreach ($countries as $code)
                <div class="rounded-4 bg-secondary d-flex align-items-center justify-content-center shadow-sm"
                    style="width: 60px; height: 60px;">
                    <span class="flag-icon flag-icon-{{ $code }}"
                        style="font-size: 2rem; width: 100%; height: 100%; background-size: cover; border-radius: 10px;"></span>
                </div>
            @endforeach
        </div>
    </div>
</section>
<style>
    .emoji-flag-carousel {
        scrollbar-width: none;
        -ms-overflow-style: none;
    }

    .emoji-flag-carousel::-webkit-scrollbar {
        display: none;
    }
</style>
