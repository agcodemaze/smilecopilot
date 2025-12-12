    $(document).ready(function() {
        "use strict";

        $("#alternative-page-datatable").DataTable({
        pagingType: "full_numbers",
        pageLength: 50,
        columnDefs: [
            { targets: [0, -2], orderable: false }, 
            { targets: 2, type: 'string', orderData: 2 } 
        ],
        order: [[2, 'asc']], 
        drawCallback: function() {
            $(".dataTables_paginate > .pagination").addClass("pagination-rounded");
        },
        language: {
            processing:     "Processando...",
            search:         "Pesquisar:",
            lengthMenu:     "Mostrar _MENU_ consultas",
            info:           "Mostrando _START_ até _END_ de _TOTAL_ consultas",
            infoEmpty:      "Mostrando 0 até 0 de 0 consultas",
            infoFiltered:   "(filtrado de _MAX_ registros no total)",
            infoPostFix:    "",
            loadingRecords: "Carregando...",
            zeroRecords:    "Nenhuma consulta encontrada",
            emptyTable:     "Nenhuma consulta encontrada",
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

