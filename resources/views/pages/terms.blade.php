@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <h1 class="mb-4">Terms of Service</h1>
            
            <p class="lead mb-5">Please read these Terms of Service carefully before using the Karen Culture Sales website. By accessing or using our service, you agree to be bound by these Terms.</p>
            
            <div class="mb-5">
                <h2>1. Acceptance of Terms</h2>
                <p>By accessing or using our website, you agree to be bound by these Terms of Service and all applicable laws and regulations. If you do not agree with any of these terms, you are prohibited from using or accessing this site.</p>
            </div>
            
            <div class="mb-5">
                <h2>2. Products and Services</h2>
                <p>All products sold on Karen Culture Sales are carefully sourced from Karen artisans. We make every effort to accurately display the colors and images of our products, but we cannot guarantee that your computer monitor's display will accurately reflect the actual colors of the products.</p>
                <p>We reserve the right to limit the quantities of any products or services that we offer. All descriptions of products and pricing are subject to change at any time without notice.</p>
            </div>
            
            <div class="mb-5">
                <h2>3. Account Information</h2>
                <p>To access certain features of our website, you may be required to register for an account. You agree to provide accurate, current, and complete information during the registration process and to update such information to keep it accurate, current, and complete.</p>
                <p>You are responsible for maintaining the confidentiality of your account and password and for restricting access to your computer. You agree to accept responsibility for all activities that occur under your account.</p>
            </div>
            
            <div class="mb-5">
                <h2>4. Ordering and Payment</h2>
                <p>When you place an order through our website, you are making an offer to purchase the products you have selected. We reserve the right to accept or decline your order for any reason.</p>
                <p>Payment must be made at the time of ordering. We accept payments via credit/debit cards through our secure Stripe payment gateway and Cash on Delivery (COD) for local orders.</p>
                <p>All prices are displayed in the applicable currency and are subject to change without notice. Prices do not include taxes or shipping charges, which will be added at checkout.</p>
            </div>
            
            <div class="mb-5">
                <h2>5. Shipping and Delivery</h2>
                <p>Delivery timeframes vary depending on your location. We are not responsible for delays caused by customs, postal services, or other circumstances beyond our control.</p>
                <p>Risk of loss and title for items purchased from our website pass to you upon delivery of the items to the carrier.</p>
            </div>
            
            <div class="mb-5">
                <h2>6. Returns and Refunds</h2>
                <p>We accept returns within 14 days of delivery for items in their original condition. Custom-made items are non-returnable unless there's a defect.</p>
                <p>To initiate a return, please contact our customer service team. Once your return is received and inspected, we'll process your refund to the original payment method.</p>
            </div>
            
            <div class="mb-5">
                <h2>7. Intellectual Property</h2>
                <p>The content on the Karen Culture Sales website, including text, graphics, logos, images, and software, is the property of Karen Culture Sales and is protected by copyright and other intellectual property laws.</p>
                <p>You may not use, reproduce, distribute, or create derivative works based upon our content without express written permission.</p>
            </div>
            
            <div class="mb-5">
                <h2>8. User Content</h2>
                <p>By posting reviews or comments on our website, you grant Karen Culture Sales a non-exclusive, royalty-free, perpetual right to use, reproduce, modify, and display your content in connection with our website and business.</p>
                <p>You agree not to post content that is unlawful, defamatory, harassing, threatening, or infringing on the intellectual property rights of others.</p>
            </div>
            
            <div class="mb-5">
                <h2>9. Limitation of Liability</h2>
                <p>Karen Culture Sales shall not be liable for any direct, indirect, incidental, special, consequential, or punitive damages resulting from your use of or inability to use our website or products.</p>
                <p>In no event shall our liability exceed the amount paid by you for the products in question.</p>
            </div>
            
            <div class="mb-5">
                <h2>10. Governing Law</h2>
                <p>These Terms of Service shall be governed by and construed in accordance with the laws of the jurisdiction in which Karen Culture Sales operates, without regard to its conflict of law provisions.</p>
            </div>
            
            <div class="mb-5">
                <h2>11. Changes to Terms</h2>
                <p>We reserve the right to modify these Terms of Service at any time. Changes will be effective immediately upon posting on our website. Your continued use of the website after any changes indicates your acceptance of the new Terms.</p>
            </div>
            
            <div class="mb-5">
                <h2>12. Contact Information</h2>
                <p>If you have any questions about these Terms of Service, please contact us at <a href="mailto:support@karenculturesales.com">support@karenculturesales.com</a> or through our <a href="{{ route('contact') }}">Contact page</a>.</p>
            </div>
            
            <p class="text-muted">Last updated: {{ date('F d, Y') }}</p>
        </div>
    </div>
</div>
@endsection 