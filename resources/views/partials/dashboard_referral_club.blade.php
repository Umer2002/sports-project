
<!-- Referral card -->
<div class=" _invite-card text-white mb-4 rounded-4 card">
    <div class="body">

        <!-- ROW 1 ▸ Invite players / Earn 50$  + logo -->
        <div class="d-flex">
            <!-- Step number -->
            <div class="step-box flex-shrink-0 me-3">1.</div>

            <!-- Text & big amount -->
            <div class="flex-grow-1">
                <div class="d-flex flex-wrap align-items-center">
                    <span class="me-2 vsmall text-uppercase fw-semibold">Invite players</span>
                    <span class="text-success fw-bold text-uppercase ms-auto me-2">Earn</span>
                    <span class="display-6 fw-bold mb-0 cblue">50 $</span>
                </div>
            </div>

            <!-- Logo -->
            <img src="{{ url('assets/images/logo.png') }}" alt="Play 2 Earn logo" height="40"
                class="ms-3 d-none d-md-block" />
        </div>

        <!-- ROW 2 ▸ Description + Earn USD/Cash label -->
        <div class="d-flex">
            <!-- Step number -->
            <div class="step-box flex-shrink-0 me-3">2.</div>

            <!-- Description -->
            <p class="mb-0 flex-grow-1 vsmall">
                For every player that joins your club through <strong>Play 2 Earn Sports</strong> you will receive <strong>50 $
                    USD</strong>
            </p>

            <!-- Earn USD/Cash label -->
            <div class="d-flex flex-column text-success fw-bold text-end ms-3">
                <span>Earn</span>
                <span class="text-uppercase vsmall text-info">USD<br />cash</span>
            </div>
        </div>

        <!-- ROW 3 ▸ Email input -->
        <form class="w-100">

                <input type="email" id="inviteEmailInput" class="form-control" style="    background-color: var(--bs-code-color);
                width: 65%;
                float: right;
                line-height: 1;
                padding-left: 13px;
                height: 30px;
                border-radius: 46px;" placeholder="Enter email here"
                    required />


        </form>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const input = document.getElementById('inviteEmailInput');
    if (!input) return;
    input.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const email = input.value.trim();
            if (email) {
                fetch("{{ route('send-invite-email') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({ email })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {

                        alert(data.msg);
                        input.value = '';

                        // Refresh the page to update invite count
                        location.reload();
                    } else {
                        alert(data.message || 'Failed to send invite.');
                    }
                })
                .catch(() => alert('Failed to send invite.'));
            }
        }
    });
});
</script>
