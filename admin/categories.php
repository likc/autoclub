<?php
session_start();
require_once 'config.php';
check_login();

$conn = db_connect();

// Processar o formulário de adição quando enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $name = sanitize($_POST['name']);
    $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', sanitize($_POST['slug'])));
    
    // Validação básica
    $errors = [];
    
    if (empty($name)) {
        $errors[] = "O nome da categoria é obrigatório.";
    }
    
    if (empty($slug)) {
        $errors[] = "O slug da categoria é obrigatório.";
    }
    
    // Verificar se o slug já existe
    $stmt = $conn->prepare("SELECT id FROM car_categories WHERE slug = ?");
    $stmt->bind_param("s", $slug);
    $stmt->execute();
    
    if ($stmt->get_result()->num_rows > 0) {
        $errors[] = "Este slug já está em uso por outra categoria.";
    }
    
    // Se não houver erros, adicionar a categoria
    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO car_categories (name, slug) VALUES (?, ?)");
        $stmt->bind_param("ss", $name, $slug);
        
        if ($stmt->execute()) {
            set_alert('success', 'Categoria adicionada com sucesso!');
            redirect('categories.php');
        } else {
            set_alert('danger', 'Erro ao adicionar categoria: ' . $conn->error);
        }
    } else {
        $error_message = implode("<br>", $errors);
        set_alert('danger', $error_message);
    }
}

// Processar o formulário de edição quando enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit') {
    $category_id = (int)$_POST['category_id'];
    $name = sanitize($_POST['name']);
    $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', sanitize($_POST['slug'])));
    
    // Validação básica
    $errors = [];
    
    if (empty($name)) {
        $errors[] = "O nome da categoria é obrigatório.";
    }
    
    if (empty($slug)) {
        $errors[] = "O slug da categoria é obrigatório.";
    }
    
    // Verificar se o slug já existe (exceto para a categoria atual)
    $stmt = $conn->prepare("SELECT id FROM car_categories WHERE slug = ? AND id != ?");
    $stmt->bind_param("si", $slug, $category_id);
    $stmt->execute();
    
    if ($stmt->get_result()->num_rows > 0) {
        $errors[] = "Este slug já está em uso por outra categoria.";
    }
    
    // Se não houver erros, atualizar a categoria
    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE car_categories SET name = ?, slug = ? WHERE id = ?");
        $stmt->bind_param("ssi", $name, $slug, $category_id);
        
        if ($stmt->execute()) {
            set_alert('success', 'Categoria atualizada com sucesso!');
            redirect('categories.php');
        } else {
            set_alert('danger', 'Erro ao atualizar categoria: ' . $conn->error);
        }
    } else {
        $error_message = implode("<br>", $errors);
        set_alert('danger', $error_message);
    }
}

// Processar a exclusão de categoria
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $category_id = (int)$_GET['id'];
    
    // Verificar se é a categoria "Todos"
    $stmt = $conn->prepare("SELECT slug FROM car_categories WHERE id = ?");
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        set_alert('danger', 'Categoria não encontrada!');
        redirect('categories.php');
    }
    
    $category = $result->fetch_assoc();
    
    if ($category['slug'] === 'all') {
        set_alert('danger', 'A categoria "Todos" não pode ser excluída!');
        redirect('categories.php');
    }
    
    // Verificar se há carros usando esta categoria
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM car_category_relations WHERE category_id = ?");
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    
    if ($result['count'] > 0) {
        set_alert('danger', "Esta categoria está sendo usada por {$result['count']} veículos e não pode ser excluída!");
        redirect('categories.php');
    }
    
    // Excluir a categoria
    $stmt = $conn->prepare("DELETE FROM car_categories WHERE id = ?");
    $stmt->bind_param("i", $category_id);
    
    if ($stmt->execute()) {
        set_alert('success', 'Categoria excluída com sucesso!');
    } else {
        set_alert('danger', 'Erro ao excluir categoria: ' . $conn->error);
    }
    
    redirect('categories.php');
}

// Obter todas as categorias
$categories_query = "SELECT * FROM car_categories ORDER BY name ASC";
$categories = $conn->query($categories_query)->fetch_all(MYSQLI_ASSOC);

