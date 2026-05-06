// ===================================================
// CONFIGURAÇÃO
// ===================================================

const API_BASE_URL = 'http://localhost/finance-app-php/api.php';

// ===================================================
// ELEMENTOS DO DOM
// ===================================================

const form = document.getElementById('transaction-form');
const typeSelect = document.getElementById('type');
const categoryInput = document.getElementById('category');
const amountInput = document.getElementById('amount');
const dateInput = document.getElementById('date');
const descriptionInput = document.getElementById('description');

const transactionsList = document.getElementById('transactions-list');
const emptyState = document.getElementById('empty-state');
const loading = document.getElementById('loading');
const transactionCount = document.getElementById('transaction-count');

const totalIncomeEl = document.getElementById('total-income');
const totalExpenseEl = document.getElementById('total-expense');
const balanceEl = document.getElementById('balance');

const filterType = document.getElementById('filter-type');
const filterCategory = document.getElementById('filter-category');
const filterDateFrom = document.getElementById('filter-date-from');
const filterDateTo = document.getElementById('filter-date-to');
const btnFilter = document.getElementById('btn-filter');
const btnReset = document.getElementById('btn-reset');

const modalEdit = document.getElementById('modal-edit');
const editForm = document.getElementById('edit-form');
const modalClose = document.getElementById('modal-close');

// ===================================================
// VARIÁVEIS GLOBAIS
// ===================================================

let allTransactions = [];
let filteredTransactions = [];

// ===================================================
// INICIALIZAÇÃO
// ===================================================

document.addEventListener('DOMContentLoaded', () => {
    console.log('DOM carregado');
    setDateToday();
    loadTransactions();
    attachEventListeners();
});

// ===================================================
// EVENT LISTENERS
// ===================================================

function attachEventListeners() {
    console.log('Anexando event listeners...');
    
    if (form) form.addEventListener('submit', handleSubmitForm);
    if (btnFilter) btnFilter.addEventListener('click', handleFilter);
    if (btnReset) btnReset.addEventListener('click', handleReset);
    if (modalClose) modalClose.addEventListener('click', closeModal);
    if (editForm) editForm.addEventListener('submit', handleSubmitEdit);
    
    if (modalEdit) {
        modalEdit.addEventListener('click', (e) => {
            if (e.target === modalEdit) closeModal();
        });
    }
    
    console.log('Event listeners anexados!');
}

// ===================================================
// FUNÇÕES PRINCIPAIS
// ===================================================

// Carregar transações
async function loadTransactions() {
    try {
        showLoading(true);
        
        const response = await fetch(`${API_BASE_URL}?action=list`);
        const data = await response.json();
        
        console.log('Resposta da API:', data);
        
        if (data.success) {
            allTransactions = data.data || [];
            filteredTransactions = [...allTransactions];
            renderTransactions();
            updateSummary();
        } else {
            showNotification('Erro ao carregar transações', 'error');
        }
    } catch (error) {
        console.error('Erro:', error);
        showNotification('Erro ao conectar com servidor', 'error');
    } finally {
        showLoading(false);
    }
}

// Renderizar transações
function renderTransactions() {
    console.log('Renderizando transações...', filteredTransactions);
    
    if (!transactionCount) {
        console.error('transactionCount não encontrado!');
        return;
    }
    
    transactionCount.textContent = `${filteredTransactions.length} ${filteredTransactions.length === 1 ? 'registro' : 'registros'}`;
    
    if (filteredTransactions.length === 0) {
        if (transactionsList) transactionsList.innerHTML = '';
        if (emptyState) emptyState.style.display = 'block';
        return;
    }
    
    if (emptyState) emptyState.style.display = 'none';
    
    if (transactionsList) {
        transactionsList.innerHTML = filteredTransactions.map(transaction => `
            <div class="transaction-item ${transaction.type}">
                <div class="transaction-info">
                    <div class="transaction-category">
                        ${transaction.type === 'income' ? '💰' : '💸'} ${transaction.category}
                    </div>
                    ${transaction.description ? `<div class="transaction-description">${transaction.description}</div>` : ''}
                    <div class="transaction-date">📅 ${formatDate(transaction.date)}</div>
                </div>
                <div class="transaction-amount">
                    ${transaction.type === 'income' ? '+' : '-'} R$ ${formatCurrency(transaction.amount)}
                </div>
                <div class="transaction-actions">
                    <button class="btn btn-edit" onclick="openEditModal(${transaction.id}, '${transaction.category}', ${transaction.amount}, '${transaction.date}', '${transaction.description || ''}')">
                        ✏️
                    </button>
                    <button class="btn btn-danger" onclick="deleteTransaction(${transaction.id})">
                        🗑️
                    </button>
                </div>
            </div>
        `).join('');
    }
}

// Atualizar resumo
async function updateSummary() {
    try {
        const response = await fetch(`${API_BASE_URL}?action=summary`);
        const data = await response.json();
        
        console.log('Resumo:', data);
        
        if (data.success) {
            const { total_income, total_expense, balance } = data.summary;
            
            if (totalIncomeEl) totalIncomeEl.textContent = `R$ ${formatCurrency(total_income)}`;
            if (totalExpenseEl) totalExpenseEl.textContent = `R$ ${formatCurrency(total_expense)}`;
            if (balanceEl) {
                balanceEl.textContent = `R$ ${formatCurrency(balance)}`;
                
                if (balance >= 0) {
                    balanceEl.style.color = 'inherit';
                } else {
                    balanceEl.style.color = '#ef4444';
                }
            }
        }
    } catch (error) {
        console.error('Erro ao atualizar resumo:', error);
    }
}

