<div class="modal fade" id="credentialsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #234dcb;">
                <h5 class="modal-title">Contact Person Credentials</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @if(session('username') && session('password'))
                <div class="alert alert-info">
                    <p>Boarding house has been registered successfully!</p>
                    <p>Please provide these credentials to the contact person:</p>
                    <p><strong>Username:</strong> {{ session('username') }}</p>
                    <p><strong>Password:</strong> {{ session('password') }}</p>
                </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@if(session('show_credentials'))
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = new bootstrap.Modal(document.getElementById('credentialsModal'));
        modal.show();
    });
</script>
@endpush
@endif