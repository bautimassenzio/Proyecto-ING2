@extends('layouts.visitante')

@section('title', 'MannyMaquinarias - Soluciones en Maquinaria')



@section('content')
<div class="hero-section relative bg-cover bg-center text-white" 
     style="background-image: url('{{ asset('images/inicio.jpg') }}');">

    <div class="absolute inset-0 bg-black bg-opacity-50"></div>

    {{-- Ajustado pt-24 para mover más abajo --}}
    <div class="relative z-10 h-full flex items-start justify-start px-6 pt-24"> 
        <div class="max-w-xl space-y-4 text-left hero-text"> 
            {{-- Eliminadas las clases de Tailwind de font-size, line-height y font-weight para controlarlo en CSS --}}
            <h1>
                MannyMaquinarias:<br>
                {{-- Clase para el color amarillo --}}
                <span class="text-yellow-400">Impulsando tus proyectos</span><br>
                con la maquinaria ideal.
            </h1>
            {{-- Eliminadas las clases de Tailwind de font-size para controlarlo en CSS --}}
            <p>
                Alquiler de equipos industriales de alta calidad
            </p>
            <div class="flex gap-4 flex-wrap justify-start"> 
                <a href="{{ url('catalogo') }}" class="btn btn-primary">
                    <i class="fas fa-search me-1"></i> Explorar Catálogo
                </a>
                <a href="/register" class="btn btn-secondary">
                    <i class="fas fa-user-plus"></i> Regístrate
                </a>
            </div>
        </div>
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
            <p>Equipos de última generación</p>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i class="fas fa-tools"></i>
            </div>
            <h3>Soporte especializado</h3>
            <p>Empleados capacitados para brindarte la ayuda necesaria </p>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i class="fas fa-handshake"></i>
            </div>
            <h3>Precios Competitivos</h3>
            <p>Las mejores tarifas del mercado con politicas de reembolso claras</p>
        </div>
    </div>
</div>
@endsection

@section('additional-styles')
@parent
<style>
    :root {
        --light-yellow: #FEF3C7;
        --text-dark: #333333;
    }

    .hero-section {
        position: relative;
        background-size: cover;
        background-position: center;
        min-height: 600px;
        border-radius: 20px;
        overflow: hidden;
        margin-bottom: 4rem;
    }

    .hero-section .relative.z-10 {
        width: 100%;
        display: flex;
        justify-content: flex-start; 
        /* Aumentamos el padding-top para mover más abajo */
        padding-top: 15rem; /* Ajusta este valor para controlar la altura */
        padding-left: 2rem;
        padding-right: 2rem;
        height: 100%;
        align-items: flex-start; 
    }

    .hero-section .max-w-xl {
        width: 100%;
        max-width: 700px; 
        padding: 0;
        text-align: left;
    }

    /* Estilo del título principal */
    .hero-text h1 {
        /* Usamos 'Helvetica Neue', 'Arial Black' o 'Impact' para una fuente gruesa */
        font-family: 'Helvetica Neue', 'Arial Black', Arial, sans-serif; 
        font-weight: 800; /* Puedes probar 900 (Black) si la fuente lo soporta */
        /* Reducimos el tamaño de la fuente para el título */
        font-size: 2.8rem; /* Puedes ajustar este valor */
        line-height: 1.2;
        margin-bottom: 1.5rem;
        color: white; /* Asegúrate de que el color sea blanco para que contraste con el overlay */
        text-shadow: 4px 4px 6px rgba(0, 0, 0, 0.96); /* Sombra para mejor lectura */
    }

    .hero-text h1 span {
        color: var(--primary-yellow);
        position: relative;
    }

    /* Estilo del párrafo */
    .hero-text p {
        /* Reducimos el tamaño de la fuente para el párrafo */
        font-size: 1.1rem; /* Puedes ajustar este valor */
        color: white;
        margin-bottom: 2rem;
        text-shadow: 2px 2px 3px rgba(0, 0, 0, 0.97); /* Sombra para mejor lectura */
    }

    /* Estilo de los botones */
    

    .section-title {
        text-align: center;
        font-size: 2.2rem;
        font-weight: 700;
        margin-bottom: 3rem;
        color: var(--text-dark);
    }

    .features-section {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 2rem;
    }

    .features-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 2rem;
    }

    .feature-card {
        background: white;
        padding: 2rem;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        border: 1px solid #f0f0f0;
        text-align: center;
        transition: 0.3s ease;
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

    @media (max-width: 768px) {
        .hero-section {
            min-height: 500px;
        }

        .hero-section .relative.z-10 {
            padding-top: 10rem; /* Ajuste para móviles, puedes cambiarlo */
            padding-left: 1.5rem;
            padding-right: 1.5rem;
        }

        .hero-text h1 {
            font-size: 2.2rem; /* Tamaño de fuente más pequeño en móviles */
        }

        .hero-text p {
            font-size: 0.9rem; /* Tamaño de fuente más pequeño en móviles */
        }

        .btn {
            font-size: 0.9rem; /* Tamaño de fuente más pequeño en móviles para botones */
            padding: 0.6rem 1.2rem;
        }

        .features-section {
            padding: 0 1rem;
        }
    }
</style>
@endsection