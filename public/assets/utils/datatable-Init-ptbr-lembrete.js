    $(document).ready(function() {
        "use strict";

        $("#alternative-page-datatable-lembrete").DataTable({
        pagingType: "full_numbers",
        pageLength: 50,
        columnDefs: [
            { targets: [0, -1], orderable: false }, 
            { targets: 2, type: 'string', orderData: 2 } 
        ],
        order: [[2, 'asc']], 
        drawCallback: function() {
            $(".dataTables_paginate > .pagination").addClass("pagination-rounded");
        },
        language: {
            processing:     "Processando...",
            search:         "Pesquisar:",
            lengthMenu:     "Mostrar _MENU_ pacientes",
            info:           "Mostrando _START_ até _END_ de _TOTAL_ pacientes",
            infoEmpty:      "Mostrando 0 até 0 de 0 pacientes",
            infoFiltered:   "(filtrado de _MAX_ pacientes no total)",
            infoPostFix:    "",
            loadingRecords: "Carregando...",
            zeroRecords:    "Nenhum paciente encontrado",
            emptyTable:     "Nenhum paciente encontrado",
            paginate: {
                first:      "Primeiro",
                previous:   "Anterior",
                next:       "Próximo",
                last:       "Último"
            },
            aria: {
                sortAscending:  ": ativar para ordenar a coluna em ordem crescente",
                sortDescending: ": ativar para ordenar a coluna em ordem decrescente"
            }
        }
    });

        $(".dataTables_length select").addClass("form-select form-select-sm");
        $(".dataTables_length label").addClass("form-label");
    });

