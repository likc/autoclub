<?php
// check_structure.php - Verificar estrutura de diretórios

echo "<h1>Verificação de Estrutura do Site</h1>";

// Função para listar diretórios recursivamente
function listDirectory($dir, $prefix = '') {
    $output = '';
    if (is_dir($dir)) {
        $files = scandir($dir);
        foreach ($files as $file) {
            if ($file != '.' && $file != '..') {
                $path = $dir . '/' . $file;
                $output .= $prefix . $file;
                if (is_dir($path)) {
                    $output .= '/ <span style="color: blue;">[DIR]</span>';
                    $output .= '<br>' . listDirectory($path, $prefix . '&nbsp;&nbsp;&nbsp;&nbsp;');
                } else {
                    $size = filesize($path);
                    $output .= ' <span style="color: green;">(' . number_format($size) . ' bytes)</span>';
                    if (strpos($file, '.jpg') !== false || strpos($file, '.png') !== false || strpos($file, '.gif') !== false) {
                        $output .= ' <span style="color: orange;">[IMAGEM]</span>';
                    }
                }
                $output .= '<br>';
            }
        }
    }
    return $output;
}

// Informações básicas
echo "<h2>Informações do Sistema</h2>";
echo "<p><strong>PHP Version:</strong> " . phpversion() . "</p>";
echo "<p><strong>Server Software:</strong> " . $_SERVER['SERVER_SOFTWARE'] . "</p>";
echo "<p><strong>Document Root:</strong> " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
echo "<p><strong>Script Path:</strong> " . __FILE__ . "</p>";
echo "<p><strong>Current Directory:</strong> " . getcwd() . "</p>";

// Estrutura de diretórios
echo "<h2>Estrutura de Diretórios Principal</h2>";
echo "<pre style='background: #f0f0f0; padding: 10px; font-family: monospace;'>";
echo listDirectory('.');
echo "</pre>";

// Verificar especificamente o diretório admin
echo "<h2>Estrutura do Diretório Admin</h2>";
if (is_dir('admin')) {
    echo "<pre style='background: #f0f0f0; padding: 10px; font-family: monospace;'>";
    echo listDirectory('admin');
    echo "</pre>";
} else {
    echo "<p style='color: red;'>Diretório 'admin' não encontrado!</p>";
}

// Teste de caminho relativo
echo "<h2>Teste de Caminhos Relativos</h2>";
$test_paths = [
    'admin/uploads/',
    './admin/uploads/',
    '../admin/uploads/',
    '/admin/uploads/',
    'uploads/',
    '../uploads/'
];

foreach ($test_paths as $path) {
    echo "<p><strong>$path:</strong> ";
    if (file_exists($path)) {
        echo "<span style='color: green;'>EXISTE</span>";
        if (is_dir($path)) {
            $files = scandir($path);
            $images = array_filter($files, function($file) {
                return preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $file);
            });
            echo " - " . count($images) . " imagens encontradas";
        }
    } else {
        echo "<span style='color: red;'>NÃO EXISTE</span>";
    }
    echo "</p>";
}

// Verificar banco de dados
echo "<h2>Verificação do Banco de Dados</h2>";
$conn = new mysqli('localhost', 'minec761_likc', 'rw23xrd807ox', 'minec761_autoclub');
if ($conn->connect_error) {
    echo "<p style='color: red;'>Erro de conexão: " . $conn->connect_error . "</p>";
} else {
    echo "<p style='color: green;'>Conexão com banco de dados OK</p>";
    
    $result = $conn->query("SELECT COUNT(*) as total FROM cars");
    $row = $result->fetch_assoc();
    echo "<p>Total de carros no banco: " . $row['total'] . "</p>";
    
    $result = $conn->query("SELECT id, model, image FROM cars WHERE image IS NOT NULL AND image != '' LIMIT 5");
    echo "<h3>Primeiros 5 carros com imagem:</h3>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Modelo</th><th>Nome da Imagem</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . htmlspecialchars($row['model']) . "</td>";
        echo "<td>" . htmlspecialchars($row['image']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    $conn->close();
}
?>

<style>
    body {
        font-family: Arial, sans-serif;
        margin: 20px;
        background-color: #f5f5f5;
    }
    pre {
        overflow-x: auto;
    }
    table {
        background: white;
    }
</style>