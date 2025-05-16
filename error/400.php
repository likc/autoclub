<?php
/**
 * Página de Erro 400 - Requisição Inválida
 * AutoClub
 */
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erro 400 - Requisição Inválida | AutoClub</title>
    
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
            --warning: #f39c12;
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
            color: var(--warning);
            text-shadow: 0 5px 20px rgba(243, 156, 18, 0.3);
            margin-bottom: 10px;
            animation: shake 2s ease-in-out infinite;
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
            color: var(--warning);
            margin-bottom: 30px;
            animation: warning-pulse 2s ease-in-out infinite;
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
            border: 2px solid var(--warning);
        }
        
        .btn-outline:hover {
            background: var(--warning);
            color: var(--light);
            transform: translateY(-3px);
        }
        
        /* Efeito de formulário quebrado */
        .broken-form {
            background: rgba(243, 156, 18, 0.1);
            border: 2px dashed var(--warning);
            padding: 30px;
            margin: 30px auto;
            border-radius: 10px;
            max-width: 400px;
            position: relative;
            transform: rotate(-2deg);
        }
        
        .form-field {
            background: rgba(0, 0, 0, 0.3);
            height: 40px;
            margin: 10px 0;
            border-radius: 5px;
            position: relative;
            overflow: hidden;
        }
        
        .form-field::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 0%;
            height: 100%;
            background: rgba(243, 156, 18, 0.3);
            animation: fill-error 3s ease-in-out infinite;
        }
        
        .form-button {
            background: var(--warning);
            color: var(--light);
            width: 100%;
            height: 40px;
            border-radius: 5px;
            margin-top: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            opacity: 0.6;
            cursor: not-allowed;
            position: relative;
        }
        
        .form-button::after {
            content: '!';
            position: absolute;
            right: 15px;
            font-size: 20px;
            animation: blink 1s ease-in-out infinite;
        }
        
        .error-notice {
            position: absolute;
            top: -10px;
            right: -10px;
            background: var(--warning);
            color: var(--dark);
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            animation: pulse 2s ease-in-out infinite;
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
        
        @keyframes shake {
            0%, 100% {
                transform: translateX(0);
            }
            25% {
                transform: translateX(-5px);
            }
            75% {
                transform: translateX(5px);
            }
        }
        
        @keyframes warning-pulse {
            0%, 100% {
                transform: scale(1);
                opacity: 1;
            }
            50% {
                transform: scale(1.1);
                opacity: 0.8;
            }
        }
        
        @keyframes fill-error {
            0% {
                width: 0%;
            }
            50% {
                width: 100%;
            }
            100% {
                width: 0%;
            }
        }
        
        @keyframes blink {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0;
            }
        }
        
        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.2);
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
            
            .broken-form {
                transform: rotate(-1deg);
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">
            <i class="fas fa-exclamation-circle"></i>
        </div>
        <div class="error-code">400</div>
        <h1 class="error-title">Requisição Inválida</h1>
        <p class="error-message">
            Hmm, parece que algo não está certo com sua solicitação. 
            Por favor, verifique as informações e tente novamente.
        </p>
        
        <div class="broken-form">
            <div class="error-notice">!</div>
            <div class="form-field"></div>
            <div class="form-field"></div>
            <div class="form-field"></div>
            <div class="form-button">
                <span>Enviar</span>
            </div>
        </div>
        
        <div class="error-actions">
            <a href="/" class="btn btn-primary">
                <i class="fas fa-home"></i> Voltar ao Início
            </a>
            <a href="javascript:history.back()" class="btn btn-outline">
                <i class="fas fa-undo"></i> Voltar
            </a>
        </div>
    </div>
</body>
</html>