@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <h1 class="mb-4">Privacy Policy</h1>
            <p class="lead mb-5">Last updated: {{ date('F d, Y') }}</p>

            <div class="card mb-4">
                <div class="card-body">
                    <h2>Introduction</h2>
                    <p>Karen Culture Sales ("we," "our," or "us") respects your privacy and is committed to protecting your personal data. This privacy policy will inform you about how we look after your personal data when you visit our website and tell you about your privacy rights and how the law protects you.</p>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <h2>Information We Collect</h2>
                    <p>We collect several different types of information for various purposes to provide and improve our service to you:</p>
                    <ul>
                        <li>Personal identification information (Name, email address, phone number, etc.)</li>
                        <li>Shipping and billing addresses</li>
                        <li>Payment information (processed securely through our payment processors)</li>
                        <li>Order history and preferences</li>
                        <li>Usage data and cookies</li>
                    </ul>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <h2>How We Use Your Information</h2>
                    <p>We use the collected data for various purposes:</p>
                    <ul>
                        <li>To process and deliver your orders</li>
                        <li>To manage your account and provide customer support</li>
                        <li>To send you updates about your orders</li>
                        <li>To communicate about promotions, special offers, and updates</li>
                        <li>To improve our website and services</li>
                        <li>To prevent fraudulent transactions</li>
                    </ul>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <h2>Data Security</h2>
                    <p>We implement appropriate security measures to protect your personal information:</p>
                    <ul>
                        <li>Secure SSL encryption for all transactions</li>
                        <li>Regular security assessments and updates</li>
                        <li>Limited access to personal information by employees</li>
                        <li>Strict data handling procedures</li>
                    </ul>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <h2>Your Rights</h2>
                    <p>Under certain circumstances, you have rights under data protection laws in relation to your personal data:</p>
                    <ul>
                        <li>The right to access your personal data</li>
                        <li>The right to correct any inaccurate personal data</li>
                        <li>The right to request erasure of your personal data</li>
                        <li>The right to object to processing of your personal data</li>
                        <li>The right to request restriction of processing your personal data</li>
                        <li>The right to request transfer of your personal data</li>
                    </ul>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <h2>Cookies</h2>
                    <p>We use cookies and similar tracking technologies to track activity on our website and hold certain information. You can instruct your browser to refuse all cookies or to indicate when a cookie is being sent.</p>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <h2>Changes to This Privacy Policy</h2>
                    <p>We may update our Privacy Policy from time to time. We will notify you of any changes by posting the new Privacy Policy on this page and updating the "Last updated" date at the top of this Privacy Policy.</p>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h2>Contact Us</h2>
                    <p>If you have any questions about this Privacy Policy, please contact us:</p>
                    <ul class="list-unstyled">
                        <li>By email: privacy@karenculturesales.com</li>
                        <li>By phone: +1 (555) 123-4567</li>
                        <li>By mail: 123 Karen Street, San Francisco, CA 94110, United States</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 