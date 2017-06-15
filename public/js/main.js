$(document).ready(function () {

    $("#lv-load, #lv-begin").on("click", function (e) {
        e.preventDefault();
        var path = $('#lv-path').val();
        LogViewer.load(path);
        $("#lv-path_name").text(path);
    });

    $("#lv-next").on("click", function (e) {
        e.preventDefault();
        if ($(this).data('click') === true) {
            LogViewer.nextPage();
        }
    });

    $("#lv-prev").on("click", function (e) {
        e.preventDefault();
        if ($(this).data('click') === true) {
            LogViewer.prevPage();
        }
    });

    $("#lv-end").on("click", function (e) {
        e.preventDefault();
        if ($(this).data('click') === true) {
            LogViewer.lastPage();
        }
    });

    $("#lv-demo").on("click", function () {
        $('#lv-path').val($(this).text());
    });
});

