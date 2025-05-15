<?php
// Script de diagnóstico para identificar problemas
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Diagnóstico do Sistema</h1>";
echo "<pre>";

// 1. Verificar se a configuração está carregando
echo "1. Verificando arquivo config.php:\n";
if (file_exists('config.php')) {
    echo "✓ config.php existe\n";
    
    // Tentar incluir o arquivo
    try {
        require_once 'config.php';
        echo "✓ config.php carregado com sucesso\n";
    } catch (Exception $e) {
        echo "✗ Erro ao carregar config.php: " . $e->getMessage() . "\n";
    }
} else {
    echo "✗ config.php não encontrado\n";
}

// 2. Verificar conexão com banco de dados
echo "\n2. Verificando conexão com banco de dados:\n";
try {
    $conn = db_connect();
    echo "✓ Conexão com banco de dados OK\n";
    
    // Testar query simples
    $result = $conn->query("SELECT COUNT(*) FROM cars");
    if ($result) {
        echo "✓ Query de teste executada com sucesso\n";
    } else {
        echo "✗ Erro na query de teste: " . $conn->error . "\n";
    }
    
    $conn->close();
} catch (Exception $e) {
    echo "✗ Erro na conexão: " . $e->getMessage() . "\n";
}

// 3. Verificar diretório de uploads
echo "\n3. Verificando diretório de uploads:\n";
$upload_dir = 'uploads/';
if (is_dir($upload_dir)) {
    echo "✓ Diretório uploads/ existe\n";
    
    // Verificar permissões
    if (is_writable($upload_dir)) {
        echo "✓ Diretório uploads/ tem permissão de escrita\n";
    } else {
        echo "✗ Diretório uploads/ não tem permissão de escrita\n";
    }
} else {
    echo "✗ Diretório uploads/ não existe\n";
    
    // Tentar criar
    if (mkdir($upload_dir, 0755, true)) {
        echo "✓ Diretório uploads/ criado com sucesso\n";
    } else {
        echo "✗ Não foi possível criar o diretório uploads/\n";
    }
}

// 4. Verificar as includes do header e footer
echo "\n4. Verificando includes:\n";
if (file_exists('includes/header.php')) {
    echo "✓ includes/header.php existe\n";
} else {
    echo "✗ includes/header.php não encontrado\n";
}

if (file_exists('includes/footer.php')) {
    echo "✓ includes/footer.php existe\n";
} else {
    echo "✗ includes/footer.php não encontrado\n";
}

// 5. Verificar erros de sintaxe nos arquivos principais
echo "\n5. Verificando sintaxe dos arquivos:\n";

$files_to_check = ['car_add.php', 'car_edit.php', 'config.php'];
foreach ($files_to_check as $file) {
    if (file_exists($file)) {
        $cmd = "php -l $file 2>&1";
        $output = shell_exec($cmd);
        if (strpos($output, 'No syntax errors') !== false) {
            echo "✓ $file - Sem erros de sintaxe\n";
        } else {
            echo "✗ $file - Erro de sintaxe: $output\n";
        }
    } else {
        echo "✗ $file não encontrado\n";
    }
}

// 6. Verificar PHP errors log
echo "\n6. Últimos erros do PHP:\n";
$error_log = ini_get('error_log');
if ($error_log && file_exists($error_log)) {
    $lines = array_slice(file($error_log), -10);
    foreach ($lines as $line) {
        echo $line;
    }
} else {
    echo "Arquivo de log de erros não encontrado ou não acessível\n";
}

// 7. Informações do PHP
echo "\n7. Informações do PHP:\n";
echo "Versão do PHP: " . phpversion() . "\n";
echo "Memory Limit: " . ini_get('memory_limit') . "\n";
echo "Max Execution Time: " . ini_get('max_execution_time') . "\n";
echo "Upload Max Filesize: " . ini_get('upload_max_filesize') . "\n";
echo "Post Max Size: " . ini_get('post_max_size') . "\n";

echo "</pre>";
?>

<style>
    body {
        font-family: Arial, sans-serif;
        margin: 20px;
        background-color: #f5f5f5;
    }
    pre {
        background: white;
        padding: 20px;
        border-radius: 5px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    h1 {
        color: #333;
    }
</style>