// Variáveis globais
let catalogData = null;      
let currentProducts = [];    
let cart = [];              

// INICIALIZAÇÃO

document.addEventListener('DOMContentLoaded', function() {
    loadCatalog();
});

// CARREGAMENTO DO CATÁLOGO (AJAX)

/**
  Carrega o catálogo de produtos do arquivo JSON via AJAX
  Utiliza Fetch API para requisição assíncrona
 */
function loadCatalog() {
    const loadingSpinner = document.getElementById('loadingSpinner');
    const productsContainer = document.getElementById('productsContainer');
    
    if (loadingSpinner) loadingSpinner.classList.remove('hidden');
    if (productsContainer) productsContainer.innerHTML = '';
    
    fetch('catalog.json')
        .then(response => {
            if (!response.ok) {
                throw new Error('Erro ao carregar o catálogo');
            }
            return response.json();
        })
        .then(data => {
            catalogData = data;
            currentProducts = data.products;
            
            loadCategories(data.categories);
            loadProductsToSelect(data.products);
            displayProducts(data.products);
            
            // Inicializar os event listeners APENAS UMA VEZ
            setupFormValidation();
            setupLightboxAddToCart();
            
            if (loadingSpinner) loadingSpinner.classList.add('hidden');
        })
        .catch(error => {
            console.error('Erro:', error);
            if (loadingSpinner) {
                // Adicionado botão de "Tentar Novamente"
                loadingSpinner.innerHTML = `
                    <div class="alert alert-danger" role="alert">
                        <div class="d-flex align-items-center justify-content-center flex-column">
                            <p class="mb-2">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Erro ao carregar os produtos. Verifique sua conexão.
                            </p>
                            <button class="btn btn-outline-danger btn-sm" onclick="loadCatalog()">
                                <i class="fas fa-sync-alt me-2"></i>Tentar Novamente
                            </button>
                        </div>
                    </div>
                `;
            }
        });
}

// SISTEMA DE FILTROS

/**
 Carrega as categorias no dropdown de filtros
  @param {Array} categories - Array de categorias do JSON
 */
function loadCategories(categories) {
    const categoryFilter = document.getElementById('categoryFilter');
    if (!categoryFilter) return;
    
    // Limpa opções anteriores para evitar duplicação em caso de retry
    categoryFilter.innerHTML = '<option value="all">Todas as Categorias</option>';
    
    categories.forEach(category => {
        const option = document.createElement('option');
        option.value = category.id;
        option.textContent = category.name;
        categoryFilter.appendChild(option);
    });
    
    // Remove listeners antigos clonando o elemento (fallback de segurança)
    const newFilter = categoryFilter.cloneNode(true);
    categoryFilter.parentNode.replaceChild(newFilter, categoryFilter);
    
    // Event listener para mudança de categoria
    newFilter.addEventListener('change', function() {
        filterProducts(this.value);
    });
}

/**
 * Filtra produtos por categoria
 @param {string} categoryId - ID da categoria selecionada ou 'all'
 */
function filterProducts(categoryId) {
    if (categoryId === 'all') {
        currentProducts = catalogData.products;
    } else {
        currentProducts = catalogData.products.filter(
            product => product.category === categoryId
        );
    }
    
    displayProducts(currentProducts);
}

// EXIBIÇÃO DINÂMICA DE PRODUTOS

/**
 * Exibe os produtos na página
 @param {Array} products - Array de produtos a exibir
 */
function displayProducts(products) {
    const productsContainer = document.getElementById('productsContainer');
    if (!productsContainer) return;
    
    productsContainer.innerHTML = '';
    
    if (products.length === 0) {
        productsContainer.innerHTML = `
            <div class="col-12">
                <div class="empty-state text-center py-5">
                    <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                    <h3 class="text-muted">Nenhum produto encontrado</h3>
                    <p class="text-muted">Tente selecionar outra categoria.</p>
                </div>
            </div>
        `;
        return;
    }
    
    products.forEach(product => {
        const productCard = createProductCard(product);
        productsContainer.appendChild(productCard);
    });
}

