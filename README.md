# 🚀 Finance App - Sistema de Gestão de Finanças Pessoais

## 📋 Resumo

Este é um **Sistema de Gestão de Finanças Pessoais** completo com:
- **Backend MVC** em PHP
- **API RESTful** JSON
- **Banco de Dados** MySQL
- **Padrão Profissional** de desenvolvimento

---

## 📁 Arquivos Inclusos

### 🔧 Arquivos de Código (3 arquivos principais)

1. **01_Transaction.php** → `models/Transaction.php`
   - Model que acessa o banco de dados
   - 8 métodos para operações CRUD

2. **02_TransactionController.php** → `controllers/TransactionController.php`
   - Controller que orquestra a lógica
   - 8 ações (list, get, create, update, delete, summary, etc)

3. **03_api.php** → `api.php`
   - Router e API que recebe requisições
   - Retorna JSON estruturado

### 📚 Documentação (6 arquivos)

4. **00_GUIA_INSTALACAO.md** - Como instalar os 3 arquivos
5. **04_API_ENDPOINTS_DOCUMENTACAO.md** - Todos os endpoints com exemplos
6. **Transaction_COMENTADO.php** - Model com comentários linha por linha
7. **TransactionController_COMENTADO.php** - Controller com comentários
8. **api_COMENTADO.php** - API com comentários
9. **GUIA_INSTALAR_COMENTADO.md** - Guia para aprender comentado

---

## 🎯 Arquitetura MVC

```
Cliente HTTP
    ↓
api.php (Router/API)
    ↓
TransactionController (Lógica)
    ↓
Transaction (Model/Banco)
    ↓
MySQL (Banco de Dados)
```

---

## 📦 Instalação Rápida

### 1. Copiar 3 Arquivos

```
C:\xampp\htdocs\finance-app\
├── models\
│   └── 01_Transaction.php         → Transaction.php
├── controllers\
│   └── 02_TransactionController.php → TransactionController.php
└── 03_api.php                      → api.php
```

### 2. Testar

```
http://localhost/finance-app/api.php?action=list
```

Você deve ver JSON com as transações.

---

## 🔌 Endpoints da API

| Método | URL | O que faz |
|--------|-----|----------|
| GET | `/api.php?action=list` | Listar todas |
| GET | `/api.php?action=get&id=1` | Obter uma |
| GET | `/api.php?action=summary` | Resumo financeiro |
| GET | `/api.php?action=type&type=expense` | Por tipo |
| POST | `/api.php` | Criar |
| PUT | `/api.php?id=1` | Atualizar |
| DELETE | `/api.php?id=1` | Deletar |
| GET | `/api.php?action=filter&...` | Com filtros |

---

## 💡 Conceitos Aprendidos

✅ **Padrão MVC** - Separação de responsabilidades
✅ **API RESTful** - Interface HTTP para dados
✅ **Banco de Dados MySQL** - Persistência
✅ **JSON** - Formato de comunicação
✅ **Validação** - Segurança de dados
✅ **Try-Catch** - Tratamento de erros
✅ **HTTP Status Codes** - Semântica corretas

---

## 📊 Estrutura de Pastas

```
C:\xampp\htdocs\finance-app\
├── config\
│   └── database.php          ← Conexão MySQL
├── models\
│   └── Transaction.php       ← Banco de dados
├── controllers\
│   └── TransactionController.php  ← Lógica
├── views\                    ← Frontend (próximo)
├── public\
│   ├── css\                  ← Estilos (próximo)
│   └── js\                   ← JavaScript (próximo)
├── api.php                   ← Router/API
├── teste_api.html            ← Página de teste
└── README.md                 ← Este arquivo
```

---

## 🗄️ Banco de Dados

### Tabela: transactions

```sql
CREATE TABLE transactions (
  id INT PRIMARY KEY AUTO_INCREMENT,
  type VARCHAR(50) NOT NULL,       -- 'income' ou 'expense'
  category VARCHAR(100) NOT NULL,
  description TEXT,
  amount DECIMAL(10, 2) NOT NULL,
  date DATE NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

---

## 🧪 Exemplos de Uso

### JavaScript - Listar

```javascript
fetch('http://localhost/finance-app/api.php?action=list')
  .then(res => res.json())
  .then(data => console.log(data));
```

### JavaScript - Criar

```javascript
fetch('http://localhost/finance-app/api.php', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({
    type: 'expense',
    category: 'Alimentação',
    amount: 150.50,
    date: '2026-04-24'
  })
})
.then(res => res.json())
.then(data => console.log(data));
```

### cURL - Listar

```bash
curl http://localhost/finance-app/api.php?action=list
```

### cURL - Criar

```bash
curl -X POST http://localhost/finance-app/api.php \
  -H "Content-Type: application/json" \
  -d '{"type":"expense","category":"Alimentação","amount":150.50,"date":"2026-04-24"}'
```

---

## 📚 Arquivos de Aprendizado

Se quer **entender cada linha de código**:

1. Leia: **Transaction_COMENTADO.php**
   - Entenda SQL, queries, banco de dados
   - Tempo: ~1 hora

2. Leia: **TransactionController_COMENTADO.php**
   - Entenda lógica, validação, orquestração
   - Tempo: ~1 hora

3. Leia: **api_COMENTADO.php**
   - Entenda headers, routing, JSON
   - Tempo: ~30 minutos

**Total:** 2,5-3 horas para entender tudo completamente.

---

## ✅ Checklist de Instalação

- [ ] Pasta `models/` criada
- [ ] Arquivo `Transaction.php` instalado
- [ ] Pasta `controllers/` criada
- [ ] Arquivo `TransactionController.php` instalado
- [ ] Arquivo `api.php` instalado
- [ ] Testei `http://localhost/finance-app/api.php?action=list`
- [ ] Recebo JSON com transações
- [ ] Li os arquivos comentados para aprender

---

## 🆘 Problemas Comuns

### "Arquivo não encontrado"
- Verifique se os arquivos estão na pasta correta
- Verifique se renomeou corretamente (Transaction.php, não 01_Transaction.php)

### "Fatal error: Class not found"
- Verifique se o arquivo foi salvo completo
- Verifique se começa com `<?php` e termina com `?>`

### "Database connection error"
- Verifique se `config/database.php` existe
- Verifique se MySQL está rodando no XAMPP
- Verifique banco `finance_app` e tabela `transactions` existem

### "JSON vazio ou erro"
- Abra o navegador F12 (DevTools)
- Vá em "Network"
- Veja qual foi a resposta exata
- Procure o erro na documentação

---

## 🚀 Próximos Passos

1. ✅ **Backend MVC** - PRONTO!
2. ✅ **API RESTful** - PRONTO!
3. ⏳ **Frontend HTML** - Criar página principal
4. ⏳ **CSS Responsivo** - Estilizar
5. ⏳ **JavaScript** - Conectar à API

---

## 📖 Recursos

- **MDN Web Docs:** https://developer.mozilla.org/
- **PHP Manual:** https://www.php.net/manual/
- **MySQL Documentation:** https://dev.mysql.com/doc/
- **REST API Guidelines:** https://restfulapi.net/

---

## 👨‍💻 Desenvolvedor

**Matheus Bacelar**
- GitHub: @MBacelar93
- Projeto: Finance App
- Data: Abril 2026

---

## 📄 Licença

Código aberto para fins educacionais.

---

## 🎉 Sucesso!

Você tem uma **API profissional e funcional**!
Agora é só criar um frontend bonito e conectar! 🚀

Qualquer dúvida, releia os arquivos comentados.
Tudo está bem documentado! 📚