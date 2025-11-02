<?php
require_once 'config.php';

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';
    
    if (!empty($email) && !empty($senha)) {
        $conn = getConnection();
        $stmt = $conn->prepare("SELECT id, nome, senha FROM usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $usuario = $result->fetch_assoc();
            if (password_verify($senha, $usuario['senha'])) {
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nome'] = $usuario['nome'];
                header('Location: dashboard.php');
                exit();
            } else {
                $erro = 'Senha incorreta';
            }
        } else {
            $erro = 'Usuário não encontrado';
        }
        
        $stmt->close();
        $conn->close();
    } else {
        $erro = 'Preencha todos os campos';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - FinDash Pro</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-box">
            <h1>Bem vindo ao <span class="brand">FinDash Pro</span></h1>
            <p class="subtitle">Preencha os dados de Login para acessar</p>
            
            <?php if ($erro): ?>
                <div class="erro-msg"><?php echo htmlspecialchars($erro); ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label>E-mail</label>
                    <input type="email" name="email" placeholder="seu-email@exemplo.com" required>
                </div>
                
                <div class="form-group">
                    <label>Senha</label>
                    <input type="password" name="senha" id="senha" placeholder="••••••••" required>
                    <span class="toggle-password" onclick="togglePassword()">Mostrar</span>
                </div>
                
                <button type="submit" class="btn btn-primary">ENTRAR</button>
                <a href="cadastro.php" class="btn btn-secondary">CADASTRE-SE</a>
            </form>
        </div>
    </div>
    
    <script src="js/script.js"></script>
</body>
</html>
