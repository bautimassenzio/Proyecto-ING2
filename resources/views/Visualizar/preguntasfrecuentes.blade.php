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

        {{-- Puedes usar la estructura de acordeón de Bootstrap para una mejor UX
             en lugar del JavaScript manual, pero adaptamos tu estructura actual
             para la compatibilidad. --}}

        <div class="faq-list"> {{-- Contenedor opcional para los ítems de FAQ --}}
            <div class="faq-item mb-4 pb-3 border-bottom"> {{-- Clases de Bootstrap para margen y borde --}}
                <h2 class="question text-primary mb-2" style="cursor: pointer;">¿Cuál es su política de cancelación?</h2> {{-- Mantengo tu estilo y cursor --}}
                <div class="answer" style="display: none;"> {{-- Oculto por defecto, se maneja con JS --}}
                    <p class="text-secondary">Nuestra política de cancelación permite cancelaciones hasta 24 horas antes de la fecha de llegada para recibir un reembolso completo. Las cancelaciones posteriores no son reembolsables.</p>
                </div>
            </div>

            <div class="faq-item mb-4 pb-3 border-bottom">
                <h2 class="question text-primary mb-2" style="cursor: pointer;">¿A qué hora es el check-in y el check-out?</h2>
                <div class="answer" style="display: none;">
                    <p class="text-secondary">El check-in es a partir de las 3:00 PM y el check-out es a las 12:00 PM.</p>
                </div>
            </div>

            <div class="faq-item mb-0 pb-0"> {{-- Último ítem sin borde inferior ni margen --}}
                <h2 class="question text-primary mb-2" style="cursor: pointer;">¿Se admiten mascotas?</h2>
                <div class="answer" style="display: none;">
                    <p class="text-secondary">Lo sentimos, no se admiten mascotas en nuestras instalaciones.</p>
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