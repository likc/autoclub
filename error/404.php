<?php
/**
 * Página de Erro 404 - Não Encontrado
 * AutoClub
 */
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erro 404 - Página Não Encontrada | AutoClub</title>
    
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
            position: relative;
        }
        
        .error-code::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 3px;
            background: var(--primary);
            animation: width-animation 2s ease-in-out infinite;
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
            animation: bounce 2s ease-in-out infinite;
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
            font-size: 16;
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
        
        /* Carro animado */
        .car-animation {
            width: 100px;
            height: 40px;
            position: absolute;
            bottom: 100px;
            animation: drive 10s linear infinite;
        }
        
        .car-body {
            width: 60px;
            height: 20px;
            background: var(--primary);
            margin: 0 auto;
            position: relative;
            border-radius: 5px;
        }
        
        .car-top {
            width: 30px;
            height: 15px;
            background: var(--primary);
            position: absolute;
            top: -15px;
            left: 15px;
            border-radius: 5px 5px 0 0;
        }
        
        .wheel {
            width: 12px;
            height: 12px;
            background: var(--light);
            border-radius: 50%;
            position: absolute;
            bottom: -6px;
            animation: rotate 1s linear infinite;
        }
        
        .wheel-front {
            left: 10px;
        }
        
        .wheel-back {
            right: 10px;
        }
        
        .road {
            position: absolute;
            bottom: 80px;
            left: 0;
            right: 0;
            height: 2px;
            background: var(--light-dark);
            overflow: hidden;
        }
        
        .road-line {
            position: absolute;
            width: 50px;
            height: 2px;
            background: var(--light);
            animation: road-move 2s linear infinite;
        }
        
        .road-line:nth-child(2) {
            left: 100px;
            animation-delay: 0.5s;
        }
        
        .road-line:nth-child(3) {
            left: 200px;
            animation-delay: 1s;
        }
        
        .road-line:nth-child(4) {
            left: 300px;
            animation-delay: 1.5s;
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
        
        @keyframes bounce {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-20px);
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
        
        @keyframes width-animation {
            0%, 100% {
                width: 100px;
            }
            50% {
                width: 150px;
            }
        }
        
        @keyframes drive {
            from {
                left: -100px;
            }
            to {
                left: 100%;
            }
        }
        
        @keyframes road-move {
            from {
                transform: translateX(0);
            }
            to {
                transform: translateX(-100px);
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
    <div class="road">
        <div class="road-line"></div>
        <div class="road-line"></div>
        <div class="road-line"></div>
        <div class="road-line"></div>
    </div>
    
    <div class="car-animation">
        <div class="car-body">
            <div class="car-top"></div>
            <div class="wheel wheel-front"></div>
            <div class="wheel wheel-back"></div>
        </div>
    </div>
    
    <div class="error-container">
        <div class="error-icon">
            <i class="fas fa-map-marked-alt"></i>
        </div>
        <div class="error-code">404</div>
        <h1 class="error-title">Página Não Encontrada</h1>
        <p class="error-message">
            Oops! Parece que você pegou um caminho errado. 
            A página que você está procurando não existe ou foi movida.
        </p>
        <div class="error-actions">
            <a href="/" class="btn btn-primary">
                <i class="fas fa-home"></i> Voltar ao Início
            </a>
            <a href="/#carros" class="btn btn-outline">
                <i class="fas fa-car"></i> Ver Carros
            </a>
        </div>
    </div>
</body>
</html>