/**
 * Cria um card HTML para um produto
 @param {Object} product - Objeto do produto
 * @returns {HTMLElement} Elemento div contendo o card
 */
function createProductCard(product) {
    const col = document.createElement('div');
    col.className = 'col';
    
    const category = catalogData.categories.find(cat => cat.id === product.category);
    const categoryName = category ? category.name : product.category;
    
    col.innerHTML = `
        <div class="card product-card h-100 shadow-sm">
            <img src="${product.image}" class="card-img-top" alt="${product.name}" 
                 onerror="this.src='src/images/Brasao.png'">
            <div class="card-body d-flex flex-column">
                <span class="badge bg-secondary mb-2 align-self-start">${categoryName}</span>
                <h5 class="card-title">${product.name}</h5>
                <p class="card-text flex-grow-1">${product.description}</p>
                <div class="product-price fs-4 fw-bold text-primary mb-3">${product.price}€</div>
                <button class="btn btn-warning w-100 mt-auto" onclick="openLightbox('${product.id}')">
                    <i class="fas fa-eye me-2"></i>Ver Detalhes
                </button>
            </div>
        </div>
    `;
    
    return col;
}

/**
 * Popula o select do formulário com os produtos disponíveis
@param {Array} products - Array de produtos
 */
function loadProductsToSelect(products) {
    const productSelect = document.getElementById('productSelect');
    if (!productSelect) return;
    
    // Limpar opções existentes (exceto a primeira)
    productSelect.innerHTML = '<option value="">Escolha um produto...</option>';

    products.forEach(product => {
        const option = document.createElement('option');
        option.value = product.id;
        option.textContent = `${product.name} - ${product.price}€`;
        option.dataset.price = product.price;
        option.dataset.name = product.name;
        productSelect.appendChild(option);
    });
}

// LIGHTBOX MODAL
/**
 * Abre o lightbox com os detalhes do produto
 @param {string} productId - ID do produto a exibir
 */
function openLightbox(productId) {
    const product = catalogData.products.find(p => p.id === productId);
    
    if (!product) return;
    
    const category = catalogData.categories.find(cat => cat.id === product.category);
    const categoryName = category ? category.name : product.category;
    
    document.getElementById('lightboxImage').src = product.image;
    document.getElementById('lightboxImage').alt = product.name;
    document.getElementById('lightboxTitle').textContent = product.name;
    document.getElementById('lightboxDescription').textContent = product.description;
    document.getElementById('lightboxCategory').textContent = categoryName;
    document.getElementById('lightboxPrice').textContent = product.price;
    
    // Define o ID do produto no input de quantidade
    const lightboxQuantity = document.getElementById('lightboxQuantity');
    if (lightboxQuantity) {
        lightboxQuantity.dataset.productId = productId;
        lightboxQuantity.value = 1; // Reset para 1
    }
    
    const lightbox = document.getElementById('lightboxModal');
    if (lightbox) {
        lightbox.classList.add('show');
        document.body.style.overflow = 'hidden';
    }
}

/**
 * Fecha o lightbox modal
 */
function closeLightbox() {
    const lightbox = document.getElementById('lightboxModal');
    if (lightbox) {
        lightbox.classList.remove('show');
        document.body.style.overflow = 'auto';
    }
}

// Event listeners para o lightbox
const lightboxModal = document.getElementById('lightboxModal');
if (lightboxModal) {
    lightboxModal.addEventListener('click', function(event) {
        if (event.target === this || event.target.classList.contains('lightbox-close')) {
            closeLightbox();
        }
    });
}

document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeLightbox();
    }
});

// SISTEMA DE CARRINHO DE COMPRAS

/**
 * Configura a validação e lógica do formulário principal
 * Previne duplicação de event listeners usando cloneNode
 */
