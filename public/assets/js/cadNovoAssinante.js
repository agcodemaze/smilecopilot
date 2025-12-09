
// ===============================
// MASCARA TELEFONE
// ===============================
function maskTelefone(value) {
    value = value.replace(/\D/g, "");

    if (value.length > 11) value = value.substring(0, 11);

    if (value.length <= 10) {
        return value.replace(/(\d{2})(\d{4})(\d{0,4})/, "($1) $2-$3");
    } else {
        return value.replace(/(\d{2})(\d{5})(\d{0,4})/, "($1) $2-$3");
    }
}

document.getElementById("telefone").addEventListener("input", function () {
    this.value = maskTelefone(this.value);
});

// ===============================
// VALIDAÇÃO EM TEMPO REAL - EMAIL
// ===============================

function validarEmail(email) {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(String(email).toLowerCase());
}

document.getElementById("email").addEventListener("input", function () {
    if (!validarEmail(this.value)) {
        this.classList.add("is-invalid");
    } else {
        this.classList.remove("is-invalid");
    }
});

// ===============================
// VALIDAÇÃO EM TEMPO REAL - SENHA
// ===============================

const senha1 = document.getElementById("senha1");
const senha2 = document.getElementById("senha2");

function validarSenhaForte(senha) {
    const regras = {
        tamanho: senha.length >= 8 && senha.length <= 10,
        maiuscula: /[A-Z]/.test(senha),
        numero: /[0-9]/.test(senha),
        especial: /[\W_]/.test(senha)
    };

    // Atualiza visualmente as regras
    document.querySelector(".rule-tamanho").classList.toggle("text-success", regras.tamanho);
    document.querySelector(".rule-maiuscula").classList.toggle("text-success", regras.maiuscula);
    document.querySelector(".rule-numero").classList.toggle("text-success", regras.numero);
    document.querySelector(".rule-especial").classList.toggle("text-success", regras.especial);

    return regras.tamanho && regras.maiuscula && regras.numero && regras.especial;
}

// ESCUTA SENHA 1
senha1.addEventListener("input", function () {
    if (!validarSenhaForte(this.value)) {
        this.classList.add("is-invalid");
    } else {
        this.classList.remove("is-invalid");
    }

    // Verifica se a senha repetida ainda confere
    if (senha2.value.length > 0 && senha2.value !== senha1.value) {
        senha2.classList.add("is-invalid");
    } else {
        senha2.classList.remove("is-invalid");
    }
});

// ESCUTA SENHA 2
senha2.addEventListener("input", function () {
    if (this.value !== senha1.value) {
        this.classList.add("is-invalid");
    } else {
        this.classList.remove("is-invalid");
    }
});

// ===============================
// VALIDAR NOME COMPLETO (max 100)
// ===============================
document.getElementById("nome").addEventListener("input", function () {
    if (this.value.length > 100) {
        this.classList.add("is-invalid");
    } else {
        this.classList.remove("is-invalid");
    }
});

// ===============================
// WIZARD
// ===============================

let currentStep = 1;

function updateSteps() {
    document.querySelectorAll(".step").forEach(step => step.classList.add("d-none"));
    document.querySelector(`.step[data-step="${currentStep}"]`).classList.remove("d-none");

    document.querySelectorAll(".step-item").forEach(item => {
        item.classList.remove("active");
        if (parseInt(item.dataset.step) <= currentStep) item.classList.add("active");
    });
}
updateSteps();

function validateStep(step) {
    let valid = true;

    if (step === 1) {
        const email = document.getElementById("email");

        if (!validarEmail(email.value)) {
            email.classList.add("is-invalid");
            valid = false;
        }

        if (!validarSenhaForte(senha1.value)) {
            senha1.classList.add("is-invalid");
            valid = false;
        }

        if (senha1.value !== senha2.value) {
            senha2.classList.add("is-invalid");
            valid = false;
        }
    }

    if (step === 2) {
        const nome = document.getElementById("nome");
        const telefone = document.getElementById("telefone");

        if (nome.value.trim().length === 0 || nome.value.length > 100) {
            nome.classList.add("is-invalid");
            valid = false;
        }

        const t = telefone.value.replace(/\D/g, "");
        if (t.length < 10) {
            telefone.classList.add("is-invalid");
            valid = false;
        }
    }

    return valid;
}

document.querySelectorAll(".next").forEach(btn => {
    btn.addEventListener("click", function () {
        if (!validateStep(currentStep)) return;

        if (currentStep < 3) {
            currentStep++;
            updateSteps();
        }

        if (currentStep === 3) submitForm();
    });
});

document.querySelectorAll(".prev").forEach(btn => {
    btn.addEventListener("click", function () {
        if (currentStep > 1) {
            currentStep--;
            updateSteps();
        }
    });
});

// ===============================
// ENVIO DO FORMULÁRIO
// ===============================

function submitForm() {

    const payload = {
        email: document.getElementById("email").value,
        senha: document.getElementById("senha1").value,
        nome: document.getElementById("nome").value,
        telefone: document.getElementById("telefone").value
    };

    fetch('/cadUsuario', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
    })
    .then(async response => {
        const data = await response.text();
        console.log("Retorno API:", data);

        currentStep = 3;
        updateSteps();
    })
    .catch(err => {
        console.error('Erro no cadastro:', err);
        alert("Erro ao cadastrar. Tente novamente.");
    });
}


