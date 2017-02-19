var Tickets = (function (module, $, window) {
    "use strict";

    var _container;
    var _list;
    var _filter;
    var _filter_toggle;
    var _filter_fields;

    var _bind_events = function () {
        $(window.document).on("click", "#filter-toggle", _toggle_filter);
        $(window.document).on("change", ".filter-field", _filter_off);
        $(window.document).on("click", "#refresh-tickets", load_tickets);
        $(window.document).on("click", "button.open-ticket", _open_ticket);
        $(window.document).on("submit", "form.ticket-status-form", _save_properties);
    };

    var _create_ticket = function (e) {

    };

    var _save_properties = function (e) {
        e.preventDefault();

        var form = $(e.target);
        var data = {
            url: Globals.ajaxUrl + "?action=support_update_ticket",
            dataType: "json",
            method: "post",
            success: function (response) {
                Sidebar.load_sidebar(response.data);
                load_tickets();
            }
        };

        data.data = form.serializeArray();

        $.ajax(data);
    };

    var _open_ticket = function (e) {
        var id = $(e.target).data("id");

        if (!App.open_tab(id)) {
            $.ajax({
                url: Globals.ajaxUrl,
                dataType: "json",
                data: {
                    id: id,
                    action: "support_load_ticket"
                },
                success: function (data) {
                    App.new_tab(data);
                    Comments.load_comments(id);
                    Sidebar.load_sidebar(id);
                    $("#" + id).find(".comment-reply").show();
                }
            });
        }
    };

    var _filter_off = function () {
        _filter_toggle.removeClass("active");
    };

    var _toggle_filter = function () {
        _filter_toggle.toggleClass("active");

        if (!_filter_toggle.hasClass("active")) {
            _filter_fields.each(function (index, element) {
                $(element).val("");
            });
        }

        load_tickets();
    };

    var _init_list = function (data) {
        _container.html(data.data);

        _list = $("#tickets-list");

        var cols = [];

        _list.find("th").each(function (index, element) {
            cols.push({
                data: $(element).data("column_name")
            });
        });

        _list.dataTable({
            columns: cols,
            saveState: true
        });
    };

    var load_tickets = function () {
        var refresh = $(".refresh");
        var data = {
            url: Globals.ajaxUrl + "?action=support_list_tickets",
            dataType: "json",
            success: _init_list
        };

        refresh.addClass("rotate");

        if (_filter_toggle.hasClass("active")) {
            data.data = _filter.serializeArray();
        }

        $.ajax(data).done(function () {
            refresh.removeClass("rotate");
        });
    };

    var initialize = function () {
        _container = $("#tickets-container");
        _filter_toggle = $("#filter-toggle");
        _filter = $("#ticket_filter");
        _filter_fields = _filter.find(".filter-field");

        load_tickets();
        window.setInterval(load_tickets, 1000 * 60);

        _bind_events();
    };

    return {
        load_tickets: load_tickets,
        initialize: initialize
    };

})(Tickets || {}, jQuery, window);

jQuery(document).ready(function ($) {
    "use strict";

    Tickets.initialize();

});