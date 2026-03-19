# LAB 1 - Página Web Estática em Docker

##  Objetivo
Executar uma página web estática (HTML com imagem) em um container Docker usando NGINX.

##  Estrutura do Projeto

```
lab01/
├── Dockerfile      # Configuração da imagem Docker
├── index.html      # Página web estática
└── README.md       # Este arquivo
```

##  Requisitos Atendidos

-  Criar imagem com Dockerfile usando NGINX
-  Executar container com docker run (sem Docker Compose)
-  Usar bind mount para adicionar index.html (não usar COPY no Dockerfile)
-  Página contém imagem (Docker logo)

##  Como Executar

### 1. Construir a Imagem Docker

```bash
# Navegue até o diretório do lab01
cd lab01

# Construa a imagem
docker build -t lab01-web .
```

**Resultado esperado:**
```
Successfully built [hash]
Successfully tagged lab01-web:latest
```

### 2. Executar o Container com Bind Mount

```bash
# A partir do diretório lab01, execute:
docker run --name lab01-container -p 8080:80 -v "${PWD}/index.html:/usr/share/nginx/html/index.html" -d lab01-web
```

**Explicação dos flags:**
- `--name lab01-container`: Nome do container
- `-p 8080:80`: Mapeia porta 8080 (host) → 80 (container)
- `-v "${PWD}/index.html:/usr/share/nginx/html/index.html"`: Bind mount do arquivo HTML
- `lab01-web`: Nome da imagem

### 3. Acessar a Aplicação

Abra seu navegador e acesse:
```
http://localhost:8080
```

Você deverá ver uma página estilizada com o logo do Docker!

##  Comandos Úteis

### Verificar se o container está rodando
```bash
docker ps
```

### Ver logs do container
```bash
docker logs lab01-container
```

### Parar o container
```bash
docker stop lab01-container
```

### Remover o container
```bash
docker rm lab01-container
```

### Remover a imagem
```bash
docker rmi lab01-web
```

##  Detalhes Técnicos

### Dockerfile
- **Base Image**: `nginx:latest` - Servidor web leve e eficiente
- **Porta Exposta**: 80 - Porta padrão do NGINX
- **Comando**: `nginx -g "daemon off;"` - Mantém o NGINX em primeiro plano

### Bind Mount
É usado bind mount `${PWD}/index.html` ao invés de COPY no Dockerfile porque:
-  Permite editar o arquivo HTML sem reconstruir a imagem
-  Desenvolver e testar em tempo real
-  Flexibilidade ao alternar diferentes versões do arquivo

##  Visualização da Página

A página web contém:
- ✓ Logo do Docker
- ✓ Informações sobre o setup
- ✓ Design responsivo e moderno
- ✓ CSS3 com gradientes e shadows
- ✓ HTML5 semântico

##  Suporte

Em caso de erros:

**Porta já em uso:**
```bash
# Use outra porta
docker run --name lab01-container -p 8081:80 -v "${PWD}/index.html:/usr/share/nginx/html/index.html" -d lab01-web
```

**Container não inicia:**
```bash
# Verifique os logs
docker logs lab01-container
```

**Bind mount não funciona:**
- Linux: Use `${PWD}` (já incluído nos comandos)
- Windows (PowerShell): Use `${PWD}` também funciona
- Windows (CMD): Substitua `${PWD}` pelo caminho completo

---
**Checkpoint 1 - Computação em Nuvem | FIAP 2026**
