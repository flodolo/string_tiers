/* eslint new-cap: [2, {"capIsNewExceptions": ["DataTable"]}] */

$(document).ready(function() {
    $('.table').DataTable({
        info: false,
        paging: false,
        searching: false
    });
});
