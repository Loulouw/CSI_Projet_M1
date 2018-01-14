$(document).ready(function () {
    $("#modal10jb").click(function () {
        $("#modal10j").modal("show");
    });
    $("#modalActiviteb").click(function () {
        $("#modalAjoutActivite").modal("show");
    });
    $("#modal1mb").click(function () {
        $("#modal1m").modal("show");
    });
    $("#modal1ab").click(function () {
        $("#modal1a").modal("show");
    });
    $("#modalAjoutEmployeb").click(function () {
        $("#modalAjoutEmploye").modal("show");
    });
    $("#modalAjoutSeanceb").click(function () {
        $("#modalAjoutSeance").modal("show");
    });

    $('#dateDebut').datepicker({"dateFormat": "yy-mm-dd "});
    $('#heureDebut').timepicker({ 'timeFormat': 'H:i:s' });

    $('#dateFin').datepicker({"dateFormat": "yy-mm-dd "});
    $('#heureFin').timepicker({ 'timeFormat': 'H:i:s' });
});


