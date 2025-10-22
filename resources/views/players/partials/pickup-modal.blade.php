
<!-- Pickup Game Modal -->
<div class="modal fade" id="pickupModal" tabindex="-1" aria-labelledby="pickupModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content bg-dark text-white">
            <div class="modal-header">
                <h5 class="modal-title" id="pickupModalLabel">Pickup Game</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Do you want to join or leave this game?
            </div>
            <div class="modal-footer">
                <button id="pickupModalJoinBtn" class="btn btn-success" onclick="joinGame(this)">Join Game</button>
                <button id="pickupModalLeaveBtn" class="btn btn-danger d-none" onclick="leaveGame(this)">Leave Game</button>
            </div>
        </div>
    </div>
</div>
