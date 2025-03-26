@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <h1 class="mb-4">Frequently Asked Questions</h1>
            
            <p class="lead mb-5">Find answers to the most common questions about Karen Culture Sales, our products, ordering, shipping, and more.</p>
            
            <div class="accordion mb-5" id="faqAccordion">
                <!-- Shopping & Products -->
                <div class="mb-4">
                    <h2>Shopping & Products</h2>
                    
                    <div class="accordion-item">
                        <h3 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1" aria-expanded="true" aria-controls="faq1">
                                Are all products authentic Karen crafts?
                            </button>
                        </h3>
                        <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                <p>Yes, all products sold on Karen Culture Sales are 100% authentic and handcrafted by Karen artisans. We work directly with artisans and communities to ensure the authenticity and quality of each item.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h3 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2" aria-expanded="false" aria-controls="faq2">
                                What makes Karen textiles unique?
                            </button>
                        </h3>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                <p>Karen textiles are distinguished by their intricate weaving techniques, vibrant colors, and traditional patterns that tell cultural stories. Each piece is hand-woven using methods passed down through generations, making every item unique and culturally significant.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h3 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3" aria-expanded="false" aria-controls="faq3">
                                How do I care for my Karen textile purchases?
                            </button>
                        </h3>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                <p>We recommend hand washing Karen textiles in cold water with mild soap, then air drying away from direct sunlight. For detailed care instructions specific to your purchase, please refer to the product description or the care card included with your order.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Ordering & Payment -->
                <div class="mb-4">
                    <h2>Ordering & Payment</h2>
                    
                    <div class="accordion-item">
                        <h3 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4" aria-expanded="false" aria-controls="faq4">
                                What payment methods do you accept?
                            </button>
                        </h3>
                        <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                <p>We accept credit/debit cards through our secure Stripe payment gateway. We also offer Cash on Delivery (COD) option for local orders.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h3 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq5" aria-expanded="false" aria-controls="faq5">
                                Can I cancel my order?
                            </button>
                        </h3>
                        <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                <p>Yes, you can cancel your order if it is still in the processing stage. For orders paid via Stripe, cancellations are allowed within 20 minutes of payment. For Cash on Delivery orders, you can cancel until the order is marked as "out for delivery". You can cancel your order from your account's Orders page.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Shipping & Delivery -->
                <div class="mb-4">
                    <h2>Shipping & Delivery</h2>
                    
                    <div class="accordion-item">
                        <h3 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq6" aria-expanded="false" aria-controls="faq6">
                                How can I track my order?
                            </button>
                        </h3>
                        <div id="faq6" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                <p>You can track your order by visiting the "Track" page and entering your order number, or by checking the order details in your account. You will also receive email updates as your order status changes.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h3 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq7" aria-expanded="false" aria-controls="faq7">
                                What is your delivery timeframe?
                            </button>
                        </h3>
                        <div id="faq7" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                <p>Delivery timeframes vary depending on your location. Local deliveries typically take 1-3 business days, while international shipping can take 7-14 business days. Please note that custom orders may take additional time.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Returns & Refunds -->
                <div class="mb-4">
                    <h2>Returns & Refunds</h2>
                    
                    <div class="accordion-item">
                        <h3 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq8" aria-expanded="false" aria-controls="faq8">
                                What is your return policy?
                            </button>
                        </h3>
                        <div id="faq8" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                <p>We accept returns within 14 days of delivery for items in their original condition. Custom-made items are non-returnable unless there's a defect. Please contact us at support@karenculturesales.com to initiate a return.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h3 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq9" aria-expanded="false" aria-controls="faq9">
                                How do I request a refund?
                            </button>
                        </h3>
                        <div id="faq9" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                <p>To request a refund, please contact our customer service team through the Contact page or email support@karenculturesales.com. Include your order number and reason for the refund. Once your return is received and inspected, we'll process your refund to the original payment method.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="text-center mb-5">
                <p>Can't find what you're looking for?</p>
                <a href="{{ route('contact') }}" class="btn btn-primary">Contact Us</a>
            </div>
        </div>
    </div>
</div>
@endsection 