<?php
// test_images.php - Arquivo de teste para verificar problemas de imagem

// Configuração do banco de dados
$db_host = 'localhost';
$db_user = 'minec761_likc';
$db_pass = 'rw23xrd807ox';
$db_name = 'minec761_autoclub';

// Conectar ao banco de dados
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

// Informações do servidor
echo "<h1>Teste de Imagens - AutoClub</h1>";
echo "<h2>Informações do Sistema</h2>";
echo "<p><strong>Diretório atual:</strong> " . getcwd() . "</p>";
echo "<p><strong>Document Root:</strong> " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
echo "<p><strong>Script Path:</strong> " . __FILE__ . "</p>";

// Verificar diretórios
echo "<h2>Verificação de Diretórios</h2>";
$dirs_to_check = [
    'admin/',
    'admin/uploads/',
    'img/',
    'uploads/'
];

foreach ($dirs_to_check as $dir) {
    echo "<p><strong>$dir:</strong> ";
    if (file_exists($dir)) {
        echo "Existe - Permissões: " . substr(sprintf('%o', fileperms($dir)), -4);
        if (is_writable($dir)) {
            echo " (Escrita OK)";
        } else {
            echo " (Sem permissão de escrita)";
        }
    } else {
        echo "NÃO existe";
    }
    echo "</p>";
}

// Buscar carros no banco
echo "<h2>Carros no Banco de Dados</h2>";
$query = "SELECT id, model, image FROM cars ORDER BY id DESC LIMIT 10";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>ID</th><th>Modelo</th><th>Nome da Imagem</th><th>Caminho Completo</th><th>Arquivo Existe?</th><th>Preview</th></tr>";
    
    while ($car = $result->fetch_assoc()) {
        $image_path = "admin/uploads/" . $car['image'];
        $exists = file_exists($image_path) ? "SIM" : "NÃO";
        
        echo "<tr>";
        echo "<td>" . $car['id'] . "</td>";
        echo "<td>" . htmlspecialchars($car['model']) . "</td>";
        echo "<td>" . htmlspecialchars($car['image']) . "</td>";
        echo "<td>" . $image_path . "</td>";
        echo "<td style='color: " . ($exists == "SIM" ? "green" : "red") . "'><strong>" . $exists . "</strong></td>";
        echo "<td>";
        if ($exists == "SIM") {
            echo "<img src='" . $image_path . "' width='100' alt='Preview'>";
        } else {
            echo "Imagem não encontrada";
        }
        echo "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>Nenhum carro encontrado no banco de dados.</p>";
}

// Listar arquivos no diretório uploads
echo "<h2>Arquivos no Diretório admin/uploads/</h2>";
if (file_exists('admin/uploads/') && is_dir('admin/uploads/')) {
    $files = scandir('admin/uploads/');
    echo "<ul>";
    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            $full_path = 'admin/uploads/' . $file;
            echo "<li>$file - Tamanho: " . filesize($full_path) . " bytes - Permissões: " . substr(sprintf('%o', fileperms($full_path)), -4) . "</li>";
        }
    }
    echo "</ul>";
} else {
    echo "<p>Diretório admin/uploads/ não encontrado.</p>";
}

// Testar upload
echo "<h2>Teste de Upload</h2>";
echo "<form method='post' enctype='multipart/form-data'>";
echo "<input type='file' name='test_image' accept='image/*'>";
echo "<input type='submit' value='Testar Upload'>";
echo "</form>";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['test_image'])) {
    echo "<h3>Resultado do Upload:</h3>";
    echo "<pre>";
    print_r($_FILES['test_image']);
    echo "</pre>";
    
    if ($_FILES['test_image']['error'] === 0) {
        $upload_dir = 'admin/uploads/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $filename = 'teste_' . time() . '_' . $_FILES['test_image']['name'];
        $target_path = $upload_dir . $filename;
        
        if (move_uploaded_file($_FILES['test_image']['tmp_name'], $target_path)) {
            echo "<p style='color: green;'>Upload bem-sucedido! Arquivo salvo em: $target_path</p>";
            if (file_exists($target_path)) {
                echo "<p>Verificação: O arquivo EXISTE após o upload.</p>";
                echo "<img src='$target_path' width='200' alt='Teste'>";
            } else {
                echo "<p style='color: red;'>Verificação: O arquivo NÃO existe após o upload.</p>";
            }
        } else {
            echo "<p style='color: red;'>Erro ao fazer upload.</p>";
        }
    }
}

$conn->close();
?>

<style>
    body {
        font-family: Arial, sans-serif;
        margin: 20px;
        background-color: #f5f5f5;
    }
    h1, h2, h3 {
        color: #333;
    }
    table {
        background-color: white;
        border-collapse: collapse;
        margin: 20px 0;
    }
    th {
        background-color: #f0f0f0;
    }
    img {
        max-width: 100px;
        height: auto;
    }
    pre {
        background-color: #f0f0f0;
        padding: 10px;
        border-radius: 5px;
    }
</style>