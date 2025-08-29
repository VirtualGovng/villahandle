document.addEventListener('DOMContentLoaded', () => {
    const paymentMethodRadios = document.querySelectorAll('input[name="payment_method"]');
    const bankDetails = document.getElementById('bankTransferDetails');

    if (paymentMethodRadios.length > 0 && bankDetails) {
        paymentMethodRadios.forEach(radio => {
            radio.addEventListener('change', () => {
                if (radio.value === 'bank_transfer' && radio.checked) {
                    bankDetails.classList.remove('bank-details-hidden');
                } else {
                    bankDetails.classList.add('bank-details-hidden');
                }
            });
        });
    }

    const planRadios = document.querySelectorAll('input[name="plan_id"]');
    planRadios.forEach(radio => {
        radio.addEventListener('change', (e) => {
            document.querySelectorAll('.plan-card').forEach(card => card.classList.remove('selected'));
            e.target.closest('.plan-card').classList.add('selected');
        });
    });
});