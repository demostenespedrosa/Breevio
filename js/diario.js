document.addEventListener('DOMContentLoaded', function() {
    // Elementos do DOM
    const form = document.getElementById('formDiario');
    const sections = document.querySelectorAll('.diary-section');
    const btnProximo = document.getElementById('btn-proximo');
    const btnVoltar = document.getElementById('btn-voltar');
    const alertContainer = document.getElementById('alert-container');

    // Estado do formulário
    let currentStep = 0;
    const formData = {
        humor: null,
        gatilhos: [],
        energia: null,
        conquista: null,
        texto: ''
    };

    // Função para mostrar alerta
    function showAlert(message, type = 'info') {
        const alert = document.createElement('div');
        alert.className = `alert alert-${type} alert-dismissible fade show`;
        alert.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        `;
        alertContainer.appendChild(alert);
        setTimeout(() => alert.remove(), 5000);
    }

    // Função para atualizar botões de navegação
    function updateNavigationButtons() {
        btnVoltar.style.display = currentStep > 0 ? 'block' : 'none';
        btnProximo.textContent = currentStep === sections.length - 1 ? 'Salvar' : 'Próximo';
    }

    // Função para validar seção atual
    function validateCurrentStep() {
        const section = sections[currentStep];
        let isValid = true;

        switch(currentStep) {
            case 0: // Humor
                if (!formData.humor) {
                    showAlert('Por favor, selecione seu humor', 'warning');
                    isValid = false;
                }
                break;
            case 1: // Gatilhos
                if (formData.gatilhos.length === 0) {
                    showAlert('Por favor, selecione pelo menos um gatilho', 'warning');
                    isValid = false;
                }
                break;
            case 2: // Energia
                if (!formData.energia) {
                    showAlert('Por favor, selecione seu nível de energia', 'warning');
                    isValid = false;
                }
                break;
            case 3: // Conquista
                if (!formData.conquista) {
                    showAlert('Por favor, selecione uma conquista', 'warning');
                    isValid = false;
                }
                break;
        }

        return isValid;
    }

    // Função para salvar diário
    async function saveDiary() {
        try {
            const response = await fetch('salvar_diario.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(formData)
            });

            const data = await response.json();

            if (data.success) {
                showAlert('Diário salvo com sucesso!', 'success');
                setTimeout(() => window.location.href = 'tela_diario.php', 1500);
            } else {
                showAlert(data.message || 'Erro ao salvar diário', 'danger');
            }
        } catch (error) {
            showAlert('Erro ao salvar diário. Tente novamente.', 'danger');
        }
    }

    // Event Listeners para seleção de humor
    document.querySelectorAll('.mood-card').forEach(card => {
        card.addEventListener('click', function() {
            document.querySelectorAll('.mood-card').forEach(c => c.classList.remove('selected'));
            this.classList.add('selected');
            formData.humor = parseInt(this.dataset.humor);
            
            // Feedback tátil
            if (navigator.vibrate) {
                navigator.vibrate(50);
            }
        });
    });

    // Event Listeners para seleção de gatilhos
    document.querySelectorAll('.trigger-pill').forEach(pill => {
        pill.addEventListener('click', function() {
            const gatilho = this.dataset.gatilho;
            
            if (gatilho === 'outro') {
                document.getElementById('outro-gatilho').style.display = 'block';
                return;
            }

            this.classList.toggle('selected');
            
            if (this.classList.contains('selected')) {
                formData.gatilhos.push(gatilho);
            } else {
                formData.gatilhos = formData.gatilhos.filter(g => g !== gatilho);
            }

            // Feedback tátil
            if (navigator.vibrate) {
                navigator.vibrate(50);
            }
        });
    });

    // Event Listener para input de outro gatilho
    document.getElementById('outro-gatilho-input').addEventListener('input', function() {
        const outroGatilho = this.value.trim();
        formData.gatilhos = formData.gatilhos.filter(g => g !== 'outro');
        
        if (outroGatilho) {
            formData.gatilhos.push(outroGatilho);
        }
    });

    // Event Listeners para seleção de energia
    document.querySelectorAll('.energy-card').forEach(card => {
        card.addEventListener('click', function() {
            document.querySelectorAll('.energy-card').forEach(c => c.classList.remove('selected'));
            this.classList.add('selected');
            formData.energia = this.dataset.energia;
            
            // Feedback tátil
            if (navigator.vibrate) {
                navigator.vibrate(50);
            }
        });
    });

    // Event Listeners para seleção de conquista
    document.querySelectorAll('.achievement-card').forEach(card => {
        card.addEventListener('click', function() {
            document.querySelectorAll('.achievement-card').forEach(c => c.classList.remove('selected'));
            this.classList.add('selected');
            formData.conquista = this.dataset.conquista;
            
            // Feedback tátil
            if (navigator.vibrate) {
                navigator.vibrate(50);
            }
        });
    });

    // Event Listener para texto opcional
    document.getElementById('texto-opcional').addEventListener('input', function() {
        formData.texto = this.value.trim();
    });

    // Event Listeners para botões de navegação
    btnProximo.addEventListener('click', function() {
        if (!validateCurrentStep()) return;

        if (currentStep === sections.length - 1) {
            saveDiary();
        } else {
            sections[currentStep].style.display = 'none';
            currentStep++;
            sections[currentStep].style.display = 'block';
            updateNavigationButtons();
        }
    });

    btnVoltar.addEventListener('click', function() {
        sections[currentStep].style.display = 'none';
        currentStep--;
        sections[currentStep].style.display = 'block';
        updateNavigationButtons();
    });

    // Inicialização
    sections.forEach((section, index) => {
        if (index !== 0) section.style.display = 'none';
    });
    updateNavigationButtons();

    // Suporte a gestos de swipe
    let touchStartX = 0;
    let touchEndX = 0;

    form.addEventListener('touchstart', e => {
        touchStartX = e.changedTouches[0].screenX;
    }, false);

    form.addEventListener('touchend', e => {
        touchEndX = e.changedTouches[0].screenX;
        handleSwipe();
    }, false);

    function handleSwipe() {
        const swipeThreshold = 50;
        const diff = touchStartX - touchEndX;

        if (Math.abs(diff) < swipeThreshold) return;

        if (diff > 0 && currentStep < sections.length - 1) {
            // Swipe left - próximo
            btnProximo.click();
        } else if (diff < 0 && currentStep > 0) {
            // Swipe right - voltar
            btnVoltar.click();
        }
    }
}); 