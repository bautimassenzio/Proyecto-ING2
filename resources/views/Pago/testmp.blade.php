<!DOCTYPE html>
<html>
<head>
    <title>Test MercadoPago</title>
    <script src="https://sdk.mercadopago.com/js/v2"></script>
</head>
<body>
    <h1>Test Pago MercadoPago</h1>

    <div id="wallet_container"></div>

    <script>
        console.log("Clave pública:", "{{ $publicKey }}");
        console.log("Preference ID:", "{{ $preference->id }}");

        const mp = new MercadoPago("{{ $publicKey }}", {
            locale: 'es-AR'
        });

        mp.checkout({
            preference: {
                id: "{{ $preference->id }}"
            },
            render: {
                container: '#wallet_container',
                label: 'Pagar con Mercado Pago'
            }
        });
    </script>
</body>
</html>