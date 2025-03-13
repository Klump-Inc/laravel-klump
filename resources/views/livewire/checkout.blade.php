<div>
    <div id="klump__checkout"></div>
    <button wire:click="checkout" class="klump-button">
        Pay with Klump
    </button>

    <script>
        document.addEventListener('livewire:load', function () {
            window.addEventListener('klump-checkout', event => {
                const payload = event.detail.payload;
                new Klump({
                    ...payload,
                    onSuccess: (data) => {
                        window.location.href = payload.data.redirect_url;
                    },
                    onError: (error) => {
                        console.error('Payment failed:', error);
                    },
                    onClose: () => {
                        console.log('Checkout closed');
                    }
                });
            });
        });
    </script>
</div>
