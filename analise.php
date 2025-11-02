<?php
require_once 'config.php';
verificarLogin();
$usuario = getUsuarioLogado();

$conn = getConnection();

// Totais gerais
$stmt = $conn->prepare("SELECT 
    SUM(CASE WHEN tipo = 'Receita' THEN valor ELSE 0 END) as total_receitas,
    SUM(CASE WHEN tipo = 'Despesa' THEN valor ELSE 0 END) as total_despesas
    FROM transacoes WHERE usuario_id = ?");
$stmt->bind_param("i", $_SESSION['usuario_id']);
$stmt->execute();
$totais = $stmt->get_result()->fetch_assoc();
$stmt->close();

$saldo = $totais['total_receitas'] - $totais['total_despesas'];

// Receitas por categoria
$stmt = $conn->prepare("SELECT categoria, SUM(valor) as total 
    FROM transacoes 
    WHERE usuario_id = ? AND tipo = 'Receita' 
    GROUP BY categoria 
    ORDER BY total DESC");
$stmt->bind_param("i", $_SESSION['usuario_id']);
$stmt->execute();
$receitas_categoria = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Despesas por categoria
$stmt = $conn->prepare("SELECT categoria, SUM(valor) as total 
    FROM transacoes 
    WHERE usuario_id = ? AND tipo = 'Despesa' 
    GROUP BY categoria 
    ORDER BY total DESC");
$stmt->bind_param("i", $_SESSION['usuario_id']);
$stmt->execute();
$despesas_categoria = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$conn->close();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Análise de Receita - FinDash Pro</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="main-content">
        <?php include 'includes/header.php'; ?>
        
        <div class="content">
            <h1 class="page-title">Análise de Receita</h1>
            
            <div class="analise-cards">
                <div class="card analise-card receita">
                    <div class="analise-icon">↑</div>
                    <div>
                        <div class="analise-label">Receita Total</div>
                        <div class="analise-value">R$ <?php echo number_format($totais['total_receitas'], 2, ',', '.'); ?></div>
                        <div class="analise-change">+12,5% vs mês anterior</div>
                    </div>
                </div>
                
                <div class="card analise-card despesa">
                    <div class="analise-icon">↓</div>
                    <div>
                        <div class="analise-label">Despesa Total</div>
                        <div class="analise-value">R$ <?php echo number_format($totais['total_despesas'], 2, ',', '.'); ?></div>
                        <div class="analise-change">-6,3% vs mês anterior</div>
                    </div>
                </div>
                
                <div class="card analise-card saldo">
                    <div class="analise-icon">=</div>
                    <div>
                        <div class="analise-label">Saldo Líquido</div>
                        <div class="analise-value">R$ <?php echo number_format($saldo, 2, ',', '.'); ?></div>
                        <div class="analise-change">+19,2% vs mês anterior</div>
                    </div>
                </div>
            </div>
            
            <div class="analise-row">
                <!-- Receita por Categoria -->
                <div class="card">
                    <h3>Receita por Categoria</h3>
                    <div class="categoria-list">
                        <?php 
                        $total_receitas = $totais['total_receitas'];
                        foreach ($receitas_categoria as $receita): 
                            $percentual = $total_receitas > 0 ? ($receita['total'] / $total_receitas) * 100 : 0;
                        ?>
                        <div class="categoria-item">
                            <div class="categoria-info">
                                <span class="categoria-nome"><?php echo htmlspecialchars($receita['categoria']); ?></span>
                                <span class="categoria-valor">R$ <?php echo number_format($receita['total'], 2, ',', '.'); ?> (<?php echo number_format($percentual, 1); ?>%)</span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-fill receita-fill" style="width: <?php echo $percentual; ?>%"></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <!-- Despesas por Categoria -->
                <div class="card">
                    <h3>Despesas por Categoria</h3>
                    <div class="categoria-list">
                        <?php 
                        $total_despesas = $totais['total_despesas'];
                        foreach ($despesas_categoria as $despesa): 
                            $percentual = $total_despesas > 0 ? ($despesa['total'] / $total_despesas) * 100 : 0;
                        ?>
                        <div class="categoria-item">
                            <div class="categoria-info">
                                <span class="categoria-nome"><?php echo htmlspecialchars($despesa['categoria']); ?></span>
                                <span class="categoria-valor">R$ <?php echo number_format($despesa['total'], 2, ',', '.'); ?> (<?php echo number_format($percentual, 1); ?>%)</span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-fill despesa-fill" style="width: <?php echo $percentual; ?>%"></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            
            <!-- Comparação Mensal -->
            <div class="card">
                <h3>Comparação Mensal</h3>
                <canvas id="comparacaoMensalChart" width="800" height="300"></canvas>
            </div>
        </div>
    </div>
    
    <script src="js/script.js"></script>
    <script src="js/charts.js"></script>
</body>
</html>
