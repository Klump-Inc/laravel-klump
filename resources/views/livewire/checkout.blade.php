<div>
    <div id="klump__checkout"></div>

    <script>
        document.addEventListener('livewire:initialized', function () {
            // Initialize Klump checkout on component load
            Livewire.dispatch('checkout');
            
            Livewire.on('klump-checkout', event => {
                const payload = event.payload;
                // Initialize Klump checkout
                new Klump({
                    ...payload,
                    onSuccess: (data) => {
                        if (payload.data.redirect_url) {
                            window.location.href = payload.data.redirect_url;
                        }
                    },
                    onError: (error) => {
                        console.error('Payment failed:', error);
                    },
                    onClose: () => {
                        console.log('Checkout closed');
                    }
                });
            });
            
            Livewire.on('klump-error', event => {
                console.error('Klump initialization error:', event.message);
            });
        });
    </script>
</div>
