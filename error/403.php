<?php
/**
 * Página de Erro 403 - Acesso Proibido
 * AutoClub
 */
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erro 403 - Acesso Proibido | AutoClub</title>
    
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
            color: var(--primary);
            text-shadow: 0 5px 20px rgba(214, 156, 30, 0.3);
            margin-bottom: 10px;
            animation: pulse 2s ease-in-out infinite;
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
            color: var(--primary);
            margin-bottom: 30px;
            animation: shake 2s ease-in-out infinite;
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
            border: 2px solid var(--primary);
        }
        
        .btn-outline:hover {
            background: var(--primary);
            color: var(--light);
            transform: translateY(-3px);
        }
        
        .background-shapes {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 1;
        }
        
        .shape {
            position: absolute;
            opacity: 0.1;
        }
        
        .shape-1 {
            width: 300px;
            height: 300px;
            background: var(--primary);
            border-radius: 50%;
            top: -150px;
            left: -150px;
            animation: float 20s ease-in-out infinite;
        }
        
        .shape-2 {
            width: 200px;
            height: 200px;
            background: var(--primary);
            border-radius: 50%;
            bottom: -100px;
            right: -100px;
            animation: float 15s ease-in-out infinite reverse;
        }
        
        .shape-3 {
            width: 100px;
            height: 100px;
            background: var(--primary);
            transform: rotate(45deg);
            top: 50%;
            left: 20%;
            animation: rotate 25s linear infinite;
        }
        
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
        
        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
        }
        
        @keyframes shake {
            0%, 100% {
                transform: translateX(0);
            }
            25% {
                transform: translateX(-10px);
            }
            75% {
                transform: translateX(10px);
            }
        }
        
        @keyframes float {
            0%, 100% {
                transform: translateY(0) translateX(0);
            }
            25% {
                transform: translateY(-20px) translateX(10px);
            }
            50% {
                transform: translateY(0) translateX(20px);
            }
            75% {
                transform: translateY(20px) translateX(10px);
            }
        }
        
        @keyframes rotate {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
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
        }
    </style>
</head>
<body>
    <div class="background-shapes">
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>
        <div class="shape shape-3"></div>
    </div>
    
    <div class="error-container">
        <div class="error-icon">
            <i class="fas fa-ban"></i>
        </div>
        <div class="error-code">403</div>
        <h1 class="error-title">Acesso Proibido</h1>
        <p class="error-message">
            Desculpe, você não tem permissão para acessar este recurso. 
            Este conteúdo está protegido e requer autorização especial.
        </p>
        <div class="error-actions">
            <a href="/" class="btn btn-primary">
                <i class="fas fa-home"></i> Voltar ao Início
            </a>
            <a href="https://wa.me/+818092815155?text=Olá,%20encontrei%20um%20erro%20403%20no%20site" class="btn btn-outline">
                <i class="fas fa-headset"></i> Contatar Suporte
            </a>
        </div>
    </div>
</body>
</html>