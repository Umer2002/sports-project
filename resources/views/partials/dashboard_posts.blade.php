    <div class="card">
        <div class="header">
            <h2>Latest Posts</h2>
            <ul class="header-dropdown">
                <li class="dropdown">
                    <a href="#" onclick="return false;" class="dropdown-toggle" data-bs-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                        <i class="material-icons">more_vert</i>
                    </a>
                    <ul class="dropdown-menu pull-right">
                        <li>
                            <a href="#" onclick="return false;">Add</a>
                        </li>
                        <li>
                            <a href="#" onclick="return false;">Edit</a>
                        </li>
                        <li>
                            <a href="#" onclick="return false;">Delete</a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
        <div class="body">
            <div class="card-block">
                @if(isset($posts) && count($posts) > 0)
                    @foreach($posts as $post)
                        <div class="row m-b-20">
                            <div class="col-auto p-r-0">
                                <img src="{{ $post->image ?? asset('assets/images/posts/default.jpg') }}" alt="user image" class="latest-posts-img">
                            </div>
                            <div class="col">
                                <h6>{{ $post->title ?? 'Untitled' }}</h6>
                                <p class="text-muted m-b-5">
                                    <i class="fa fa-play-circle-o"></i>
                                    {{ $post->type ?? 'Post' }} |
                                    {{ $post->created_at ? $post->created_at->diffForHumans() : '' }}
                                </p>
                                <p class="text-muted ">{{ $post->excerpt ?? Str::limit($post->body ?? '', 60) }}</p>
                            </div>
                        </div>
                    @endforeach
                    <div class="text-center">
                    <a href="#" class="b-b-primary text-primary">View All Posts</a>
                </div>
                @else
                    <div class="row m-b-20">
                        <div class="col text-center text-muted">
                            No posts available.
                        </div>
                    </div>
                @endif
                
            </div>
        </div>
    </div>

