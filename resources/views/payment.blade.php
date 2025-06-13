<form action="/create-order" method="POST" id="payment-form">
    @csrf
    <input type="hidden" name="property_id" value="RELTRS25NZ51">
    <input type="text" name="name" placeholder="Your Name">
    <input type="email" name="email" placeholder="Your Email">
    <input type="text" name="mobile" placeholder="Your Mobile Number">
    <input type="number" name="amount" placeholder="Amount">
    <button type="submit">Pay Now</button>
</form>

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
    document.getElementById('payment-form').addEventListener('submit', function(e) {
        e.preventDefault();

        var data = new FormData(this);

        fetch('api/create-order', {
                method: 'POST',
                body: data,
            })
            .then(response => response.json())
            .then(data => {
                var options = {
                    key: data.key,
                    amount: data.amount * 100,
                    currency: "INR",
                    name: "RealEstate",
                    description: "Property Payment",
                    order_id: data.order_id,
                    handler: function(response) {
                        var paymentData = {
                            order_id: data.order_id,
                            payment_id: response.razorpay_payment_id,
                            signature: response.razorpay_signature
                        };

                        fetch('api/verify-payment', {
                                method: 'POST',
                                body: JSON.stringify(paymentData),
                                headers: {
                                    'Content-Type': 'application/json',
                                },
                            })
                            .then(response => response.json())
                            .then(data => alert(data.message))
                            .catch(error => alert(error.message));
                    }
                };

                var rzp1 = new Razorpay(options);
                rzp1.open();
            })
            .catch(error => alert(error.message));
    });
</script>