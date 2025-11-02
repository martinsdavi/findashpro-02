<?php
require_once 'config.php';
verificarLogin();
$usuario = getUsuarioLogado();

$conn = getConnection();
$stmt = $conn->prepare("SELECT * FROM objetivos WHERE usuario_id = ? ORDER BY id DESC");
$stmt->bind_param("i", $_SESSION['usuario_id']);
$stmt->execute();
$objetivos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Objetivos - FinDash Pro</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="main-content">
        <?php include 'includes/header.php'; ?>
        
        <div class="content">
            <h1 class="page-title">Objetivos</h1>
            
            <div class="objetivos-grid">
                <?php foreach ($objetivos as $objetivo): 
                    $percentual = ($objetivo['valor_atual'] / $objetivo['valor_meta']) * 100;
                    $percentual = min($percentual, 100);
                    $faltam = $objetivo['valor_meta'] - $objetivo['valor_atual'];
                    $status_class = $percentual >= 100 ? 'completo' : '';
                ?>
                <div class="card objetivo-card <?php echo $status_class; ?>">
                    <div class="objetivo-header">
                        <h3><?php echo htmlspecialchars($objetivo['titulo']); ?></h3>
                        <span class="objetivo-percentual"><?php echo number_format($percentual, 0); ?>%</span>
                    </div>
                    
                    <div class="objetivo-valores">
                        <span>R$ <?php echo number_format($objetivo['valor_atual'], 2, ',', '.'); ?></span>
                        <span>R$ <?php echo number_format($objetivo['valor_meta'], 2, ',', '.'); ?></span>
                    </div>
                    
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: <?php echo $percentual; ?>%"></div>
                    </div>
                    
                    <?php if ($percentual >= 100): ?>
                        <p class="objetivo-status completo">Meta atingida!</p>
                    <?php else: ?>
                        <p class="objetivo-status">Faltam R$ <?php echo number_format($faltam, 2, ',', '.'); ?> para atingir sua meta</p>
                    <?php endif; ?>
                    
                    <div class="objetivo-acoes">
                        <button class="btn-icon" onclick="editarObjetivo(<?php echo $objetivo['id']; ?>)">✏️</button>
                        <button class="btn-icon" onclick="excluirObjetivo(<?php echo $objetivo['id']; ?>)">🗑️</button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <button class="btn btn-primary btn-large" onclick="abrirModalObjetivo()">
                + Adicionar Novo Objetivo
            </button>
        </div>
    </div>
    
    <!-- Modal Novo Objetivo -->
    <div id="modalObjetivo" class="modal">
        <div class="modal-content">
            <span class="close" onclick="fecharModalObjetivo()">&times;</span>
            <h2 id="modalTitulo">Novo Objetivo</h2>
            <form id="formObjetivo">
                <input type="hidden" id="objetivo_id" name="id">
                
                <div class="form-group">
                    <label>Título do Objetivo</label>
                    <input type="text" id="titulo" name="titulo" required>
                </div>
                
                <div class="form-group">
                    <label>Valor Atual</label>
                    <input type="number" id="valor_atual" name="valor_atual" step="0.01" required>
                </div>
                
                <div class="form-group">
                    <label>Valor da Meta</label>
                    <input type="number" id="valor_meta" name="valor_meta" step="0.01" required>
                </div>
                
                <button type="submit" class="btn btn-primary">Salvar</button>
            </form>
        </div>
    </div>
    
    <script src="js/script.js"></script>
    <script src="js/objetivos.js"></script>
</body>
</html>
