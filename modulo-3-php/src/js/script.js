// VARIÁVEIS GLOBAIS

// --- INICIALIZAÇÃO ---
document.addEventListener('DOMContentLoaded', function() {
    setupFormValidation();
    setupLightboxAddToCart();
});

// --- SISTEMA DE UI E LIGHTBOX (Visualização do Produto) ---

/**
 * Abre o lightbox com os detalhes do produto.
 * Recebe os dados diretamente do HTML (loja.php)
 */
function openLightbox(productId, name, description, price, image, category) {
    // Atualiza os elementos visuais do modal
    const imgElement = document.getElementById('lightboxImage');
    if (imgElement) {
        imgElement.src = image;
        imgElement.alt = name;
    }

    setTextContent('lightboxTitle', name);
    setTextContent('lightboxDescription', description);
    setTextContent('lightboxCategory', category);
    setTextContent('lightboxPrice', '€' + parseFloat(price).toFixed(2));
    
    // Configura o input de quantidade escondido ou visível para saber qual produto adicionar
    const lightboxQuantity = document.getElementById('lightboxQuantity');
    if (lightboxQuantity) {
        lightboxQuantity.dataset.productId = productId; // Guarda o ID para usar no botão Adicionar
        lightboxQuantity.value = 1; // Reseta a quantidade para 1
    }
    
    // Mostra o modal
    const lightbox = document.getElementById('lightboxModal');
    if (lightbox) {
        lightbox.classList.add('show');
        document.body.style.overflow = 'hidden'; 
    }
}

// Função auxiliar para preencher texto com segurança
function setTextContent(id, text) {
    const el = document.getElementById(id);
    if (el) el.textContent = text;
}

function closeLightbox() {
    const lightbox = document.getElementById('lightboxModal');
    if (lightbox) {
        lightbox.classList.remove('show');
        document.body.style.overflow = 'auto'; 
    }
}

// Fechar lightbox com cliques fora da janela
document.addEventListener('click', (e) => {
    if (e.target.id === 'lightboxModal') closeLightbox();
});

// Fechar lightbox com a tecla ESC
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeLightbox();
});


// --- INTEGRAÇÃO COM PHP  ---

function setupLightboxAddToCart() {
    const btn = document.getElementById('btnLightboxAdd');
    if (!btn) return;
    
    btn.addEventListener('click', () => {
        const input = document.getElementById('lightboxQuantity');
        
        if (input) {
            // Pega o ID e a Quantidade
            const productId = input.dataset.productId;
            // Se o valor for inválido, assume 1
            const quantity = parseInt(input.value) || 1; 
            
            window.location.href = 'cart.php?action=add&id=' + productId + '&qty=' + quantity;
        }
    });
}

// --- UTILITÁRIOS ---

function setupFormValidation() {
    // Garante que ninguém digite números negativos ou zero nos inputs de quantidade
    const qtyInputs = document.querySelectorAll('input[type="number"]');
    qtyInputs.forEach(input => {
        input.addEventListener('change', function() {
            if (this.value < 1) this.value = 1;
            if (this.value > 100) this.value = 100; // Limite opcional de estoque
        });
    });
}