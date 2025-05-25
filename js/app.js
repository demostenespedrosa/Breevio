// Gerenciamento do tema claro/escuro
function toggleDarkMode() {
    const body = document.body;
    body.classList.toggle('dark-mode');
    const isDarkMode = body.classList.contains('dark-mode');
    localStorage.setItem('darkMode', isDarkMode);
}

// Carregar preferência de tema
function loadThemePreference() {
    const isDarkMode = localStorage.getItem('darkMode') === 'true';
    if (isDarkMode) {
        document.body.classList.add('dark-mode');
    }
}

// Registrar recaída via Ajax
function registrarRecaida(motivo) {
    fetch('php/registrar_recaida.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            motivo: motivo,
            data: new Date().toISOString().split('T')[0]
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Recaída registrada com sucesso!');
            location.reload();
        } else {
            alert('Erro ao registrar recaída: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao registrar recaída');
    });
}

// Obter mensagem motivacional do dia
function obterMensagemMotivacional() {
    console.log('Buscando mensagem motivacional...');
    fetch('php/mensagem_do_dia.php')
        .then(response => {
            console.log('Resposta recebida:', response);
            return response.json();
        })
        .then(data => {
            console.log('Dados recebidos:', data);
            if (data.success) {
                const mensagemElement = document.getElementById('mensagem-motivacional');
                if (mensagemElement) {
                    mensagemElement.textContent = data.mensagem;
                    console.log('Mensagem atualizada:', data.mensagem);
                } else {
                    console.error('Elemento mensagem-motivacional não encontrado');
                }
            } else {
                console.error('Erro ao obter mensagem:', data.message);
            }
        })
        .catch(error => {
            console.error('Erro na requisição:', error);
        });
}

// Calcular economia
function calcularEconomia(cigarrosPorDia, precoCarteira, diasSemFumar) {
    const cigarrosPorCarteira = 20; // Padrão
    const economiaPorDia = (cigarrosPorDia / cigarrosPorCarteira) * precoCarteira;
    return (economiaPorDia * diasSemFumar).toFixed(2);
}

// Gerenciamento de tema
document.addEventListener('DOMContentLoaded', () => {
    // Verificar preferência de tema salva
    const savedTheme = localStorage.getItem('theme') || 'light';
    document.documentElement.setAttribute('data-theme', savedTheme);
    
    // Configurar botão de tema
    const themeToggle = document.getElementById('theme-toggle');
    if (themeToggle) {
        themeToggle.addEventListener('click', () => {
            const currentTheme = document.documentElement.getAttribute('data-theme');
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            
            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            
            // Atualizar ícone
            const icon = themeToggle.querySelector('i');
            icon.className = newTheme === 'light' ? 'bi bi-moon-stars' : 'bi bi-sun';
        });
    }

    // Adicionar classe para animações de entrada
    document.querySelectorAll('.card, .list-group-item').forEach(element => {
        element.classList.add('fade-in');
    });

    // Configurar tooltips do Bootstrap
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Adicionar comportamento de swipe para cards
    let touchStartX = 0;
    let touchEndX = 0;
    
    document.querySelectorAll('.card').forEach(card => {
        card.addEventListener('touchstart', e => {
            touchStartX = e.changedTouches[0].screenX;
        }, false);
        
        card.addEventListener('touchend', e => {
            touchEndX = e.changedTouches[0].screenX;
            handleSwipe(card);
        }, false);
    });
    
    function handleSwipe(card) {
        const swipeThreshold = 50;
        const diff = touchEndX - touchStartX;
        
        if (Math.abs(diff) > swipeThreshold) {
            if (diff > 0) {
                // Swipe direito
                card.style.transform = 'translateX(0)';
            } else {
                // Swipe esquerdo
                card.style.transform = 'translateX(-100%)';
            }
        }
    }

    // Adicionar feedback tátil
    document.querySelectorAll('.btn, .nav-link').forEach(element => {
        element.addEventListener('click', () => {
            if ('vibrate' in navigator) {
                navigator.vibrate(50);
            }
        });
    });

    // Melhorar acessibilidade
    document.querySelectorAll('button, a').forEach(element => {
        if (!element.getAttribute('aria-label')) {
            const text = element.textContent.trim();
            if (text) {
                element.setAttribute('aria-label', text);
            }
        }
    });

    // Adicionar suporte a gestos de zoom
    let scale = 1;
    document.addEventListener('gesturestart', e => {
        e.preventDefault();
    });
    
    document.addEventListener('gesturechange', e => {
        e.preventDefault();
        scale = e.scale;
        document.body.style.transform = `scale(${scale})`;
    });
    
    document.addEventListener('gestureend', e => {
        e.preventDefault();
        document.body.style.transform = '';
    });

    // Carregar tema
    loadThemePreference();

    // Configurar modal de recaída
    const recaidaModal = document.getElementById('recaidaModal');
    if (recaidaModal) {
        const form = recaidaModal.querySelector('form');
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const motivo = this.querySelector('#motivo').value;
            registrarRecaida(motivo);
            bootstrap.Modal.getInstance(recaidaModal).hide();
        });
    }

    // Carregar mensagem motivacional na dashboard
    if (document.getElementById('mensagem-motivacional')) {
        obterMensagemMotivacional();
    }

    // Gerenciar formulário de recaída
    const formRecaida = document.getElementById('formRecaida');
    if (formRecaida) {
        formRecaida.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (!confirm('Tem certeza que deseja registrar uma recaída? Isso reiniciará sua contagem.')) {
                return;
            }
            
            const data = {
                data_recaida: document.getElementById('data_recaida').value,
                hora_recaida: document.getElementById('hora_recaida').value,
                motivo: document.getElementById('motivo').value
            };

            // Verificar se todos os campos foram preenchidos
            if (!data.data_recaida || !data.hora_recaida || !data.motivo) {
                alert('Por favor, preencha todos os campos.');
                return;
            }
            
            fetch('php/registrar_recaida.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    alert('Erro ao registrar recaída: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao registrar recaída. Tente novamente.');
            });
        });
    }
}); 