function setupFormValidation() {
    const form = document.getElementById('calculatorForm');
    if (!form) return;

    // Remover event listeners anteriores (se existirem)
    const newForm = form.cloneNode(true);
    form.parentNode.replaceChild(newForm, form);

    newForm.addEventListener('submit', function(e) {
        e.preventDefault();
        e.stopPropagation();

        const productSelect = document.getElementById('productSelect');
        const quantityInput = document.getElementById('quantity');
        
        const productId = productSelect.value;
        const quantity = parseInt(quantityInput.value);

        if (!productId || quantity <= 0 || quantity > 100 || isNaN(quantity)) {
            newForm.classList.add('was-validated');
            return;
        }

        newForm.classList.remove('was-validated');

        const product = catalogData.products.find(p => p.id === productId);

        if (product) {
            addToCart(product, quantity);
            
            // Feedback visual
            showNotification(`${quantity}x ${product.name} adicionado ao carrinho!`);
            
            // Reset do formulário
            quantityInput.value = 1;
            productSelect.value = '';
            productSelect.classList.remove('is-valid', 'is-invalid');
            quantityInput.classList.remove('is-valid', 'is-invalid');
        }
    });
    
    // Validação visual dos inputs 
    const quantityInput = document.getElementById('quantity');
    if (quantityInput) {
        quantityInput.addEventListener('input', function() {
            const value = parseInt(this.value);
            if (value > 0 && value <= 100 && !isNaN(value)) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            } else {
                this.classList.remove('is-valid');
                this.classList.add('is-invalid');
            }
        });
    }
    
    const productSelect = document.getElementById('productSelect');
    if (productSelect) {
        productSelect.addEventListener('change', function() {
            if (this.value !== '') {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            } else {
                this.classList.remove('is-valid');
                this.classList.add('is-invalid');
            }
        });
    }
}

/**
 * Configura o formulário de adicionar ao carrinho dentro do lightbox
 * Usa a mesma técnica de cloneNode para evitar event listeners duplicados
 */
function setupLightboxAddToCart() {
    const form = document.getElementById('lightboxAddToCartForm');
    if (!form) return;

    // Remover event listeners anteriores (se existirem)
    const newForm = form.cloneNode(true);
    form.parentNode.replaceChild(newForm, form);

    newForm.addEventListener('submit', function(e) {
        e.preventDefault();
        e.stopPropagation();

        const quantityInput = document.getElementById('lightboxQuantity');
        const productId = quantityInput.dataset.productId;
        const quantity = parseInt(quantityInput.value);

        if (!productId || quantity <= 0 || quantity > 100 || isNaN(quantity)) {
            newForm.classList.add('was-validated');
            return;
        }

        newForm.classList.remove('was-validated');

        const product = catalogData.products.find(p => p.id === productId);

        if (product) {
            addToCart(product, quantity);
            showNotification(`${quantity}x ${product.name} adicionado ao carrinho!`);
            closeLightbox();
        }
    });
}

/**
 * Adiciona um produto ao carrinho ou incrementa a quantidade se já existir
 * Limite máximo de 100 unidades por produto
  @param {Object} product - Objeto do produto a adicionar
  @param {number} quantity - Quantidade a adicionar
 */
function addToCart(product, quantity) {
    const productId = product.id;
    const existingItemIndex = cart.findIndex(item => item.id === productId);

    if (existingItemIndex > -1) {
        // Se o item já existe, verifica se não ultrapassa 100 unidades
        const newQuantity = cart[existingItemIndex].quantity + quantity;
        
        if (newQuantity > 100) {
            showNotification(`⚠️ Limite máximo de 100 unidades por produto! Você já tem ${cart[existingItemIndex].quantity} unidades no carrinho.`);
            return;
        }
        
        cart[existingItemIndex].quantity = newQuantity;
    } else {
        // Se é novo, adiciona ao carrinho
        cart.push({
            id: productId,
            name: product.name,
            price: parseFloat(product.price),
            quantity: quantity
        });
    }
    
    updateCartDisplay();
}

/**
 * Atualiza a exibição do carrinho de compras
 * Calcula totais e gera a lista de itens dinamicamente
 */
