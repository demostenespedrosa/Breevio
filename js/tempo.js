// Função para atualizar o tempo em tempo real
function atualizarTempo() {
    // Obter a data e hora de parar do elemento data-attribute
    const dataParar = new Date(document.getElementById('tempo-sem-fumar').dataset.dataParar + ' ' + document.getElementById('tempo-sem-fumar').dataset.horaParar);
    const agora = new Date();
    
    // Calcular a diferença
    const diff = agora - dataParar;
    
    // Converter para dias, horas e minutos
    const dias = Math.floor(diff / (1000 * 60 * 60 * 24));
    const horas = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutos = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
    
    // Calcular dias fracionados para as metas (usado apenas para cálculos)
    const diasFracionados = dias + (horas / 24) + (minutos / 1440);
    
    // Formatar o texto para exibição
    let tempoTexto = '';
    if (dias > 0) {
        tempoTexto += `${dias} ${dias === 1 ? 'dia' : 'dias'}`;
        if (horas > 0 || minutos > 0) {
            tempoTexto += ', ';
        }
    }
    if (horas > 0) {
        tempoTexto += `${horas} ${horas === 1 ? 'hora' : 'horas'}`;
        if (minutos > 0) {
            tempoTexto += ' e ';
        }
    }
    if (minutos > 0) {
        tempoTexto += `${minutos} ${minutos === 1 ? 'minuto' : 'minutos'}`;
    }
    
    // Atualizar os elementos na página
    document.getElementById('dias-sem-fumar').textContent = tempoTexto;
    if (document.getElementById('horas-sem-fumar')) {
        document.getElementById('horas-sem-fumar').textContent = horas;
    }
    if (document.getElementById('minutos-sem-fumar')) {
        document.getElementById('minutos-sem-fumar').textContent = minutos;
    }
    
    // Atualizar o progresso
    const progresso = Math.min((diasFracionados / 30) * 100, 100);
    const progressBar = document.querySelector('.progress-bar');
    if (progressBar) {
        progressBar.style.width = `${progresso}%`;
    }

    // Atualizar estado das metas
    atualizarMetas(diasFracionados);

    // Atualizar dados do progresso geral
    if (document.getElementById('progresso-geral')) {
        atualizarProgressoGeral(diasFracionados);
    }
}

// Função para formatar dias em texto amigável
function formatarTempoMeta(dias) {
    const diasInt = Math.floor(dias);
    const horas = Math.floor((dias - diasInt) * 24);
    const minutos = Math.floor(((dias - diasInt) * 24 - horas) * 60);
    
    let texto = '';
    if (diasInt > 0) {
        texto += `${diasInt} ${diasInt === 1 ? 'dia' : 'dias'}`;
        if (horas > 0 || minutos > 0) {
            texto += ', ';
        }
    }
    if (horas > 0) {
        texto += `${horas} ${horas === 1 ? 'hora' : 'horas'}`;
        if (minutos > 0) {
            texto += ' e ';
        }
    }
    if (minutos > 0) {
        texto += `${minutos} ${minutos === 1 ? 'minuto' : 'minutos'}`;
    }
    return texto;
}

// Função para atualizar o estado das metas
function atualizarMetas(dias) {
    // Selecionar todas as metas
    const metas = document.querySelectorAll('.meta-card');
    
    metas.forEach(meta => {
        const diasMeta = parseFloat(meta.dataset.dias);
        const status = meta.classList.contains('conquista-alcancada') ? 'conquista-alcancada' : 'conquista-pendente';
        
        // Atualizar o contador de dias atual
        const diasAtual = meta.querySelector('.dias-atual');
        if (diasAtual) {
            diasAtual.textContent = document.getElementById('dias-sem-fumar').textContent;
        }
        
        // Atualizar a barra de progresso
        const progressBar = meta.querySelector('.progress-bar');
        if (progressBar) {
            const progresso = Math.min((dias / diasMeta) * 100, 100);
            progressBar.style.width = `${progresso}%`;
        }
        
        // Se atingiu a meta e ainda não está marcada como conquistada
        if (dias >= diasMeta && status === 'conquista-pendente') {
            // Atualizar classes
            meta.classList.remove('conquista-pendente');
            meta.classList.add('conquista-alcancada');
            
            // Atualizar ícone
            const icone = meta.querySelector('i');
            if (icone) {
                icone.classList.remove('text-muted');
                icone.classList.add('text-success');
            }
            
            // Verificar se já existe o ícone de check
            let checkIcon = meta.querySelector('.bi-check-circle-fill');
            if (!checkIcon) {
                // Adicionar ícone de check
                checkIcon = document.createElement('i');
                checkIcon.className = 'bi bi-check-circle-fill text-success fs-4 ms-3';
                meta.querySelector('.flex-grow-1').after(checkIcon);
            }
            
            // Atualizar barra de progresso
            if (progressBar) {
                progressBar.style.width = '100%';
                progressBar.classList.add('bg-success');
            }
        }
    });
}

// Função para atualizar os dados do progresso geral
function atualizarProgressoGeral(dias) {
    // Obter dados do usuário dos elementos data-attributes
    const cigarrosPorDia = parseInt(document.getElementById('progresso-geral').dataset.cigarrosPorDia);
    const precoCarteira = parseFloat(document.getElementById('progresso-geral').dataset.precoCarteira);
    const cigarrosPorCarteira = parseInt(document.getElementById('progresso-geral').dataset.cigarrosPorCarteira);

    // Calcular cigarros evitados
    const cigarrosEvitados = Math.floor(cigarrosPorDia * dias);
    
    // Calcular economia
    const precoPorCigarro = precoCarteira / cigarrosPorCarteira;
    const economiaTotal = cigarrosEvitados * precoPorCigarro;

    // Calcular dias inteiros
    const dataParar = new Date(document.getElementById('tempo-sem-fumar').dataset.dataParar + ' ' + document.getElementById('tempo-sem-fumar').dataset.horaParar);
    const agora = new Date();
    const diff = agora - dataParar;
    const diasInteiros = Math.floor(diff / (1000 * 60 * 60 * 24));

    // Atualizar elementos na página
    document.getElementById('dias-progresso').textContent = diasInteiros;
    document.getElementById('cigarros-evitados').textContent = cigarrosEvitados.toLocaleString('pt-BR');
    document.getElementById('economia-total').textContent = `R$ ${economiaTotal.toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
}

// Atualizar a cada segundo
setInterval(atualizarTempo, 1000);

// Atualizar imediatamente ao carregar a página
document.addEventListener('DOMContentLoaded', atualizarTempo); 