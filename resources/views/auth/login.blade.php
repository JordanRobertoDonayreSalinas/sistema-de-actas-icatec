<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido - Sistema</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
    
    <style>
        body { 
            font-family: 'Poppins', sans-serif;
            /* Fondo azul suave idéntico a tu referencia */
            background-color: #dbeafe; 
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }

        /* --- TARJETA PRINCIPAL (SPLIT LAYOUT) --- */
        .main-card {
            background: white;
            width: 100%;
            max-width: 900px; /* Ancho máximo amplio para el estilo horizontal */
            height: auto;
            min-height: 550px; /* Altura mínima para que no se vea aplastado */
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
            display: flex; /* Magia: pone los elementos uno al lado del otro */
            overflow: hidden;
            position: relative;
        }

        /* --- COLUMNA IZQUIERDA (ANIMACIÓN) --- */
        .left-side {
            width: 50%;
            background-color: #f8fafc; /* Un gris muy sutil o blanco */
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
            position: relative;
        }

        /* Decoración de fondo sutil en el lado izquierdo (Opcional) */
        .left-side::before {
            content: '';
            position: absolute;
            width: 300px;
            height: 300px;
            background: #eff6ff;
            border-radius: 50%;
            z-index: 0;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .lottie-wrapper {
            width: 100%;
            max-width: 350px;
            height: auto;
            position: relative;
            z-index: 10;
        }

        /* --- COLUMNA DERECHA (FORMULARIO) --- */
        .right-side {
            width: 50%;
            padding: 40px 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: white;
        }

        /* RESPONSIVE: En móviles se pone uno debajo del otro */
        @media (max-width: 768px) {
            .main-card {
                flex-direction: column;
                max-width: 450px;
            }
            .left-side, .right-side {
                width: 100%;
                height: auto;
            }
            .left-side {
                padding: 30px;
                min-height: 250px;
            }
            .lottie-wrapper {
                max-width: 200px;
            }
        }

        /* --- ESTILOS DEL FORMULARIO --- */
        .title {
            font-size: 28px;
            font-weight: 700;
            color: #1e40af; /* Azul fuerte como la referencia */
            margin-bottom: 5px;
            text-align: center;
        }

        .subtitle {
            font-size: 14px;
            color: #64748b;
            text-align: center;
            margin-bottom: 35px;
        }

        .input-group {
            margin-bottom: 20px;
            position: relative;
        }

        .custom-input {
            width: 100%;
            padding: 15px 20px 15px 45px; /* Espacio para icono */
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            font-size: 14px;
            outline: none;
            transition: all 0.3s;
            box-sizing: border-box;
            color: #334155;
        }

        .custom-input:focus {
            background: white;
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }

        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            width: 20px;
            height: 20px;
            color: #94a3b8;
            pointer-events: none;
        }

        .btn-primary {
            width: 100%;
            padding: 16px;
            background: #2563eb; /* Azul brillante */
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 15px;
            cursor: pointer;
            transition: background 0.3s;
            box-shadow: 0 10px 20px -5px rgba(37, 99, 235, 0.4);
            margin-top: 10px;
        }

        .btn-primary:hover {
            background: #1d4ed8;
        }
    </style>
</head>
<body>

    <div class="main-card">
        
        <div class="left-side">
            <div class="lottie-wrapper">
                <lottie-player 
                    src="{{ asset('assets/login.json') }}" 
                    background="transparent" 
                    speed="1" 
                    style="width: 100%; height: 100%;" 
                    loop 
                    autoplay>
                </lottie-player>
            </div>
        </div>

        <div class="right-side">
            
            <div class="text-center">
                <h1 class="title">Bienvenido</h1>
                <p class="subtitle">Ingresa tus credenciales para acceder al sistema</p>
            </div>

            <form action="{{ route('login') }}" method="POST">
                @csrf
                
                <div class="input-group">
                    <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <input type="text" 
                           name="username" 
                           required 
                           placeholder="Usuario" 
                           class="custom-input">
                    @error('username')
                        <span style="color: #ef4444; font-size: 12px; margin-left: 10px;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="input-group">
                    <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    <input type="password" 
                           name="password" 
                           required 
                           placeholder="Contraseña" 
                           class="custom-input">
                </div>

                <button type="submit" class="btn-primary">
                    Ingresar
                </button>

            </form>
        </div>
    </div>

</body>
</html>