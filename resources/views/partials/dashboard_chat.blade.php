<div class="container-fluid">
    <div class="col-xs-12">

        <div class="card">
            <div class="header">
                <h2>Chat Coaches & Mgmnt</h2>

            </div>
            <div class="body">
                <div id="plist" class="people-list">
                    <div class="form-line m-b-15">
                        <input type="text" class="form-control" placeholder="Search...">
                    </div>
                    <div class="tab-content">
                        <div id="chat_user" style="overflow-y:auto; height:590px;">
                            <ul class="chat-list list-unstyled m-b-0">
                                @forelse($teammates ?? [] as $mate)
                                    <li class="clearfix initiate-chat" data-user-id="{{ $mate->id }}">
                                        <img src="{{ $mate->player?->photo ? asset($mate->player->photo) : asset('images/avatar-default.png') }}" alt="avatar">
                                        <div class="about">
                                            <div class="name">{{ $mate->first_name ?? $mate->name }}</div>
                                            <div class="status">
                                                <i class="material-icons offline">fiber_manual_record</i>
                                                offline
                                            </div>
                                        </div>
                                    </li>
                                @empty
                                    <li class="text-center text-muted">No teammates found</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.querySelectorAll('.initiate-chat').forEach(function(el){
    el.addEventListener('click', function(){
        const userId = this.dataset.userId;
        fetch(`/player/chat/initiate/${userId}`)
            .then(resp => resp.json())
            .then(data => {
                if(data.chat_id){
                    window.location.href = `/player/chat?chat_id=${data.chat_id}`;
                }
            });
    });
});
</script>
@endpush
