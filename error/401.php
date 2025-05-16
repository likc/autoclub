<?php
/**
 * Página de Erro 401 - Não Autorizado
 * AutoClub
 */
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erro 401 - Não Autorizado | AutoClub</title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #d69c1e;
            --primary-light: #e6ae30;
            --dark: #151515;
            --dark-medium: #222222;
            --light: #ffffff;
            --light-dark: #d0d0d0;
            --info: #3498db;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--dark);
            color: var(--light);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        
        .error-container {
            text-align: center;
            z-index: 2;
            padding: 40px;
            max-width: 600px;
            animation: fadeInUp 0.8s ease forwards;
        }
        
        .error-code {
            font-size: 120px;
            font-weight: 800;
            color: var(--info);
            text-shadow: 0 5px 20px rgba(52, 152, 219, 0.3);
            margin-bottom: 10px;
        }
        
        .error-title {
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 20px;
            color: var(--light);
        }
        
        .error-message {
            font-size: 18px;
            color: var(--light-dark);
            margin-bottom: 40px;
            line-height: 1.6;
        }
        
        .error-icon {
            font-size: 80px;
            color: var(--info);
            margin-bottom: 30px;
            animation: lock-shake 3s ease-in-out infinite;
        }
        
        .error-actions {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .btn {
            display: inline-block;
            padding: 12px 28px;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s ease;
            text-decoration: none;
            font-size: 16px;
        }
        
        .btn-primary {
            background: var(--primary);
            color: var(--light);
        }
        
        .btn-primary:hover {
            background: var(--primary-light);
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(214, 156, 30, 0.3);
        }
        
        .btn-outline {
            background: transparent;
            color: var(--light);
            border: 2px solid var(--info);
        }
        
        .btn-outline:hover {
            background: var(--info);
            color: var(--light);
            transform: translateY(-3px);
        }
        
        /* Animação de cadeado */
        .lock-animation {
            width: 200px;
            height: 200px;
            margin: 30px auto;
            position: relative;
        }
        
        .lock-body {
            width: 100px;
            height: 80px;
            background: var(--info);
            margin: 0 auto;
            position: relative;
            top: 60px;
            border-radius: 10px;
            box-shadow: 0 10px 20px rgba(52, 152, 219, 0.3);
        }
        
        .lock-body::before {
            content: '';
            position: absolute;
            width: 30px;
            height: 40px;
            background: var(--dark);
            top: 20px;
            left: 35px;
            border-radius: 50% 50% 0 0;
        }
        
        .lock-shackle {
            width: 60px;
            height: 60px;
            border: 10px solid var(--info);
            border-bottom: none;
            border-radius: 50% 50% 0 0;
            position: absolute;
            top: 0;
            left: 70px;
            box-shadow: 0 -5px 10px rgba(52, 152, 219, 0.3);
            animation: shackle-shake 3s ease-in-out infinite;
        }
        
        /* Partículas de segurança */
        .security-particles {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
        }
        
        .particle {
            position: absolute;
            background: var(--info);
            width: 3px;
            height: 3px;
            border-radius: 50%;
            opacity: 0.5;
            animation: float-particle 10s linear infinite;
        }
        
        .particle:nth-child(1) { left: 10%; animation-delay: 0s; }
        .particle:nth-child(2) { left: 20%; animation-delay: 1s; }
        .particle:nth-child(3) { left: 30%; animation-delay: 2s; }
        .particle:nth-child(4) { left: 40%; animation-delay: 3s; }
        .particle:nth-child(5) { left: 50%; animation-delay: 4s; }
        .particle:nth-child(6) { left: 60%; animation-delay: 5s; }
        .particle:nth-child(7) { left: 70%; animation-delay: 6s; }
        .particle:nth-child(8) { left: 80%; animation-delay: 7s; }
        .particle:nth-child(9) { left: 90%; animation-delay: 8s; }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes lock-shake {
            0%, 100% {
                transform: translateX(0);
            }
            10%, 30%, 50%, 70%, 90% {
                transform: translateX(-2px);
            }
            20%, 40%, 60%, 80% {
                transform: translateX(2px);
            }
        }
        
        @keyframes shackle-shake {
            0%, 100% {
                transform: rotate(0deg);
            }
            25% {
                transform: rotate(-5deg);
            }
            75% {
                transform: rotate(5deg);
            }
        }
        
        @keyframes float-particle {
            from {
                transform: translateY(100vh) translateX(0);
                opacity: 0;
            }
            10% {
                opacity: 0.5;
            }
            90% {
                opacity: 0.5;
            }
            to {
                transform: translateY(-100vh) translateX(100px);
                opacity: 0;
            }
        }
        
        @media (max-width: 768px) {
            .error-code {
                font-size: 80px;
            }
            
            .error-title {
                font-size: 28px;
            }
            
            .error-message {
                font-size: 16px;
            }
            
            .error-icon {
                font-size: 60px;
            }
            
            .lock-animation {
                transform: scale(0.8);
            }
        }
    </style>
</head>
<body>
    <div class="security-particles">
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
    </div>
    
    <div class="error-container">
        <div class="error-icon">
            <i class="fas fa-lock"></i>
        </div>
        <div class="error-code">401</div>
        <h1 class="error-title">Não Autorizado</h1>
        
        <div class="lock-animation">
            <div class="lock-shackle"></div>
            <div class="lock-body"></div>
        </div>
        
        <p class="error-message">
            Você precisa estar autenticado para acessar esta página. 
            Por favor, faça login com suas credenciais.
        </p>
        
        <div class="error-actions">
            <a href="/" class="btn btn-primary">
                <i class="fas fa-home"></i> Voltar ao Início
            </a>
            <a href="/admin/login.php" class="btn btn-outline">
                <i class="fas fa-sign-in-alt"></i> Fazer Login
            </a>
        </div>
    </div>
</body>
</html>