function updateCartDisplay() {
    const cartList = document.getElementById('cartItemsList');
    const totalResultDiv = document.getElementById('totalResult');
    const resultTotalSpan = document.getElementById('resultTotal');
    const resultProductSpan = document.getElementById('resultProduct');
    
    if (!cartList || !totalResultDiv) return;
    
    cartList.innerHTML = '';

    if (cart.length === 0) {
        cartList.innerHTML = '<li class="list-group-item text-muted text-center">Nenhum item adicionado.</li>';
        totalResultDiv.classList.add('d-none');
        return;
    }
    
    let totalValue = 0;
    let totalItems = 0;
    
    cart.forEach((item, index) => {
        const itemTotal = item.price * item.quantity;
        totalValue += itemTotal;
        totalItems += item.quantity;
        
        const listItem = document.createElement('li');
        listItem.className = 'list-group-item d-flex justify-content-between align-items-center';
        listItem.innerHTML = `
            <div class="flex-grow-1">
                ${item.name} <small class="text-muted">(${item.quantity} x ${item.price.toFixed(2)}€)</small>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-warning text-dark rounded-pill">${itemTotal.toFixed(2)}€</span>
                <button class="btn btn-sm btn-outline-danger border-0" onclick="removeFromCart(${index})" title="Remover item">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </div>
        `;
        cartList.appendChild(listItem);
    });

    totalResultDiv.classList.remove('d-none');
    if (resultTotalSpan) resultTotalSpan.textContent = totalValue.toFixed(2);
    if (resultProductSpan) resultProductSpan.textContent = `${cart.length} Produtos Distintos (${totalItems} Unidades)`;
}

/**
 * Exibe notificação temporária de feedback ao usuário
 * @param {string} message - Mensagem a exibir
 */
function showNotification(message) {
    // Criar notificação temporária
    const notification = document.createElement('div');
    notification.className = 'alert alert-success position-fixed top-0 start-50 translate-middle-x mt-3 shadow';
    notification.style.zIndex = '9999';
    notification.innerHTML = `<i class="fas fa-check-circle me-2"></i>${message}`;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.classList.add('fade');
        setTimeout(() => notification.remove(), 500);
    }, 3000);
}

/**
 * Remove uma unidade do produto do carrinho
 * Se quantidade > 1: decrementa
 * Se quantidade = 1: remove completamente
 @param {number} index - Índice do item no array do carrinho
 */
function removeFromCart(index) {
    if (index >= 0 && index < cart.length) {
        const item = cart[index];
        
        if (item.quantity > 1) {
            // Se tiver mais de 1 unidade, diminui a quantidade
            cart[index].quantity -= 1;
            showNotification(`Removida 1 unidade de ${item.name}. Restam ${cart[index].quantity}.`);
        } else {
            // Se tiver apenas 1 unidade, remove completamente
            cart.splice(index, 1);
            showNotification(`${item.name} removido completamente do carrinho!`);
        }
        
        updateCartDisplay();
    }
}

// SISTEMA DE FINALIZAÇÃO DE PEDIDO

/**
 * Abre o modal de confirmação do pedido
 * Preenche o resumo com os itens do carrinho
 */
function finalizePurchase() {
    if (cart.length === 0) {
        showNotification('⚠️ Seu carrinho está vazio!');
        return;
    }
    
    // Preencher o resumo do pedido no modal
    const orderSummaryList = document.getElementById('orderSummaryList');
    const orderTotalItems = document.getElementById('orderTotalItems');
    const orderTotalValue = document.getElementById('orderTotalValue');
    
    orderSummaryList.innerHTML = '';
    let totalValue = 0;
    let totalItems = 0;
    
    cart.forEach(item => {
        const itemTotal = item.price * item.quantity;
        totalValue += itemTotal;
        totalItems += item.quantity;
        
        const listItem = document.createElement('li');
        listItem.className = 'list-group-item d-flex justify-content-between align-items-center';
        listItem.innerHTML = `
            <div>
                <strong>${item.name}</strong>
                <br>
                <small class="text-muted">${item.quantity} x ${item.price.toFixed(2)}€</small>
            </div>
            <span class="badge bg-warning text-dark rounded-pill fs-6">${itemTotal.toFixed(2)}€</span>
        `;
        orderSummaryList.appendChild(listItem);
    });
    
    orderTotalItems.textContent = totalItems;
    orderTotalValue.textContent = totalValue.toFixed(2);
    
    // Abrir o modal usando Bootstrap
    const modal = new bootstrap.Modal(document.getElementById('orderConfirmationModal'));
    modal.show();
}

