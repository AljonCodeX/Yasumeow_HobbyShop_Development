@php
    $toRate = $toRate ?? null;
    $toRateEvent = $toRateEvent ?? null;
@endphp

@if ($toRate || $toRateEvent)
    @php
        $rateAction = $toRate ? route('reservations.rate', $toRate) : route('events.rate', $toRateEvent);
        $rateLabel  = $toRate ? 'your pickup of Reservation #' . $toRate->id : $toRateEvent->title;
        $rateKey    = $toRate ? 'r' . $toRate->id : 'e' . $toRateEvent->id;
    @endphp

    <button type="button" id="rateTrigger" class="d-none" data-bs-toggle="modal" data-bs-target="#rateModal"></button>

    <div class="modal fade" id="rateModal" tabindex="-1" aria-labelledby="rateTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form method="POST" action="{{ $rateAction }}" class="modal-content">
                @csrf
                <div class="modal-body text-center px-4 pt-4 pb-2">
                    <h2 class="h5 fw-bold mb-1" id="rateTitle">How are we doing?</h2>
                    <p class="small text-muted mb-3">Rate {{ $rateLabel }}</p>
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
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Maybe later</button>
                    <button class="btn btn-dark">Submit</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Open once per browser session; "Maybe later" hides it until the next session
        window.addEventListener('load', function () {
            var key = 'rate-skip-{{ $rateKey }}';
            try { if (sessionStorage.getItem(key)) return; } catch (e) {}
            document.getElementById('rateTrigger').click();
            document.getElementById('rateModal').addEventListener('hidden.bs.modal', function () {
                try { sessionStorage.setItem(key, '1'); } catch (e) {}
            });
        });
    </script>
@endif