// Obter contagem de carros por categoria
foreach ($categories as &$category) {
    $stmt = $conn->prepare("
        SELECT COUNT(*) as count FROM car_category_relations 
        WHERE category_id = ?
    ");
    $stmt->bind_param("i", $category['id']);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    
    $category['car_count'] = $result['count'];
}

$conn->close();
?>

<?php include 'includes/header.php'; ?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Categorias</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                    <li class="breadcrumb-item active">Categorias</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Lista de Categorias</h3>
                        
                        <div class="card-tools">
                            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addCategoryModal">
                                <i class="fas fa-plus"></i> Adicionar Nova
                            </button>
                        </div>
                    </div>
                    
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-nowrap">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nome</th>
                                    <th>Slug</th>
                                    <th>Veículos</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($categories as $category): ?>
                                    <tr>
                                        <td><?php echo $category['id']; ?></td>
                                        <td><?php echo htmlspecialchars($category['name']); ?></td>
                                        <td><code><?php echo htmlspecialchars($category['slug']); ?></code></td>
                                        <td>
                                            <?php if ($category['car_count'] > 0): ?>
                                                <a href="cars.php?category=<?php echo urlencode($category['slug']); ?>">
                                                    <?php echo $category['car_count']; ?> veículo(s)
                                                </a>
                                            <?php else: ?>
                                                0 veículos
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-info btn-sm edit-category-btn" 
                                                    data-toggle="modal" 
                                                    data-target="#editCategoryModal"
                                                    data-id="<?php echo $category['id']; ?>"
                                                    data-name="<?php echo htmlspecialchars($category['name']); ?>"
                                                    data-slug="<?php echo htmlspecialchars($category['slug']); ?>">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            
                                            <?php if ($category['slug'] !== 'all'): ?>
                                                <button type="button" class="btn btn-danger btn-sm" 
                                                        data-toggle="modal" 
                                                        data-target="#deleteCategoryModal<?php echo $category['id']; ?>">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                                
                                                <!-- Modal de Confirmação de Exclusão -->
                                                <div class="modal fade" id="deleteCategoryModal<?php echo $category['id']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Confirmar Exclusão</h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>Tem certeza que deseja excluir a categoria <strong><?php echo htmlspecialchars($category['name']); ?></strong>?</p>
                                                                
                                                                <?php if ($category['car_count'] > 0): ?>
                                                                    <div class="alert alert-warning">
                                                                        <i class="icon fas fa-exclamation-triangle"></i>
                                                                        Esta categoria está sendo usada por <?php echo $category['car_count']; ?> veículo(s) e não pode ser excluída!
                                                                    </div>
                                                                <?php else: ?>
                                                                    <p class="text-danger">Esta ação não pode ser desfeita!</p>
                                                                <?php endif; ?>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                                                <?php if ($category['car_count'] === 0): ?>
                                                                    <a href="categories.php?action=delete&id=<?php echo $category['id']; ?>" class="btn btn-danger">Excluir</a>
                                                                <?php else: ?>
                                                                    <button type="button" class="btn btn-danger" disabled>Excluir</button>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                
                                <?php if (empty($categories)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center">Nenhuma categoria encontrada.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal Adicionar Categoria -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" role="dialog" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCategoryModalLabel">Adicionar Nova Categoria</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="" method="POST">
                <input type="hidden" name="action" value="add">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="add_name">Nome da Categoria</label>
                        <input type="text" class="form-control" id="add_name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="add_slug">Slug</label>
                        <input type="text" class="form-control" id="add_slug" name="slug" required>
                        <small class="form-text text-muted">O slug é usado para identificar a categoria na URL. Use apenas letras, números e hífens.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Adicionar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar Categoria -->
<div class="modal fade" id="editCategoryModal" tabindex="-1" role="dialog" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editCategoryModalLabel">Editar Categoria</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="" method="POST">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="category_id" id="edit_category_id">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_name">Nome da Categoria</label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_slug">Slug</label>
                        <input type="text" class="form-control" id="edit_slug" name="slug" required>
                        <small class="form-text text-muted">O slug é usado para identificar a categoria na URL. Use apenas letras, números e hífens.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-gerar slug quando o nome for digitado (para adição)
        const addNameInput = document.getElementById('add_name');
        const addSlugInput = document.getElementById('add_slug');
        
        addNameInput.addEventListener('input', function() {
            addSlugInput.value = generateSlug(this.value);
        });
        
        // Auto-gerar slug quando o nome for digitado (para edição)
        const editNameInput = document.getElementById('edit_name');
        const editSlugInput = document.getElementById('edit_slug');
        
        editNameInput.addEventListener('input', function() {
            // Só atualiza o slug se não tiver sido manualmente editado
            if (!editSlugInput.dataset.edited) {
                editSlugInput.value = generateSlug(this.value);
            }
        });
        
        editSlugInput.addEventListener('input', function() {
            // Marcar como editado manualmente
            this.dataset.edited = true;
        });
        
        // Função para popular o modal de edição
        const editBtns = document.querySelectorAll('.edit-category-btn');
        
        editBtns.forEach(function(btn) {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                const name = this.dataset.name;
                const slug = this.dataset.slug;
                
                document.getElementById('edit_category_id').value = id;
                document.getElementById('edit_name').value = name;
                document.getElementById('edit_slug').value = slug;
                
                // Resetar o estado de edição manual
                editSlugInput.dataset.edited = false;
            });
        });
        
        // Função para gerar slug
        function generateSlug(text) {
            return text
                .toString()
                .normalize('NFD')                // Normalizar acentos
                .replace(/[\u0300-\u036f]/g, '') // Remover acentos
                .toLowerCase()
                .trim()
                .replace(/\s+/g, '-')           // Espaços para hífens
                .replace(/[^\w\-]+/g, '')       // Remover caracteres não-alfanuméricos
                .replace(/\-\-+/g, '-');        // Remover múltiplos hífens
        }
    });
</script>

<?php include 'includes/footer.php'; ?>

<!-- index.php (redirecionamento) -->
<?php
session_start();
require_once 'config.php';

// Redirecionar para dashboard se estiver logado, senão para login
if (is_logged_in()) {
    redirect('dashboard.php');
} else {
    redirect('login.php');
}
?>
