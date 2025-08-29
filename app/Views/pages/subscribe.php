<div class="page-hero">
    <div class="container">
        <h1>Choose Your Plan</h1>
        <p>Join VillaStudio today to unlock exclusive content, offline downloads, and an ad-free experience.</p>
    </div>
</div>

<div class="container content-container">
    <form action="/payment/initiate" method="POST" id="subscriptionForm">
        <div class="plans-container">
            <?php foreach ($plans as $plan): ?>
            <div class="plan-card <?php echo ($plan['name'] === 'Premium Monthly') ? 'highlighted' : ''; ?>">
                <?php if ($plan['name'] === 'Premium Monthly'): ?>
                    <div class="plan-badge">Most Popular</div>
                <?php endif; ?>
                <h3><?php echo htmlspecialchars($plan['name']); ?></h3>
                <div class="plan-price">
                    <sup>$</sup><?php echo htmlspecialchars(floor($plan['price'])); ?><sup>.<?php echo htmlspecialchars(substr(strrchr($plan['price'], "."), 1)); ?></sup>
                    <span>/ <?php echo ($plan['duration_days'] > 31) ? 'Year' : 'Month'; ?></span>
                </div>
                <p class="plan-description"><?php echo htmlspecialchars($plan['description']); ?></p>
                <ul class="plan-features">
                    <li>HD Streaming</li>
                    <li>Offline Downloads</li>
                    <?php if (strpos($plan['name'], 'Premium') !== false): ?>
                        <li>4K Ultra HD Streaming</li>
                        <li>Early Access to Originals</li>
                    <?php endif; ?>
                </ul>
                <div class="plan-select">
                    <input type="radio" name="plan_id" id="plan-<?php echo $plan['id']; ?>" value="<?php echo $plan['id']; ?>" required>
                    <label for="plan-<?php echo $plan['id']; ?>">Choose Plan</label>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="payment-selection">
            <h3>Select Payment Method</h3>
            <div class="payment-methods">
                <div class="method-option">
                    <input type="radio" id="flutterwave" name="payment_method" value="flutterwave" required>
                    <label for="flutterwave">
                        <img src="/public/images/flutterwave-logo.png" alt="Flutterwave">
                        <span>Card, Bank, USSD</span>
                    </label>
                </div>
                <div class="method-option">
                    <input type="radio" id="paypal" name="payment_method" value="paypal">
                    <label for="paypal">
                        <img src="/public/images/paypal-logo.png" alt="PayPal">
                        <span>PayPal</span>
                    </label>
                </div>
                <div class="method-option">
                    <input type="radio" id="bank_transfer" name="payment_method" value="bank_transfer">
                    <label for="bank_transfer">
                        <img src="/public/images/bank-transfer-icon.png" alt="Bank Transfer">
                        <span>Direct Bank Transfer</span>
                    </label>
                </div>
            </div>
            <div id="bankTransferDetails" class="bank-details-hidden">
                <h4>Bank Account Details</h4>
                <p>Please transfer the total amount to the account below. Use your email address as the payment reference.</p>
                <ul>
                    <li><strong>Bank Name:</strong> Polaris Bank</li>
                    <li><strong>Account Name:</strong> Villahandle</li>
                    <li><strong>Account Number:</strong> 4092036908</li>
                </ul>
                <p class="small-text">Your subscription will be activated manually within 24 hours of payment confirmation.</p>
            </div>
            <div class="submit-container">
                <button type="submit" class="btn btn-primary btn-lg">Proceed to Payment</button>
            </div>
        </div>
    </form>
</div>

<script src="/public/js/payment.js"></script>