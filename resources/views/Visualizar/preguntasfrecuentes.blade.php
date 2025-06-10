preguntasfrecuentes.blade.php
{{-- resources/views/faqs/index.blade.php --}}

@extends('layouts.base') {{-- ¡Asegúrate de que 'layouts.base' sea el nombre correcto de tu layout! --}}

@section('title', 'Preguntas Frecuentes') {{-- Título específico para esta página --}}

{{-- Opcional: Define la navegación si la necesitas para esta vista --}}
@section('navigation')
    @auth('users')
        <li class="nav-item">
            <a class="nav-link" href="{{ route('catalogo.index') }}">Catálogo</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('reservas.index') }}">Mis Reservas</a>
        </li>
    @else
        <li class="nav-item">
            <a class="nav-link" href="{{ route('login') }}">Login</a>
        </li>
        @if (Route::has('register'))
            <li class="nav-item">
                <a class="nav-link" href="{{ route('register') }}">Registrarse</a>
            </li>
        @endif
    @endauth
    <li class="nav-item">
        <a class="nav-link active" aria-current="page" href= >Preguntas Frecuentes</a>
    </li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10 col-lg-8"> {{-- Un poco más ancha para las FAQs --}}
        <h1 class="text-center mb-4">Preguntas Frecuentes</h1>

        <div class="faq-list"> {{-- Contenedor opcional para los ítems de FAQ --}}
            <div class="faq-item mb-4 pb-3 border-bottom"> {{-- Clases de Bootstrap para margen y borde --}}
                <h2 class="question text-primary mb-2" style="cursor: pointer;">¿Cómo hago para alquilar una máquina?</h2>
                <div class="answer" style="display: none;">
                    <p class="text-secondary">Es fácil. Elegí la máquina en nuestro catálogo, seleccioná las fechas y confirmá la reserva.</p>
                </div>
            </div>

            <div class="faq-item mb-4 pb-3 border-bottom">
                <h2 class="question text-primary mb-2" style="cursor: pointer;">¿Necesito algún permiso especial?</h2>
                <div class="answer" style="display: none;">
                    <p class="text-secondary">Depende de la máquina. Algunas requieren licencias o experiencia previa. Te lo indicamos en la descripción.</p>
                </div>
            </div>

            <div class="faq-item mb-4 pb-3 border-bottom">
                <h2 class="question text-primary mb-2" style="cursor: pointer;">¿Puedo extender mi alquiler?</h2>
                <div class="answer" style="display: none;">
                    <p class="text-secondary">Sí, si la máquina no está reservada, podés extender el plazo. Contactanos con anticipación.</p>
                </div>
            </div>

            <div class="faq-item mb-4 pb-3 border-bottom">
                <h2 class="question text-primary mb-2" style="cursor: pointer;">¿Qué pasa si la máquina se rompe?</h2>
                <div class="answer" style="display: none;">
                    <p class="text-secondary">Avisanos de inmediato. Evaluaremos el daño y te daremos una solución rápida.</p>
                </div>
            </div>

            <div class="faq-item mb-0 pb-0"> {{-- Último ítem sin borde inferior ni margen --}}
                <h2 class="question text-primary mb-2" style="cursor: pointer;">¿Cómo pago el alquiler?</h2>
                <div class="answer" style="display: none;">
                    <p class="text-secondary">Aceptamos varios medios de pago. Lo verás al finalizar tu reserva.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('additional-scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const faqItems = document.querySelectorAll('.faq-item');
        faqItems.forEach(item => {
            const question = item.querySelector('.question');
            question.addEventListener('click', function() {
                // Alternar la clase 'active' para mostrar/ocultar la respuesta
                item.classList.toggle('active');

                // También puedes alternar la visibilidad directa del answer div si prefieres
                const answer = item.querySelector('.answer');
                if (answer.style.display === 'none') {
                    answer.style.display = 'block';
                } else {
                    answer.style.display = 'none';
                }
            });
        });
    });
</script>
@endsection