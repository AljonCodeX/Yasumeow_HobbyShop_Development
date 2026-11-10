<div class="modal fade" id="rateModal" tabindex="-1" aria-labelledby="rateTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="" id="rateForm" class="modal-content">
            @csrf
            <div class="modal-body text-center px-4 pt-4 pb-2">
                <h2 class="h5 fw-bold mb-1" id="rateTitle">How are we doing?</h2>
                <p class="small text-muted mb-3">Rate <span id="rateLabel"></span></p>
                <div class="rate-stars">
                    @foreach ([5, 4, 3, 2, 1] as $n)
                        <input type="radio" name="stars" id="star{{ $n }}" value="{{ $n }}" required>
                        <label for="star{{ $n }}" title="{{ $n }} {{ $n === 1 ? 'star' : 'stars' }}">
                            <i class="bi bi-star-fill"></i>
                        </label>
                    @endforeach
                </div>
            </div>
            <div class="modal-footer border-0 justify-content-center pt-2 pb-4">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-dark">Submit</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Rate buttons carry the form action and label; the cards are links, so stop the link from opening
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('[data-rate-action]');
        if (!btn) return;
        e.preventDefault();
        var form = document.getElementById('rateForm');
        form.reset();
        form.action = btn.dataset.rateAction;
        document.getElementById('rateLabel').textContent = btn.dataset.rateLabel;
    });
</script>