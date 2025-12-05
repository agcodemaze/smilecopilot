function confirmAndSubmit(event) {
    event.preventDefault();

    let btn = event.currentTarget;

    let formId = btn.dataset.form;
    let url = btn.dataset.url;
    let titulo = btn.dataset.title || "Confirmação";
    let mensagem = btn.dataset.msg || "Deseja continuar?";

    let form = document.getElementById(formId);

    if (!form) {
        console.error("Formulário não encontrado:", formId);
        Swal.fire("Erro", "Formulário não encontrado.", "error");
        return;
    }

    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    Swal.fire({
        title: titulo,
        text: mensagem,
        icon: 'question',
        showDenyButton: true,
        confirmButtonText: 'CONFIRMAR',
        denyButtonText: 'CANCELAR',
        confirmButtonColor: "#0cadc2ff",
        denyButtonColor: "#fa7575ff",
        background: "#ffffffff",
        color: "#82929bff",
        width: '420px'
    }).then((result) => {

        if (result.isConfirmed) {

            Swal.fire({
                title: 'Salvando...',
                text: 'Aguarde enquanto processamos.',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                },
                background: "#ffffffff",
                color: "#82929bff",
                width: '420px'
            });

            let formData = new FormData(form);

            $.ajax({
                url: url,
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                dataType: "json",

                success: function(response) {
                    Swal.close();

                    if (response.success === true) {

                        Swal.fire({
                            title: 'Sucesso!',
                            text: response.message,
                            icon: 'success',
                            width: '420px',
                            confirmButtonColor: "#0cadc2ff",
                            background: "#ffffffff",
                            color: "#82929bff"
                        }).then(() => {
                            location.reload();
                        });

                    } else {
                        Swal.fire({
                            title: 'Erro!',
                            text: response.message, 
                            icon: 'error',
                            width: '420px',
                            confirmButtonColor: "#0cadc2ff",
                            background: "#ffffffff",
                            color: "#82929bff"
                        });
                    }
                },

                error: function(xhr, status, error) {
                    Swal.close();
                    Swal.fire({
                        title: 'Erro!',
                        text: 'Falha ao enviar dados: ' + error,
                        icon: 'error',
                        width: '420px',
                        confirmButtonColor: "#0cadc2ff",
                        background: "#ffffffff",
                        color: "#82929bff"
                    });
                }
            });

        } else {
            Swal.fire({
                title: 'Cancelado',
                text: 'Nenhuma ação realizada.',
                icon: 'info',
                width: '420px',
                confirmButtonColor: "#0cadc2ff",
                background: "#ffffffff",
                color: "#82929bff"
            }).then(() => {
                location.reload();
            });
        }
    });
}

$(document).ready(function () {
    $(".btn-sweet").on("click", confirmAndSubmit);
});
