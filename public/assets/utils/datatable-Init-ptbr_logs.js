    $(document).ready(function() {
        "use strict";

        $("#alternative-page-datatable").DataTable({
        pagingType: "full_numbers",
        pageLength: 100,
        columnDefs: [
            { targets: [0, -1], orderable: false }, 
            { targets: 2, type: 'string', orderData: 2 } 
        ],
        order: [[1, 'desc']], 
        drawCallback: function() {
            $(".dataTables_paginate > .pagination").addClass("pagination-rounded");
        },
        language: {
            processing:     "Processando...",
            search:         "Pesquisar:",
            lengthMenu:     "Mostrar _MENU_ registros",
            info:           "Mostrando _START_ até _END_ de _TOTAL_ registros",
            infoEmpty:      "Mostrando 0 até 0 de 0 registros",
            infoFiltered:   "(filtrado de _MAX_ registros no total)",
            infoPostFix:    "",
            loadingRecords: "Carregando...",
            zeroRecords:    "Nenhum registro encontrado",
            emptyTable:     "Nenhum dado disponível nesta tabela",
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