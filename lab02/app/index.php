<?php
ob_start();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LAB 2 - Gerenciador de Tarefas</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            padding: 40px;
        }
        
        header {
            text-align: center;
            margin-bottom: 40px;
            border-bottom: 2px solid #667eea;
            padding-bottom: 20px;
        }
        
        h1 {
            color: #333;
            font-size: 2.5em;
            margin-bottom: 10px;
        }
        
        .subtitle {
            color: #667eea;
            font-size: 1.1em;
        }
        
        .info-box {
            background: #f9f9f9;
            border-left: 4px solid #667eea;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
        }
        
        input[type="text"],
        textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-family: inherit;
            font-size: 1em;
        }
        
        textarea {
            resize: vertical;
            min-height: 100px;
        }
        
        input[type="text"]:focus,
        textarea:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 5px rgba(102, 126, 234, 0.1);
        }
        
        button {
            background: #667eea;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            font-size: 1em;
            cursor: pointer;
            font-weight: 600;
            transition: background 0.3s;
        }
        
        button:hover {
            background: #764ba2;
        }
        
        .task-list {
            list-style: none;
            margin-top: 30px;
        }
        
        .task-item {
            background: #f9f9f9;
            padding: 20px;
            margin-bottom: 15px;
            border-radius: 5px;
            border-left: 4px solid #667eea;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .task-content {
            text-align: left;
            flex: 1;
        }
        
        .task-title {
            font-size: 1.1em;
            color: #333;
            font-weight: 600;
        }
        
        .task-desc {
            color: #666;
            font-size: 0.95em;
            margin-top: 5px;
        }
        
        .delete-btn {
            background: #e74c3c;
            padding: 8px 15px;
            font-size: 0.9em;
        }
        
        .delete-btn:hover {
            background: #c0392b;
        }
        
        .empty-state {
            text-align: center;
            color: #999;
            padding: 40px 20px;
        }
        
        .badge {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.85em;
            margin-top: 10px;
        }
        
        footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            text-align: center;
            color: #999;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>📝 Gerenciador de Tarefas</h1>
            <p class="subtitle">LAB 2 - Docker + PHP + MySQL</p>
        </header>
        
        <div class="info-box">
            <strong>✓ Status:</strong> Aplicação conectada ao MySQL com sucesso!<br>
            <strong>📊 Banco de Dados:</strong> MySQL com persistência via Docker Volume<br>
            <strong>🐳 Infraestrutura:</strong> 2 Containers (PHP + MySQL)
        </div>
        
        <form method="POST" action="">
            <h3>Adicionar Nova Tarefa</h3>
            <div class="form-group">
                <label for="title">Título da Tarefa:</label>
                <input type="text" id="title" name="title" placeholder="Digite o título..." required>
            </div>
            <div class="form-group">
                <label for="description">Descrição:</label>
                <textarea id="description" name="description" placeholder="Descreva a tarefa..."></textarea>
            </div>
            <button type="submit">Adicionar Tarefa</button>
        </form>
        
        <h3 style="margin-top: 40px; margin-bottom: 20px;">Minhas Tarefas</h3>
        
        <?php
        // Conexão com MySQL
        $servername = "mysql-lab02";
        $username = "lab02_user";
        $password = "lab02_pass";
        $dbname = "lab02_db";
        
        try {
            $conn = new PDO(
                "mysql:host=$servername;dbname=$dbname",
                $username,
                $password,
                array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
            );
            
            // Criar tabela se não existir
            $sql_create = "CREATE TABLE IF NOT EXISTS tasks (
                id INT AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(255) NOT NULL,
                description TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )";
            $conn->exec($sql_create);
            
            // Adicionar nova tarefa
            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['title'])) {
                $title = $_POST['title'];
                $description = $_POST['description'] ?? '';
                
                $sql_insert = "INSERT INTO tasks (title, description) VALUES (?, ?)";
                $stmt = $conn->prepare($sql_insert);
                $stmt->execute([$title, $description]);
                
                header("Location: " . $_SERVER['PHP_SELF']);
                exit;
            }
            
            // Deletar tarefa
            if (isset($_GET['delete'])) {
                $id = $_GET['delete'];
                $sql_delete = "DELETE FROM tasks WHERE id = ?";
                $stmt = $conn->prepare($sql_delete);
                $stmt->execute([$id]);
                
                header("Location: " . $_SERVER['PHP_SELF']);
                exit;
            }
            
            // Listar tarefas
            $sql_select = "SELECT * FROM tasks ORDER BY created_at DESC";
            $result = $conn->query($sql_select);
            $tasks = $result->fetchAll(PDO::FETCH_ASSOC);
            
            if (empty($tasks)) {
                echo '<div class="empty-state">
                    <p>Nenhuma tarefa ainda.</p>
                    <p>Adicione uma tarefa acima para começar! 🚀</p>
                </div>';
            } else {
                echo '<ul class="task-list">';
                foreach ($tasks as $task) {
                    echo '<li class="task-item">
                        <div class="task-content">
                            <div class="task-title">' . htmlspecialchars($task['title']) . '</div>';
                    if (!empty($task['description'])) {
                        echo '<div class="task-desc">' . htmlspecialchars($task['description']) . '</div>';
                    }
                    echo '<span class="badge">📅 ' . date('d/m/Y H:i', strtotime($task['created_at'])) . '</span>
                        </div>
                        <a href="?delete=' . $task['id'] . '" onclick="return confirm(\'Tem certeza?\')">
                            <button class="delete-btn">Deletar</button>
                        </a>
                    </li>';
                }
                echo '</ul>';
            }
            
        } catch(PDOException $e) {
            echo '<div class="info-box" style="border-left-color: #e74c3c; background: #ffe6e6;">
                <strong>❌ Erro de Conexão:</strong><br>
                Não foi possível conectar ao MySQL.<br>
                <small>Verifique se o container MySQL está rodando e acessível.</small><br><br>
                <strong>Detalhes:</strong> ' . htmlspecialchars($e->getMessage()) . '
            </div>';
        } finally {
            $conn = null;
        }
        ?>
        
        <footer>
            <p><strong>Checkpoint 1 - Computação em Nuvem | FIAP 2026</strong></p>
            <p>PHP 7.4 + Apache + MySQL com Docker</p>
        </footer>
    </div>
</body>
</html>
