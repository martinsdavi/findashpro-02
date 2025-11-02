<?php
require_once 'config.php';

$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';
    $confirmar_senha = $_POST['confirmar_senha'] ?? '';
    
    if (empty($nome) || empty($email) || empty($senha)) {
        $erro = 'Preencha todos os campos';
    } elseif ($senha !== $confirmar_senha) {
        $erro = 'As senhas não coincidem';
    } else {
        $conn = getConnection();
        
        // Verificar se email já existe
        $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $erro = 'Este email já está cadastrado';
        } else {
            // Inserir novo usuário
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $nome, $email, $senha_hash);
            
            if ($stmt->execute()) {
                $sucesso = 'Cadastro realizado com sucesso! Redirecionando...';
                $_SESSION['usuario_id'] = $stmt->insert_id;
                $_SESSION['usuario_nome'] = $nome;
                header("refresh:2;url=dashboard.php");
            } else {
                $erro = 'Erro ao cadastrar usuário';
            }
        }
        
        $stmt->close();
        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - FinDash Pro</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-box">
            <h1>Cadastre-se no <span class="brand">FinDash Pro</span></h1>
            <p class="subtitle">Crie sua conta para começar</p>
            
            <?php if ($erro): ?>
                <div class="erro-msg"><?php echo htmlspecialchars($erro); ?></div>
            <?php endif; ?>
            
            <?php if ($sucesso): ?>
                <div class="sucesso-msg"><?php echo htmlspecialchars($sucesso); ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label>Nome Completo</label>
                    <input type="text" name="nome" placeholder="Seu nome" required>
                </div>
                
                <div class="form-group">
                    <label>E-mail</label>
                    <input type="email" name="email" placeholder="seu-email@exemplo.com" required>
                </div>
                
                <div class="form-group">
                    <label>Senha</label>
                    <input type="password" name="senha" placeholder="••••••••" required>
                </div>
                
                <div class="form-group">
                    <label>Confirmar Senha</label>
                    <input type="password" name="confirmar_senha" placeholder="••••••••" required>
                </div>
                
                <button type="submit" class="btn btn-primary">CADASTRAR</button>
                <a href="login.php" class="btn btn-secondary">JÁ TENHO CONTA</a>
            </form>
        </div>
    </div>
    
    <script src="js/script.js"></script>
</body>
</html>
