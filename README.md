# 🅿️ Sistema de Estacionamento – WOWLIVE / Mateus Urbano

Sistema web em PHP para controle de estacionamento com:

- Cadastro de entrada com foto da placa
- Saída com cálculo automático de diárias e valor
- Tabela de preços configurável (carro/ônibus, normal/parceiro, desconto por diárias)
- Controle de veículos ativos
- Geração de recibo em PDF
- Controle de usuários (ADMIN / OPERADOR)
- Layout dark minimalista e responsivo

---

## 🧱 Stack técnica

- **Linguagem:** PHP 7+ (testado em XAMPP)
- **Banco:** MySQL/MariaDB
- **Front-end:** HTML + CSS custom (tema escuro)
- **PDF:** [FPDF](http://www.fpdf.org/)
- **Controle de versão:** Git + GitHub

---

## 📦 Funcionalidades

### 1. Autenticação e perfis

- Login com `login` + `senha`
- Senha armazenada com `password_hash()`
- Perfis:
  - `ADMIN` – acesso total, inclusive gestão de usuários e tabela de preços
  - `OPERADOR` – apenas operações do dia a dia (entrada/saída/ativos)

### 2. Entrada de veículos (`entrada.php`)

- Upload de **foto da placa** (opcional)
- Campos:
  - Tipo de veículo: `CARRO` ou `ONIBUS`
  - Tipo de cliente: `NORMAL` ou `PARCEIRO`
  - Placa (formato antigo e Mercosul)
  - Nome do cliente
  - Modelo
  - Cor
  - Observações
- Registro em `veiculos_movimentacao` com:
  - `data_hora_entrada`
  - `foto_entrada`
  - `status = 'ATIVO'`

### 3. Veículos ativos (`ativos.php`)

- Lista todos os registros com `status = 'ATIVO'`
- Mostra: placa, data/hora de entrada, cliente, modelo, cor
- Link direto para **registrar saída** por placa

### 4. Saída de veículos

- Tela de busca por placa (`saida.php`)
- Processamento (`saida_buscar.php`):
  - Localiza último registro `ATIVO`
  - Calcula:
    - tempo total (ex.: `1 dia 3h 20min`)
    - número de diárias (mínimo 1, fracionado a cada 24h)
    - valor total com base nas regras de preço
  - Permite anexar foto de saída (opcional)
- Confirmação e gravação (`saida_finalizar.php`):
  - Atualiza:
    - `data_hora_saida`
    - `tempo_total`
    - `num_diarias`
    - `valor_total`
    - `foto_saida`
    - `status = 'FINALIZADO'`

### 5. Tabela de preços (`precos.php`)

- CRUD completo em `tabela_precos`
- Campos:
  - `tipo_veiculo` – `CARRO` ou `ONIBUS`
  - `cliente_tipo` – `NORMAL` ou `PARCEIRO`
  - `min_dias`
  - `max_dias` (pode ser `NULL` para ∞)
  - `valor_diaria`
- Regra padrão usada nas funções:
  - Carro: R$ 50,00 / diária, sem desconto
  - Ônibus normal:
    - 1 diária → R$ 120,00
    - 2+ diárias → R$ 100,00/dia
  - Ônibus parceiro:
    - 1 diária → R$ 80,00
    - 2+ diárias → R$ 60,00/dia

### 6. Recibo em PDF (`recibo.php`)

- Gera recibo com FPDF contendo:
  - Dados do estabelecimento
  - Placa, modelo, cor
  - Tipo de veículo / tipo de cliente
  - Período (entrada/saída)
  - Tempo total
  - Número de diárias
  - Valor total em destaque
- Disponível após finalizar a saída.

### 7. Administração de usuários (`admin_usuarios.php`)

- Criar usuários:
  - Nome, login, perfil (ADMIN/OPERADOR)
  - Senha inicial padrão: `123456` (obrigatório trocar depois)
- Reset de senha (volta para `123456`)
- Ativar/Desativar usuário
- Acesso restrito a `user_perfil = 'ADMIN'`

---

## 🗃️ Estrutura de banco (visão geral)

- `usuarios`
- `veiculos_movimentacao`
- `tabela_precos`

Scripts SQL podem ser exportados do phpMyAdmin a partir do ambiente atual.

---

## 🔐 Segurança básica

- Senhas com `password_hash() / password_verify()`
- Sessão PHP com validação em todas as páginas protegidas
- Restrição de acesso por perfil (ADMIN vs OPERADOR)
- Redirecionamento para `login.php` quando a sessão não existe

---

## 🚀 Fluxo de uso

1. ADMIN configura:
   - usuários
   - tabela de preços
2. OPERADOR faz:
   - login
   - registra entradas
   - consulta ativos
   - registra saídas
   - gera recibos

---

## 🧪 Desenvolvimento e Git

Branches recomendadas:

- `main` – produção
- `dev` – desenvolvimento e novas features
- `hotfix` – correções rápidas em produção

Fluxo básico:

```bash
# Criar nova funcionalidade
git checkout dev
# ...codar...
git add .
git commit -m "Descrição da mudança"
git push

# Depois fazer merge dev -> main via Pull Request no GitHub