// ===================================================
// HANDLERS
// ===================================================

// Submeter formulário (criar)
async function handleSubmitForm(e) {
    e.preventDefault();
    
    const data = {
        type: typeSelect.value,
        category: categoryInput.value,
        amount: amountInput.value,
        date: dateInput.value,
        description: descriptionInput.value
    };
    
    console.log('Criando transação:', data);
    
    try {
        const response = await fetch(API_BASE_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        console.log('Resposta create:', result);
        
        if (result.success) {
            showNotification('Transação criada com sucesso! 🎉', 'success');
            form.reset();
            setDateToday();
            loadTransactions();
        } else {
            showNotification(`Erro: ${result.error}`, 'error');
        }
    } catch (error) {
        console.error('Erro:', error);
        showNotification('Erro ao criar transação', 'error');
    }
}

// Deletar transação
async function deleteTransaction(id) {
    if (!confirm('Tem certeza que deseja deletar esta transação?')) return;
    
    console.log('Deletando transação:', id);
    
    try {
        const response = await fetch(`${API_BASE_URL}?id=${id}`, {
            method: 'DELETE'
        });
        
        const data = await response.json();
        
        console.log('Resposta delete:', data);
        
        if (data.success) {
            showNotification('Transação deletada com sucesso', 'success');
            loadTransactions();
        } else {
            showNotification(`Erro: ${data.error}`, 'error');
        }
    } catch (error) {
        console.error('Erro:', error);
        showNotification('Erro ao deletar transação', 'error');
    }
}

// Abrir modal de edição
function openEditModal(id, category, amount, date, description) {
    console.log('Abrindo modal:', {id, category, amount, date, description});
    
    if (document.getElementById('edit-id')) document.getElementById('edit-id').value = id;
    if (document.getElementById('edit-category')) document.getElementById('edit-category').value = category;
    if (document.getElementById('edit-amount')) document.getElementById('edit-amount').value = amount;
    if (document.getElementById('edit-date')) document.getElementById('edit-date').value = date;
    if (document.getElementById('edit-description')) document.getElementById('edit-description').value = description;
    
    if (modalEdit) modalEdit.style.display = 'flex';
}

// Fechar modal
function closeModal() {
    console.log('Fechando modal');
    if (modalEdit) modalEdit.style.display = 'none';
    if (editForm) editForm.reset();
}

// Submeter edição
async function handleSubmitEdit(e) {
    e.preventDefault();
    
    const id = document.getElementById('edit-id').value;
    const data = {
        category: document.getElementById('edit-category').value,
        amount: document.getElementById('edit-amount').value,
        date: document.getElementById('edit-date').value,
        description: document.getElementById('edit-description').value
    };
    
    console.log('Atualizando transação:', {id, data});
    
    try {
        const response = await fetch(`${API_BASE_URL}?id=${id}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        console.log('Resposta update:', result);
        
        if (result.success) {
            showNotification('Transação atualizada com sucesso', 'success');
            closeModal();
            loadTransactions();
        } else {
            showNotification(`Erro: ${result.error}`, 'error');
        }
    } catch (error) {
        console.error('Erro:', error);
        showNotification('Erro ao atualizar transação', 'error');
    }
}

// Filtrar
function handleFilter() {
    console.log('Filtrando...');
    
    const filters = {
        type: filterType.value,
        category: filterCategory.value.toLowerCase()
    };
    
    filteredTransactions = allTransactions.filter(transaction => {
        if (filters.type && transaction.type !== filters.type) return false;
        if (filters.category && !transaction.category.toLowerCase().includes(filters.category)) return false;
        
        const transDate = new Date(transaction.date);
        if (filterDateFrom.value) {
            const dateFrom = new Date(filterDateFrom.value);
            if (transDate < dateFrom) return false;
        }
        if (filterDateTo.value) {
            const dateTo = new Date(filterDateTo.value);
            if (transDate > dateTo) return false;
        }
        
        return true;
    });
    
    console.log('Resultado do filtro:', filteredTransactions);
    renderTransactions();
}

// Limpar filtros
function handleReset() {
    console.log('Limpando filtros');
    
    filterType.value = '';
    filterCategory.value = '';
    filterDateFrom.value = '';
    filterDateTo.value = '';
    
    filteredTransactions = [...allTransactions];
    renderTransactions();
}

// ===================================================
// UTILITÁRIOS
// ===================================================

// Formatar moeda
function formatCurrency(value) {
    return parseFloat(value).toLocaleString('pt-BR', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

// Formatar data
function formatDate(dateString) {
    const date = new Date(dateString + 'T00:00:00');
    return date.toLocaleDateString('pt-BR');
}

// Definir data como hoje
function setDateToday() {
    const today = new Date().toISOString().split('T')[0];
    if (dateInput) dateInput.value = today;
}

// Mostrar/ocultar loading
function showLoading(show) {
    if (loading) {
        loading.style.display = show ? 'flex' : 'none';
    }
}

// Notificação
function showNotification(message, type = 'info') {
    const notification = document.getElementById('notification');
    if (!notification) {
        console.error('notification não encontrado!');
        return;
    }
    
    notification.textContent = message;
    notification.className = `notification ${type}`;
    notification.style.display = 'block';
    
    setTimeout(() => {
        notification.style.display = 'none';
    }, 3000);
}