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
            processing:     "Processing...",
            search:         "Search:",
            lengthMenu:     "Show _MENU_ entries",
            info:           "Showing _START_ to _END_ of _TOTAL_ entries",
            infoEmpty:      "Showing 0 to 0 of 0 entries",
            infoFiltered:   "(filtered from _MAX_ total entries)",
            infoPostFix:    "",
            loadingRecords: "Loading...",
            zeroRecords:    "No matching records found",
            emptyTable:     "No data available in this table",
            paginate: {
                first:      "First",
                previous:   "Previous",
                next:       "Next",
                last:       "Last"
            },
            aria: {
                sortAscending:  ": activate to sort column ascending",
                sortDescending: ": activate to sort column descending"
            }
        }
    });

    $(".dataTables_length select").addClass("form-select form-select-sm");
    $(".dataTables_length label").addClass("form-label");
});
