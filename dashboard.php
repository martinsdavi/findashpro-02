<?php
require_once 'config.php';
verificarLogin();
$usuario = getUsuarioLogado();

$conn = getConnection();

// Buscar transações recentes
$stmt = $conn->prepare("SELECT * FROM transacoes WHERE usuario_id = ? ORDER BY data DESC, id DESC LIMIT 9");
$stmt->bind_param("i", $_SESSION['usuario_id']);
$stmt->execute();
$transacoes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Calcular totais
$stmt = $conn->prepare("SELECT 
    SUM(CASE WHEN tipo = 'Receita' THEN valor ELSE 0 END) as total_receitas,
    SUM(CASE WHEN tipo = 'Despesa' THEN valor ELSE 0 END) as total_despesas
    FROM transacoes WHERE usuario_id = ?");
$stmt->bind_param("i", $_SESSION['usuario_id']);
$stmt->execute();
$totais = $stmt->get_result()->fetch_assoc();
$stmt->close();

$saldo = $totais['total_receitas'] - $totais['total_despesas'];

// Buscar recebimentos
$stmt = $conn->prepare("SELECT * FROM transacoes WHERE usuario_id = ? AND tipo = 'Receita' ORDER BY valor DESC LIMIT 3");
$stmt->bind_param("i", $_SESSION['usuario_id']);
$stmt->execute();
$recebimentos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Buscar contas a pagar (despesas futuras ou recentes)
$stmt = $conn->prepare("SELECT * FROM transacoes WHERE usuario_id = ? AND tipo = 'Despesa' ORDER BY data DESC LIMIT 2");
$stmt->bind_param("i", $_SESSION['usuario_id']);
$stmt->execute();
$contas_pagar = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$conn->close();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - FinDash Pro</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="main-content">
        <?php include 'includes/header.php'; ?>
        
        <div class="content">
            <h1 class="page-title">Dashboard</h1>
            
            <div class="dashboard-grid">
                <!-- Cartão -->
                <div class="card card-credit">
                    <div class="card-number">XXXX XXXX XXXX XXXX</div>
                    <div class="card-info">
                        <div class="card-holder"><?php echo htmlspecialchars($usuario['nome']); ?></div>
                        <div class="card-date"><?php echo date('m/y'); ?></div>
                    </div>
                    <div class="card-logo"></div>
                </div>
                
                <!-- Carteira -->
                <div class="card">
                    <div class="card-label">Carteira</div>
                    <div class="card-value">R$ <?php echo number_format($saldo, 2, ',', '.'); ?></div>
                    <div class="card-details">
                        <span class="receita">↑ R$ <?php echo number_format($totais['total_receitas'], 2, ',', '.'); ?></span>
                        <span class="despesa">↓ R$ <?php echo number_format($totais['total_despesas'], 2, ',', '.'); ?></span>
                    </div>
                </div>
                
                <!-- Contas a Pagar -->
                <div class="card">
                    <div class="card-label">Contas a Pagar</div>
                    <div class="card-subtitle">Mantenha suas contas atualizadas para evitar problemas</div>
                    <div class="progress-info">
                        <strong>14 DE 14</strong>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: 100%"></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="dashboard-row">
                <!-- Transações -->
                <div class="card transactions-card">
                    <h3>Transações</h3>
                    <div class="transactions-list">
                        <?php foreach (array_slice($transacoes, 0, 9) as $transacao): ?>
                        <div class="transaction-item">
                            <span class="transaction-icon">↓</span>
                            <span class="transaction-desc"><?php echo htmlspecialchars($transacao['descricao']); ?></span>
                            <span class="transaction-date"><?php echo date('M d', strtotime($transacao['data'])); ?></span>
                            <span class="transaction-value">R$ <?php echo number_format($transacao['valor'], 0, ',', '.'); ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <!-- Ganhos Mensais -->
                <div class="card">
                    <div class="card-header-row">
                        <h3>Ganhos Mensais</h3>
                        <span class="badge-receita">↑ renda</span>
                    </div>
                    <div class="card-value">R$21.124,00</div>
                    <canvas id="ganhosMensaisChart" width="400" height="200"></canvas>
                </div>
            </div>
            
            <div class="dashboard-row">
                <!-- Recebimentos -->
                <div class="card">
                    <h3>Recebimentos</h3>
                    <div class="recebimentos-list">
                        <?php foreach ($recebimentos as $recebimento): ?>
                        <div class="recebimento-item">
                            <span class="recebimento-icon">↗</span>
                            <div>
                                <div class="recebimento-value">R$ <?php echo number_format($recebimento['valor'], 2, ',', '.'); ?></div>
                                <div class="recebimento-desc"><?php echo htmlspecialchars($recebimento['descricao']); ?></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <!-- Contas a pagar -->
                <div class="card">
                    <h3>Contas a pagar</h3>
                    <div class="contas-list">
                        <?php foreach ($contas_pagar as $conta): ?>
                        <div class="conta-item">
                            <span class="conta-icon">□</span>
                            <div>
                                <div class="conta-value">R$ <?php echo number_format($conta['valor'], 2, ',', '.'); ?></div>
                                <div class="conta-desc"><?php echo htmlspecialchars($conta['descricao']); ?></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <!-- Ganhos -->
                <div class="card ganhos-card">
                    <h3>Ganhos</h3>
                    <canvas id="ganhosChart" width="200" height="200"></canvas>
                    <div class="ganhos-value">R$ 4.523,98</div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="js/script.js"></script>
    <script src="js/charts.js"></script>
</body>
</html>
