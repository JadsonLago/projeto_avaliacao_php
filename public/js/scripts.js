function validarFiltros() {
    const dtIni = document.getElementById('data_inicial').value;
    const dtFim = document.getElementById('data_final').value;

    // 1. Validação de preenchimento obrigatório dos dois campos
    if ((dtIni && !dtFim) || (!dtIni && dtFim)) {
        alert("Para filtrar por período, é obrigatório preencher a Data Inicial e a Data Final.");
        return false; // Impede o envio do formulário
    }

    // 2. Validação de datas absurdas (Ano limite)
    if (dtIni) {
        // O input date retorna no formato YYYY-MM-DD. Pegamos a primeira parte (Ano)
        const anoIni = parseInt(dtIni.split('-')[0]);
        if (anoIni < 2000 || anoIni > 2100) {
            alert("A Data Inicial informada é inválida ou fora do limite permitido (Anos 2000 a 2100).");
            return false;
        }
    }

    if (dtFim) {
        const anoFim = parseInt(dtFim.split('-')[0]);
        if (anoFim < 2000 || anoFim > 2100) {
            alert("A Data Final informada é inválida ou fora do limite permitido (Anos 2000 a 2100).");
            return false;
        }
    }

    // 3. Validação cronológica (Data Inicial não pode ser maior que a Final)
    if (dtIni && dtFim) {
        if (new Date(dtIni) > new Date(dtFim)) {
            alert("A Data Inicial não pode ser maior que a Data Final.");
            return false;
        }
    }

    return true; // Se passar em tudo, envia o formulário
}