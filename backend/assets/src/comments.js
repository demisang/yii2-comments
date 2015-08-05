"use strict";

$(document).on("click", "#comments-grid .toggle-approve", function (e) {
    e.preventDefault();

    var link = $(this);
    var approvedClass = "btn-success";
    var unapprovedClass = "btn-warning";
    var iconChecked = "glyphicon glyphicon-check";
    var iconUnChecked = "glyphicon glyphicon-unchecked";
    var is_approved = link.hasClass(approvedClass);

    var toggleApproved = function (block) {
        var iconSpan = link.find("span.glyphicon");
        var dropdownButton = link.closest(".btn-group").find(".dropdown-toggle:first");
        var buttons = link;

        if (is_approved) {
            // Toggle to unapproved
            buttons.removeClass(approvedClass);
            buttons.addClass(unapprovedClass);
            iconSpan.attr("class", iconUnChecked);
        } else {
            // Toggle to approved
            buttons.removeClass(unapprovedClass);
            buttons.addClass(approvedClass);
            iconSpan.attr("class", iconChecked);
        }
    };

    toggleApproved();
    link.addClass("disabled");

    $.ajax({
        method: "POST",
        url: link.attr("href"),
        dataType: "json",
        success: function (data) {
            if (data && data.status === "success") {

            } else if (data && data.status === "error") {
                // Rollback button toggling
                toggleApproved();
                alert("Database error");
            } else {
                // Rollback button toggling
                toggleApproved();
                alert("Unknown response status");
            }
        },
        complete: function () {
            link.removeClass("disabled");
        }
    });
});