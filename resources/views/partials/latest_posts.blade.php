@if($blogs->count() > 0)    
<div class="text-white">
    <h6 class="text-lg font-semibold mb-3">Latest Posts</h6>
    <ul class="space-y-2">
        @foreach($blogs as $blog)
            <li class="text-sm">
                <strong>{{ $blog->title }}</strong><br>
                <span class="text-gray-400">{{ \Illuminate\Support\Str::limit($blog->content, 60) }}</span>
            </li>
        @endforeach
    </ul>
</div>
@endif