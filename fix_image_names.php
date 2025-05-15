<?php
// fix_image_names_v2.php - Script aprimorado para corrigir nomes de imagens

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

echo "<h1>Correção de Nomes de Imagens - V2</h1>";

// Primeiro, vamos listar os arquivos existentes no diretório
$upload_dir = 'admin/uploads/';
$existing_files = array();

if (is_dir($upload_dir)) {
    $files = scandir($upload_dir);
    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            $existing_files[] = $file;
            // Criar um array com o nome base (sem extensão) como chave
            $name_without_ext = pathinfo($file, PATHINFO_FILENAME);
            $existing_files[$name_without_ext] = $file;
        }
    }
}

echo "<h2>Arquivos existentes no diretório uploads:</h2>";
echo "<pre>";
print_r($existing_files);
echo "</pre>";

// Obter todos os carros
$query = "SELECT id, model, image FROM cars WHERE image IS NOT NULL AND image != ''";
$result = $conn->query($query);

$fixed_count = 0;
$error_count = 0;

echo "<h2>Processando imagens...</h2>";
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>ID</th><th>Modelo</th><th>Nome no BD</th><th>Arquivo Encontrado</th><th>Ação</th></tr>";

while ($row = $result->fetch_assoc()) {
    $id = $row['id'];
    $model = $row['model'];
    $current_name = trim($row['image']); // Remover espaços extras
    
    echo "<tr>";
    echo "<td>$id</td>";
    echo "<td>" . htmlspecialchars($model) . "</td>";
    echo "<td>" . htmlspecialchars($current_name) . "</td>";
    
    $found = false;
    $correct_filename = '';
    
    // Se o nome já tem extensão e existe
    if (preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $current_name)) {
        if (in_array($current_name, $existing_files)) {
            echo "<td style='color: green;'>$current_name</td>";
            echo "<td>Já está correto</td>";
            $found = true;
        }
    }
    
    // Se não encontrou, tentar encontrar pelo nome base
    if (!$found) {
        // Remover possível extensão incompleta
        $base_name = preg_replace('/\.(jpg|jpeg|png|gif|webp)$/i', '', $current_name);
        
        // Procurar nos arquivos existentes
        if (isset($existing_files[$base_name])) {
            $correct_filename = $existing_files[$base_name];
            $found = true;
            echo "<td style='color: blue;'>$correct_filename</td>";
        } else {
            // Tentar match parcial
            foreach ($existing_files as $key => $file) {
                if (!is_numeric($key) && strpos($file, $base_name) !== false) {
                    $correct_filename = $file;
                    $found = true;
                    echo "<td style='color: orange;'>$correct_filename (match parcial)</td>";
                    break;
                }
            }
        }
    }
    
    if ($found && $correct_filename && $correct_filename != $current_name) {
        // Atualizar no banco de dados
        $update_query = "UPDATE cars SET image = ? WHERE id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("si", $correct_filename, $id);
        
        if ($stmt->execute()) {
            echo "<td style='color: green;'>ATUALIZADO</td>";
            $fixed_count++;
        } else {
            echo "<td style='color: red;'>ERRO ao atualizar</td>";
            $error_count++;
        }
    } elseif (!$found) {
        echo "<td style='color: red;'>NÃO ENCONTRADO</td>";
        echo "<td>-</td>";
        $error_count++;
    } else {
        echo "<td>-</td>";
    }
    
    echo "</tr>";
}

echo "</table>";

echo "<h2>Resumo</h2>";
echo "<p>Total processado: " . $result->num_rows . "</p>";
echo "<p>Corrigidos: $fixed_count</p>";
echo "<p>Erros/Não encontrados: $error_count</p>";

// Criar mapeamento para facilitar debug
echo "<h2>Mapeamento de Correções Sugeridas</h2>";
echo "<pre>";
$result->data_seek(0); // Resetar o ponteiro do resultado
while ($row = $result->fetch_assoc()) {
    $current_name = trim($row['image']);
    $base_name = preg_replace('/\.(jpg|jpeg|png|gif|webp)$/i', '', $current_name);
    
    foreach ($existing_files as $key => $file) {
        if (!is_numeric($key) && strpos($file, $base_name) !== false) {
            echo "ID {$row['id']}: {$current_name} => {$file}\n";
            break;
        }
    }
}
echo "</pre>";

$conn->close();
?>

<style>
    body {
        font-family: Arial, sans-serif;
        margin: 20px;
        background-color: #f5f5f5;
    }
    h1, h2 {
        color: #333;
    }
    table {
        border-collapse: collapse;
        margin: 20px 0;
        background: white;
    }
    th {
        background: #f0f0f0;
        font-weight: bold;
    }
    td, th {
        padding: 8px;
        border: 1px solid #ddd;
    }
    pre {
        background: #f0f0f0;
        padding: 10px;
        border-radius: 5px;
        overflow-x: auto;
    }
</style>