/**
 * Confirma o pedido e limpa o carrinho
 * Simula o envio do pedido
 */
function confirmOrder() {
    // Fechar o modal
    const modalElement = document.getElementById('orderConfirmationModal');
    const modal = bootstrap.Modal.getInstance(modalElement);
    modal.hide();
    
    // Guardar informação do pedido para exibir
    const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
    const totalValue = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    
    // Limpar o carrinho
    cart = [];
    updateCartDisplay();
    
    // Mostrar mensagem de sucesso com Refresh automático
    showSuccessMessage(totalItems, totalValue);
}

/**
 * Exibe mensagem de sucesso após confirmação do pedido
 * Força o scroll para o topo ao recarregar
  @param {number} totalItems - Total de itens do pedido
  @param {number} totalValue - Valor total do pedido
 */
function showSuccessMessage(totalItems, totalValue) {
    const successHTML = `
        <div class="alert alert-success alert-dismissible fade show position-fixed top-50 start-50 translate-middle shadow-lg" 
             style="z-index: 10000; min-width: 400px; max-width: 90%; transition: opacity 0.3s ease;" role="alert">
            <h4 class="alert-heading text-center">
                <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                <br>Pedido Confirmado!
            </h4>
            <hr>
            <p class="mb-2">
                <i class="fas fa-box me-2"></i><strong>Total de produtos:</strong> ${totalItems} unidades
            </p>
            <p class="mb-0">
                <i class="fas fa-euro-sign me-2"></i><strong>Valor total:</strong> ${totalValue.toFixed(2)}€
            </p>
            <button type="button" class="btn-close position-absolute top-0 end-0 m-2"></button>
        </div>
        <div class="position-fixed top-0 start-0 w-100 h-100 bg-dark" 
             style="z-index: 9999; opacity: 0.5; transition: opacity 0.3s ease;"></div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', successHTML);

    // Função auxiliar para realizar o refresh forçando o topo
    const refreshToTop = () => {
        const alert = document.querySelector('.alert-success');
        const overlay = document.querySelector('.bg-dark.position-fixed');
        
        if (alert) alert.style.opacity = '0';
        if (overlay) overlay.style.opacity = '0';
        
        setTimeout(() => {
            if (alert) alert.remove();
            if (overlay) overlay.remove();
            
            // Força o scroll visual para o topo imediatamente
            window.scrollTo(0, 0);
            
            // Impede o navegador de restaurar a posição do scroll ao recarregar
            if ('scrollRestoration' in history) {
                history.scrollRestoration = 'manual';
            }
            
            // Recarrega a página
            location.reload();
        }, 300);
    };
    
    // Remover automaticamente após 5 segundos
    setTimeout(() => {
        // Verifica se os elementos ainda existem antes de tentar removê-los
        if (document.querySelector('.alert-success')) {
            refreshToTop();
        }
    }, 5000);
    
    // Evento para o botão de fechar (X)
    const closeButton = document.querySelector('.alert-success .btn-close');
    if (closeButton) {
        closeButton.addEventListener('click', function(e) {
            e.preventDefault();
            refreshToTop();
        });
    }
    
    // Evento para clicar no fundo escuro (overlay)
    const overlay = document.querySelector('.bg-dark.position-fixed');
    if (overlay) {
        overlay.addEventListener('click', function(e) {
            e.preventDefault();
            refreshToTop();
        });
    }
}

/**
 * Limpa todos os itens do carrinho
 * Pede confirmação antes de limpar
 */
function clearCart() {
    if (cart.length === 0) {
        showNotification('⚠️ O carrinho já está vazio!');
        return;
    }
    
    if (confirm('Tem certeza que deseja limpar todo o carrinho?')) {
        cart = [];
        updateCartDisplay();
        showNotification('🗑️ Carrinho limpo com sucesso!');
    }
}