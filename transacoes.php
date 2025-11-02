<?php
require_once 'config.php';
verificarLogin();
$usuario = getUsuarioLogado();

$filtro = $_GET['filtro'] ?? 'todas';
$busca = $_GET['busca'] ?? '';

$conn = getConnection();

// Construir query com filtros
$query = "SELECT * FROM transacoes WHERE usuario_id = ?";
$params = [$_SESSION['usuario_id']];
$types = "i";

if ($filtro === 'receitas') {
    $query .= " AND tipo = 'Receita'";
} elseif ($filtro === 'despesas') {
    $query .= " AND tipo = 'Despesa'";
}

if (!empty($busca)) {
    $query .= " AND (descricao LIKE ? OR categoria LIKE ?)";
    $busca_param = "%$busca%";
    $params[] = $busca_param;
    $params[] = $busca_param;
    $types .= "ss";
}

$query .= " ORDER BY data DESC, id DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$transacoes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transações - FinDash Pro</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="main-content">
        <?php include 'includes/header.php'; ?>
        
        <div class="content">
            <div class="page-header">
                <h1 class="page-title">Transações</h1>
                <div class="filter-buttons">
                    <a href="?filtro=todas" class="filter-btn <?php echo $filtro === 'todas' ? 'active' : ''; ?>">Todas</a>
                    <a href="?filtro=receitas" class="filter-btn <?php echo $filtro === 'receitas' ? 'active' : ''; ?>">Receitas</a>
                    <a href="?filtro=despesas" class="filter-btn <?php echo $filtro === 'despesas' ? 'active' : ''; ?>">Despesas</a>
                </div>
            </div>
            
            <div class="card">
                <div class="table-actions">
                    <button class="btn btn-primary" onclick="abrirModalTransacao()">+ Nova Transação</button>
                </div>
                
                <table class="transactions-table">
                    <thead>
                        <tr>
                            <th>Descrição</th>
                            <th>Categoria</th>
                            <th>Data</th>
                            <th>Tipo</th>
                            <th>Valor</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($transacoes as $transacao): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($transacao['descricao']); ?></td>
                            <td><span class="categoria-badge"><?php echo htmlspecialchars($transacao['categoria']); ?></span></td>
                            <td><?php echo date('M d', strtotime($transacao['data'])); ?></td>
                            <td>
                                <span class="tipo-badge tipo-<?php echo strtolower($transacao['tipo']); ?>">
                                    <?php echo $transacao['tipo']; ?>
                                </span>
                            </td>
                            <td class="valor-<?php echo strtolower($transacao['tipo']); ?>">
                                <?php echo $transacao['tipo'] === 'Despesa' ? '- ' : '+ '; ?>
                                R$ <?php echo number_format($transacao['valor'], 2, ',', '.'); ?>
                            </td>
                            <td>
                                <button class="btn-icon" onclick="editarTransacao(<?php echo $transacao['id']; ?>)">✏️</button>
                                <button class="btn-icon" onclick="excluirTransacao(<?php echo $transacao['id']; ?>)">🗑️</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Modal Nova Transação -->
    <div id="modalTransacao" class="modal">
        <div class="modal-content">
            <span class="close" onclick="fecharModalTransacao()">&times;</span>
            <h2 id="modalTitulo">Nova Transação</h2>
            <form id="formTransacao">
                <input type="hidden" id="transacao_id" name="id">
                
                <div class="form-group">
                    <label>Descrição</label>
                    <input type="text" id="descricao" name="descricao" required>
                </div>
                
                <div class="form-group">
                    <label>Categoria</label>
                    <input type="text" id="categoria" name="categoria" required>
                </div>
                
                <div class="form-group">
                    <label>Data</label>
                    <input type="date" id="data" name="data" required>
                </div>
                
                <div class="form-group">
                    <label>Tipo</label>
                    <select id="tipo" name="tipo" required>
                        <option value="Receita">Receita</option>
                        <option value="Despesa">Despesa</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Valor</label>
                    <input type="number" id="valor" name="valor" step="0.01" required>
                </div>
                
                <button type="submit" class="btn btn-primary">Salvar</button>
            </form>
        </div>
    </div>
    
    <script src="js/script.js"></script>
    <script src="js/transacoes.js"></script>
</body>
</html>
