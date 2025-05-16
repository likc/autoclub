<?php
/**
 * Página de Erro 500 - Erro Interno do Servidor
 * AutoClub
 */
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erro 500 - Erro Interno do Servidor | AutoClub</title>
    
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
            --error: #e74c3c;
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
            color: var(--error);
            text-shadow: 0 5px 20px rgba(231, 76, 60, 0.3);
            margin-bottom: 10px;
            animation: glitch 2s ease-in-out infinite;
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
            color: var(--error);
            margin-bottom: 30px;
            animation: spin-icon 3s linear infinite;
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
            border: 2px solid var(--error);
        }
        
        .btn-outline:hover {
            background: var(--error);
            color: var(--light);
            transform: translateY(-3px);
        }
        
        /* Engrenagens animadas */
        .gears-container {
            position: absolute;
            width: 300px;
            height: 300px;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0.1;
        }
        
        .gear {
            position: absolute;
            border: 5px solid var(--error);
            border-radius: 50%;
        }
        
        .gear::before,
        .gear::after {
            content: '';
            position: absolute;
            background: var(--error);
        }
        
        .gear::before {
            width: 100%;
            height: 30%;
            top: 35%;
            left: 0;
        }
        
        .gear::after {
            width: 30%;
            height: 100%;
            top: 0;
            left: 35%;
        }
        
        .gear-1 {
            width: 100px;
            height: 100px;
            top: 50px;
            left: 50px;
            animation: rotate-gear 4s linear infinite;
        }
        
        .gear-2 {
            width: 70px;
            height: 70px;
            top: 120px;
            right: 60px;
            animation: rotate-gear 3s linear infinite reverse;
        }
        
        .gear-3 {
            width: 50px;
            height: 50px;
            bottom: 50px;
            left: 100px;
            animation: rotate-gear 2s linear infinite;
        }
        
        /* Efeito de código de erro */
        .error-details {
            background: rgba(231, 76, 60, 0.1);
            border: 1px solid rgba(231, 76, 60, 0.3);
            padding: 15px;
            margin: 20px 0;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            color: var(--error);
            text-align: left;
            overflow-x: auto;
        }
        
        .error-details code {
            display: block;
            white-space: pre-wrap;
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
        
        @keyframes glitch {
            0%, 100% {
                text-shadow: 0 5px 20px rgba(231, 76, 60, 0.3);
                transform: translateX(0);
            }
            20% {
                text-shadow: -2px 5px 20px rgba(231, 76, 60, 0.5);
                transform: translateX(2px);
            }
            40% {
                text-shadow: 2px 5px 20px rgba(231, 76, 60, 0.5);
                transform: translateX(-2px);
            }
            60% {
                text-shadow: 0 5px 20px rgba(231, 76, 60, 0.7);
                transform: translateX(0);
            }
        }
        
        @keyframes spin-icon {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }
        
        @keyframes rotate-gear {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }
        
        .tech-terms {
            display: inline-block;
            animation: blink 1s ease-in-out infinite;
        }
        
        @keyframes blink {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.5;
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
            
            .gears-container {
                width: 200px;
                height: 200px;
            }
            
            .gear-1 {
                width: 60px;
                height: 60px;
            }
            
            .gear-2 {
                width: 40px;
                height: 40px;
            }
            
            .gear-3 {
                width: 30px;
                height: 30px;
            }
        }
    </style>
</head>
<body>
    <div class="gears-container">
        <div class="gear gear-1"></div>
        <div class="gear gear-2"></div>
        <div class="gear gear-3"></div>
    </div>
    
    <div class="error-container">
        <div class="error-icon">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <div class="error-code">500</div>
        <h1 class="error-title">Erro Interno do Servidor</h1>
        <p class="error-message">
            Ops! Algo deu errado no nosso servidor. 
            Nossos <span class="tech-terms">mecânicos digitais</span> já foram notificados e estão trabalhando na solução.
        </p>
        
        <div class="error-details">
            <code>
Error: Internal Server Error
Status: 500
Time: <?php echo date('Y-m-d H:i:s'); ?>

Attempting to fix...
            </code>
        </div>
        
        <div class="error-actions">
            <a href="/" class="btn btn-primary">
                <i class="fas fa-home"></i> Voltar ao Início
            </a>
            <a href="https://wa.me/+818092815155?text=Olá,%20encontrei%20um%20erro%20500%20no%20site" class="btn btn-outline">
                <i class="fas fa-tools"></i> Reportar Problema
            </a>
        </div>
    </div>
</body>
</html>