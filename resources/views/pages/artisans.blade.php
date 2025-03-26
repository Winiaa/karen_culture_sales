@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-md-10 mx-auto">
            <h1 class="mb-4">Our Artisans</h1>
            
            <p class="lead mb-5">Meet the talented Karen artisans behind our handcrafted products. Each person represents generations of cultural heritage and exceptional craftsmanship.</p>
            
            <!-- Featured Artisan -->
            <div class="card mb-5">
                <div class="row g-0">
                    <div class="col-md-5">
                        <img src="https://via.placeholder.com/600x800" class="img-fluid rounded-start h-100 object-fit-cover" alt="Master Weaver Naw Paw">
                    </div>
                    <div class="col-md-7">
                        <div class="card-body h-100 d-flex flex-column justify-content-center">
                            <h2 class="card-title">Naw Paw</h2>
                            <h6 class="text-muted mb-3">Master Weaver | Village Elder</h6>
                            <p class="card-text">Naw Paw has been weaving traditional Karen textiles for over 50 years. As a village elder, she plays a crucial role in passing down traditional weaving techniques to younger generations. Her intricate patterns tell stories of Karen history and culture, with each piece taking several weeks to complete.</p>
                            <p class="card-text">Her specialty is the traditional Karen blouse (Nee' Thoo Po'), which features complex geometric patterns and vibrant natural dyes she creates from plants gathered in the surrounding forests.</p>
                            <div class="mt-3">
                                <a href="{{ route('products.index') }}?artisan=naw-paw" class="btn btn-outline-primary">View Naw Paw's Products</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Artisan Grid -->
            <h2 class="mb-4">Meet Our Artisans</h2>
            <div class="row mb-5">
                <!-- Artisan 1 -->
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <img src="https://via.placeholder.com/400x400" class="card-img-top" alt="Saw Htoo">
                        <div class="card-body">
                            <h3 class="card-title h5">Saw Htoo</h3>
                            <h6 class="text-muted mb-3">Woodcarver</h6>
                            <p class="card-text">Saw Htoo creates exquisite wooden crafts using traditional Karen woodworking techniques. His carvings often depict animals and symbols significant to Karen folklore.</p>
                        </div>
                        <div class="card-footer bg-transparent border-0">
                            <a href="{{ route('products.index') }}?artisan=saw-htoo" class="btn btn-sm btn-outline-primary">View Products</a>
                        </div>
                    </div>
                </div>
                
                <!-- Artisan 2 -->
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <img src="https://via.placeholder.com/400x400" class="card-img-top" alt="Naw Wah">
                        <div class="card-body">
                            <h3 class="card-title h5">Naw Wah</h3>
                            <h6 class="text-muted mb-3">Backstrap Weaver</h6>
                            <p class="card-text">Specializing in backstrap loom weaving, Naw Wah creates intricate textiles with detailed patterns that represent Karen history. Her scarves are known for their fine detail and vibrant colors.</p>
                        </div>
                        <div class="card-footer bg-transparent border-0">
                            <a href="{{ route('products.index') }}?artisan=naw-wah" class="btn btn-sm btn-outline-primary">View Products</a>
                        </div>
                    </div>
                </div>
                
                <!-- Artisan 3 -->
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <img src="https://via.placeholder.com/400x400" class="card-img-top" alt="Saw Hser">
                        <div class="card-body">
                            <h3 class="card-title h5">Saw Hser</h3>
                            <h6 class="text-muted mb-3">Basketry Artist</h6>
                            <p class="card-text">Saw Hser creates functional and decorative baskets using traditional bamboo weaving techniques. His work combines practical utility with artistic expression, reflecting Karen's close relationship with nature.</p>
                        </div>
                        <div class="card-footer bg-transparent border-0">
                            <a href="{{ route('products.index') }}?artisan=saw-hser" class="btn btn-sm btn-outline-primary">View Products</a>
                        </div>
                    </div>
                </div>
                
                <!-- Artisan 4 -->
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <img src="https://via.placeholder.com/400x400" class="card-img-top" alt="Naw Khu">
                        <div class="card-body">
                            <h3 class="card-title h5">Naw Khu</h3>
                            <h6 class="text-muted mb-3">Embroidery Specialist</h6>
                            <p class="card-text">Naw Khu's detailed embroidery work adorns traditional Karen clothing and accessories. Her needle skills bring traditional symbols and stories to life through vibrant threads and meticulous stitching.</p>
                        </div>
                        <div class="card-footer bg-transparent border-0">
                            <a href="{{ route('products.index') }}?artisan=naw-khu" class="btn btn-sm btn-outline-primary">View Products</a>
                        </div>
                    </div>
                </div>
                
                <!-- Artisan 5 -->
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <img src="https://via.placeholder.com/400x400" class="card-img-top" alt="Saw Pleh">
                        <div class="card-body">
                            <h3 class="card-title h5">Saw Pleh</h3>
                            <h6 class="text-muted mb-3">Musical Instrument Maker</h6>
                            <p class="card-text">Saw Pleh crafts traditional Karen musical instruments including the tennaku (harp) and klo (drum). Each piece is not only functional but also a work of art that preserves Karen musical heritage.</p>
                        </div>
                        <div class="card-footer bg-transparent border-0">
                            <a href="{{ route('products.index') }}?artisan=saw-pleh" class="btn btn-sm btn-outline-primary">View Products</a>
                        </div>
                    </div>
                </div>
                
                <!-- Artisan 6 -->
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <img src="https://via.placeholder.com/400x400" class="card-img-top" alt="Naw Day">
                        <div class="card-body">
                            <h3 class="card-title h5">Naw Day</h3>
                            <h6 class="text-muted mb-3">Natural Dye Specialist</h6>
                            <p class="card-text">Naw Day creates natural dyes using traditional methods passed down through generations. Her knowledge of local plants and their properties produces vibrant, eco-friendly colors for Karen textiles.</p>
                        </div>
                        <div class="card-footer bg-transparent border-0">
                            <a href="{{ route('products.index') }}?artisan=naw-day" class="btn btn-sm btn-outline-primary">View Products</a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Community Impact Section -->
            <div class="card bg-light mb-5">
                <div class="card-body p-4">
                    <h2 class="card-title mb-4">Community Impact</h2>
                    <p>When you purchase from Karen Culture Sales, you're directly supporting these artisans and their communities. Your support helps:</p>
                    
                    <div class="row mt-4">
                        <div class="col-md-4 mb-3">
                            <div class="d-flex">
                                <div class="me-3">
                                    <i class="fas fa-graduation-cap fa-2x text-primary"></i>
                                </div>
                                <div>
                                    <h5>Education</h5>
                                    <p class="mb-0">Fund educational opportunities for artisans' children and community schools</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="d-flex">
                                <div class="me-3">
                                    <i class="fas fa-hands fa-2x text-primary"></i>
                                </div>
                                <div>
                                    <h5>Cultural Preservation</h5>
                                    <p class="mb-0">Support workshops that pass traditional skills to younger generations</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="d-flex">
                                <div class="me-3">
                                    <i class="fas fa-home fa-2x text-primary"></i>
                                </div>
                                <div>
                                    <h5>Economic Stability</h5>
                                    <p class="mb-0">Provide sustainable income for families and strengthen local economies</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- CTA Section -->
            <div class="text-center">
                <h2 class="mb-3">Experience Karen Craftsmanship</h2>
                <p class="mb-4">Discover authentic handcrafted products made with care and cultural significance.</p>
                <a href="{{ route('products.index') }}" class="btn btn-primary btn-lg">Shop Our Collection</a>
            </div>
        </div>
    </div>
</div>
@endsection 