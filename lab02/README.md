# LAB 2 - Aplicação PHP com MySQL em Docker

##  Objetivo
Executar uma aplicação PHP simples (Gerenciador de Tarefas) conectada a um banco MySQL, ambos em containers Docker com persistência de dados.

##  Estrutura do Projeto

```
lab02/
├── Dockerfile          # Dockerfile da aplicação PHP
├── app/
│   └── index.php       # Aplicação PHP (Gerenciador de Tarefas)
└── README.md          # Este arquivo
```

##  Requisitos Atendidos

-  Criar imagem da aplicação com Dockerfile (PHP 7.4 + Apache)
-  Executar containers com docker run (sem Docker Compose)
-  Provisionar MySQL em container Docker
-  Usar Docker Volume para persistência de dados do MySQL
-  Aplicação PHP conectada ao MySQL

##  Aplicação: Gerenciador de Tarefas

A aplicação é um gerenciador simples de tarefas com as seguintes funcionalidades:

- ✓ Adicionar novas tarefas com título e descrição
- ✓ Listar todas as tarefas ordenadas por data
- ✓ Deletar tarefas
- ✓ Persistência de dados em banco MySQL
- ✓ Interface moderna e responsiva

##  Como Executar

### 1. Criar Volume Docker para MySQL

Este volume garante que os dados do MySQL persistem mesmo quando o container é parado/deletado:

```bash
docker volume create mysql-volume
```

### 2. Executar Container MySQL

```bash
docker run --name mysql-lab02 \
  -e MYSQL_ROOT_PASSWORD=root_password \
  -e MYSQL_DATABASE=lab02_db \
  -e MYSQL_USER=lab02_user \
  -e MYSQL_PASSWORD=lab02_pass \
  -v mysql-volume:/var/lib/mysql \
  -d \
  mysql:5.7
```

**Explicação dos flags:**
- `--name mysql-lab02`: Nome do container MySQL
- `-e MYSQL_ROOT_PASSWORD=root_password`: Senha do usuário root
- `-e MYSQL_DATABASE=lab02_db`: Banco de dados criado automaticamente
- `-e MYSQL_USER=lab02_user`: Usuário do banco
- `-e MYSQL_PASSWORD=lab02_pass`: Senha do usuário
- `-v mysql-volume:/var/lib/mysql`: Docker Volume para persistência
- `3306/tcp` interno: MySQL acessivel apenas entre containers (evita conflito na porta 3306 do host)
- `-d`: Rodar em background

### 3. Construir Imagem PHP/Apache

```bash
cd lab02
docker build -t lab02-php .
```

**Resultado esperado:**
```
Successfully built [hash]
Successfully tagged lab02-php:latest
```

### 4. Executar Container PHP/Apache

```bash
# A partir do diretório lab02, execute:
docker run --name lab02-container \
  -p 8081:80 \
  --link mysql-lab02:mysql-lab02 \
  -d lab02-php
```

**Explicação dos flags:**
- `--name lab02-container`: Nome do container PHP
- `-p 8081:80`: Mapeia porta 8081 (host) → 80 (container)
- `--link mysql-lab02:mysql-lab02`: Conecta ao container MySQL (permite comunicação entre containers)
- `lab02-php`: Imagem da aplicação

### 5. Acessar a Aplicação

Abra seu navegador e acesse:
```
http://localhost:8081
```

Você deverá ver o Gerenciador de Tarefas!

##  Fluxo de Dados

```
[Navegador HTTP]
    ↓
[Container PHP/Apache:8081]
    ↓
[Rede Docker - Link]
    ↓
[Container MySQL:3306]
    ↓
[Docker Volume mysql-volume] ← Persistência de dados
```

##  Comandos Úteis

### Verificar containers rodando
```bash
docker ps
```

### Verificar logs PHP
```bash
docker logs lab02-container
```

### Verificar logs MySQL
```bash
docker logs mysql-lab02
```

### Conectar ao MySQL da linha de comando
```bash
docker exec -it mysql-lab02 mysql -u lab02_user -plab02_pass lab02_db
```

### Ver volumes criados
```bash
docker volume ls
```

### Inspecionar volume
```bash
docker volume inspect mysql-volume
```

##  Parar e Remover Containers

### Parar os containers
```bash
docker stop lab02-container mysql-lab02
```

### Remover os containers
```bash
docker rm lab02-container mysql-lab02
```

### Remover a imagem
```bash
docker rmi lab02-php
```

### Remover volume ( Isso deleta os dados!)
```bash
docker volume rm mysql-volume
```

##  Persistência de Dados

O Docker Volume `mysql-volume` garante que:
-  Dados persistem quando o container MySQL é parado
-  Dados são preservados quando o container é removido
-  Novo container pode usar o mesmo volume e acessar os mesmos dados

**Para verificar a persistência:**
1. Execute os containers e adicione algumas tarefas
2. Pare o container MySQL: `docker stop mysql-lab02`
3. Inicie novamente: `docker start mysql-lab02`
4. Atualize a página do navegador - as tarefas ainda estarão lá!

##  Troubleshooting

### "Connection refused" ao acessar a aplicação
- Aguarde 10-15 segundos para MySQL inicializar completamente
- Verificar logs: `docker logs mysql-lab02`

### "Can't connect to MySQL server"
- Verificar se MySQL está rodando: `docker ps | grep mysql-lab02`
- Verificar logs: `docker logs mysql-lab02`

### Erro "Port is already in use"
```bash
# Use outra porta
docker run --name lab02-container -p 8082:80 --link mysql-lab02:mysql-lab02 -d lab02-php
```

### Dados não persistem após remover container
- Certifique-se de usar o mesmo volume: `-v mysql-volume:/var/lib/mysql`
- Verificar se volume existe: `docker volume ls`

##  SLA Esperado

| Recurso | Configuração |
|---------|-------------|
| PHP | 7.4 + Apache2 |
| MySQL | 5.7 |
| Conexão | PDO com tratamento de erro |
| Banco | lab02_db |
| Usuário | lab02_user |
| Senhas | lab02_pass (MySQL) |
| Volume | mysql-volume (/var/lib/mysql) |
| Porta PHP | 8081 |
| Porta MySQL | 3306 |

##  Suporte

Para mais informações sobre Docker:
- Docker Docs: https://docs.docker.com
- Docker Hub: https://hub.docker.com

---
**Checkpoint 1 - Computação em Nuvem | FIAP 2026**
