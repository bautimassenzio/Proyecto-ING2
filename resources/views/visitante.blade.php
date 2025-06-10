@extends('layouts.visitante')

@section('title', 'MaquinaTech - Soluciones en Maquinaria')

@section('content')
<div class="hero-section">
    <div class="hero-content">
        <h1>Soluciones en <span>Maquinaria</span> para tus proyectos</h1>
        <p>Alquiler, venta y mantenimiento de equipos industriales de alta calidad</p>
        <div class="hero-buttons">
        <a class="nav-link {{ request()->is('catalogo') ? 'active' : '' }}" href="{{ url('catalogo') }}">
                <i class="fas fa-shopping-cart me-1"></i> Ver Catálogo
            </a>
            <a href="/register" class="btn btn-primary btn-lg">
                <i class="fas fa-user-plus"></i> Crear Cuenta
            </a>
        </div>
    </div>
    <div class="hero-image">
        <img src="/placeholder.svg?height=400&width=500" alt="Maquinaria industrial">
    </div>
</div>

<div class="features-section">
    <h2 class="section-title">¿Por qué elegirnos?</h2>
    
    <div class="features-grid">
        <div class="feature-card">
            <div class="feature-icon">
                <i class="fas fa-truck-monster"></i>
            </div>
            <h3>Maquinaria de Calidad</h3>
            <p>Equipos de última generación con mantenimiento garantizado</p>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon">
                <i class="fas fa-tools"></i>
            </div>
            <h3>Servicio Técnico</h3>
            <p>Soporte especializado y repuestos originales</p>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon">
                <i class="fas fa-handshake"></i>
            </div>
            <h3>Precios Competitivos</h3>
            <p>Las mejores tarifas del mercado con planes flexibles</p>
        </div>
    </div>
</div>

@endsection

@section('additional-styles')
@parent
<style>
    /* Container centrado para el contenido */
    .hero-section,
    .features-section,
    .cta-section {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 2rem;
    }

    /* Hero Section */
    .hero-section {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 3rem;
        align-items: center;
        margin-bottom: 4rem;
        padding: 2rem;
    }

    

    .hero-content h1 {
        font-size: 3rem;
        font-weight: 800;
        line-height: 1.2;
        margin-bottom: 1.5rem;
        color: var(--text-dark);
    }

    .hero-content h1 span {
        color: var(--primary-yellow);
        position: relative;
    }

    .hero-content h1 span::after {
        content: '';
        position: absolute;
        bottom: 5px;
        left: 0;
        width: 100%;
        height: 8px;
        background-color: var(--light-yellow);
        z-index: -1;
    }

    .hero-content p {
        font-size: 1.2rem;
        color: #666;
        margin-bottom: 2rem;
    }

    .hero-buttons {
        display: flex;
        gap: 1rem;
    }

    .btn-lg {
        padding: 1rem 2rem;
        font-size: 1.1rem;
    }

    .hero-image {
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .hero-image img {
        max-width: 100%;
        border-radius: 20px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    }

    /* Features Section */
    .features-section {
        margin-bottom: 4rem;
    }

    .section-title {
        text-align: center;
        font-size: 2.2rem;
        font-weight: 700;
        margin-bottom: 3rem;
        color: var(--text-dark);
    }

    .features-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 2rem;
        max-width: 1200px;
        margin: 0 auto;
    }

    .feature-card {
        background: white;
        padding: 2rem;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
        border: 1px solid #f0f0f0;
        text-align: center;
    }

    .feature-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 40px rgba(255, 184, 0, 0.15);
    }

    .feature-icon {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, var(--primary-yellow), var(--secondary-yellow));
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
    }

    .feature-icon i {
        font-size: 2rem;
        color: white;
    }

    .feature-card h3 {
        font-size: 1.3rem;
        font-weight: 700;
        margin-bottom: 1rem;
        color: var(--text-dark);
    }

    .feature-card p {
        color: #666;
        line-height: 1.6;
    }

    /* CTA Section */
    .cta-section {
        background: linear-gradient(135deg, var(--primary-yellow), var(--secondary-yellow));
        padding: 4rem 2rem;
        border-radius: 20px;
        text-align: center;
        color: white;
        margin-bottom: 2rem;
    }

    .cta-content h2 {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 1rem;
    }

    .cta-content p {
        font-size: 1.2rem;
        margin-bottom: 2rem;
        opacity: 0.9;
    }

    .cta-buttons {
        display: flex;
        justify-content: center;
        gap: 1rem;
    }

    .cta-section .btn-primary {
        background: white;
        color: var(--primary-yellow);
    }

    .cta-section .btn-primary:hover {
        background: var(--dark-bg);
        color: white;
    }

    .cta-section .btn-outline {
        border-color: white;
        color: white;
    }

    .cta-section .btn-outline:hover {
        background: white;
        color: var(--primary-yellow);
    }

    /* Responsive */
    @media (max-width: 992px) {
        .hero-section {
            grid-template-columns: 1fr;
            text-align: center;
            padding: 1rem;
        }

        .hero-buttons {
            justify-content: center;
        }

        .hero-image {
            order: -1;
        }

        .hero-content h1 {
            font-size: 2.5rem;
        }
    }

    @media (max-width: 768px) {
        .hero-content h1 {
            font-size: 2rem;
        }

        .hero-buttons {
            flex-direction: column;
        }

        .cta-buttons {
            flex-direction: column;
        }

        .section-title {
            font-size: 1.8rem;
        }

        .features-section {
            padding: 0 1rem;
        }

        .cta-section {
            margin: 0 1rem 2rem;
        }
    }
</style>
@endsection
