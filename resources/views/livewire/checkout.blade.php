<div>
    <div id="klump__checkout"></div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get the order data directly from PHP
            const order = @json($order);
            let items = @json($items);
            const customer = @json($customer);
            const meta_data = @json($meta_data ?? []);

            // Function to create a fresh payload each time
            function createPayload() {
                const payload = {
                    publicKey: '{{ config('klump.public_key') }}',
                    data: {
                        amount: order.amount,
                        currency: order.currency,
                        email: customer.email,
                        merchant_reference: order.reference || order.id,
                        shipping_fee: order.shipping_fee || 0,
                        items: items,
                        meta_data: {
                            order_id: order.id,
                            klump_plugin_source: 'laravel',
                            klump_plugin_version: '1.0.0',
                            custom_fields: meta_data
                        }
                    },
                    onSuccess: () => {
                        if (order.redirect_url) {
                            window.location.href = order.redirect_url;
                        }
                    },
                    onError: (error) => {
                        console.error('Payment failed:', error);
                    },
                    onClose: () => {
                        console.log('Checkout closed');
                    },
                    onLoad: () => {
                        console.log('Checkout loaded');
                    },
                    onOpen: () => {
                        console.log('Checkout opened');
                    }
                };

                // Add redirect_url if available
                if (order.redirect_url) {
                    payload.data.redirect_url = order.redirect_url;
                }

                // Add customer details if available
                if (customer.name) {
                    const nameParts = customer.name.split(' ');
                    payload.data.first_name = nameParts[0] || '';
                    payload.data.last_name = nameParts.slice(1).join(' ') || '';
                }

                if (customer.phone) {
                    payload.data.phone = customer.phone;
                }

                return payload;
            }

            // Initialize Klump to render the button
            try {
                setTimeout(() => {
                    const klumpButton = document.getElementById('klump__checkout');
                    if (klumpButton) {
                        klumpButton.addEventListener('click', function() {
                            try {
                                // Create a new instance with a fresh payload when clicked
                                new Klump(createPayload());
                            } catch (error) {
                                console.error('Error initializing Klump on click:', error);
                            }
                        });
                    }
                }, 500);
            } catch (error) {
                console.error('Error initializing Klump:', error);
            }
        });
    </script>
</div>
