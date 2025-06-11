{{-- resources/views/faqs/index.blade.php --}}

@extends($layout) {{-- ¡Asegúrate de que 'layouts.base' sea el nombre correcto de tu layout! --}}

@section('title', 'Preguntas Frecuentes') {{-- Título específico para esta página --}}



@section('content')
<div class="row justify-content-center">
    <div class="col-md-10 col-lg-8"> {{-- Un poco más ancha para las FAQs --}}
        <h1 class="text-center mb-4">Preguntas Frecuentes</h1>

        {{-- Puedes usar la estructura de acordeón de Bootstrap para una mejor UX
             en lugar del JavaScript manual, pero adaptamos tu estructura actual
             para la compatibilidad. --}}

        <div class="faq-list"> {{-- Contenedor opcional para los ítems de FAQ --}}
            <div class="faq-item mb-4 pb-3 border-bottom"> {{-- Clases de Bootstrap para margen y borde --}}
                <h2 class="question text-primary mb-2" style="cursor: pointer;">¿Cuáles son los requisitos para alquilar maquinaria?</h2> {{-- Mantengo tu estilo y cursor --}}
                <div class="answer" style="display: none;"> {{-- Oculto por defecto, se maneja con JS --}}
                    <p class="text-secondary">Para alquilar nuestras máquinas, generalmente solicitamos:</p>
                </div>
            </div>

            <div class="faq-item mb-4 pb-3 border-bottom">
                <h2 class="question text-primary mb-2" style="cursor: pointer;">¿Qué sucede si la maquinaria se daña o sufre una avería durante el período de alquiler?</h2>
                <div class="answer" style="display: none;">
                    <p class="text-secondary">Es importante notificar cualquier daño o avería de inmediato. Nuestro contrato de alquiler especifica las responsabilidades.</p>
                </div>
            </div>

            <div class="faq-item mb-0 pb-0"> {{-- Último ítem sin borde inferior ni margen --}}
                <h2 class="question text-primary mb-2" style="cursor: pointer;">¿Existe un tiempo mínimo y máximo de alquiler? ¿Se pueden extender los plazos?</h2>
                <div class="answer" style="display: none;">
                    <p class="text-secondary">Sí, contamos con diferentes modalidades de alquiler para adaptarnos a tus necesidades.</p>
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