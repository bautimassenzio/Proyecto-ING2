<!DOCTYPE html>
<html>
<head>
    <title>Preguntas Frecuentes</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f8;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
        }

        .faq-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            width: 80%;
            max-width: 960px;
        }

        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 30px;
        }

        .faq-item {
            margin-bottom: 25px;
            border-bottom: 1px solid #eee;
            padding-bottom: 20px;
        }

        .faq-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .question {
            color: #007bff;
            font-weight: bold;
            margin-bottom: 10px;
            cursor: pointer;
        }

        .answer {
            color: #555;
            line-height: 1.6;
            display: none; /* Inicialmente oculto */
        }

        .faq-item.active .answer {
            display: block;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const faqItems = document.querySelectorAll('.faq-item');
            faqItems.forEach(item => {
                const question = item.querySelector('.question');
                question.addEventListener('click', function() {
                    item.classList.toggle('active');
                });
            });
        });
    </script>
</head>
<body>
    <div class="faq-container">
        <h1>Preguntas Frecuentes</h1>

        <div class="faq-item">
            <h2 class="question">¿Cuál es su política de cancelación?</h2>
            <div class="answer">
                <p>Nuestra política de cancelación permite cancelaciones hasta 24 horas antes de la fecha de llegada para recibir un reembolso completo. Las cancelaciones posteriores no son reembolsables.</p>
            </div>
        </div>

        <div class="faq-item">
            <h2 class="question">¿A qué hora es el check-in y el check-out?</h2>
            <div class="answer">
                <p>El check-in es a partir de las 3:00 PM y el check-out es a las 12:00 PM.</p>
            </div>
        </div>

        <div class="faq-item">
            <h2 class="question">¿Se admiten mascotas?</h2>
            <div class="answer">
                <p>Lo sentimos, no se admiten mascotas en nuestras instalaciones.</p>
            </div>
        </div>

        </div>
</body>
</html>
>>>>>>> origin/bautimerge
