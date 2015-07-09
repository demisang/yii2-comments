// --------
// COMMENTS
// --------

(function ($) {
    "use strict";

    $.fn.commentsWidget = function (options) {
        var opts = $.extend({}, $.fn.commentsWidget.defaults, options);
        var widget = $(this);

        // Move form by the click on "reply" or "restore-form" buttons
        $(widget).on("click", opts.replyButtonSelector + ", " + opts.restoreFormSelector, function (e) {
            e.preventDefault();

            opts.moveReplyForm.call(opts, widget, $(this));
        });

        // Delete comment
        $(widget).on("click", opts.deleteButtonSelector, function (e) {
            e.preventDefault();
            var link = $(this);

            if (confirm(opts.deleteComfirmText)) {
                // Restore form position
                opts.restoreCommentForm.call(opts, widget);

                $.ajax({
                    method: "POST",
                    url: link.attr("href"),
                    dataType: "json",
                    success: function () {
                        // Remove comment item (with all child comments)
                        link.closest("li").remove();
                    }
                });
            }
        });

        $(widget.find(opts.formBoxSelector + " form")).on("beforeSubmit", {
            options: opts,
            widget: widget
        }, opts.submitForm);

        return this;
    };

    $.fn.commentsWidget.defaults = {
        maxNestedLevel: 6,
        deleteComfirmText: "Are you sure you want to delete this comment?",
        // Selectors
        itemSelector: ".comment",
        commentTextSelector: ".comment-text > p",
        formBoxSelector: ".comment-form",
        replyButtonSelector: ".reply-button",
        deleteButtonSelector: ".delete-button",
        updateButtonSelector: ".update-button",
        restoreFormSelector: ".restore-comment-form",
        parentCommentIdInputSelector: ".parent_comment_id",
        defaultFormPositionSelector: ".primary-form-container",
        // Handlers
        moveReplyForm: function (widget, replyButton) {
            // In current context "this" variable refers to extended widget options
            var form = widget.find(this.formBoxSelector),
                parentCommentId = replyButton.data("comment-id"),
                newPosition = replyButton.closest(this.itemSelector).after();

            if (!newPosition.length) {
                newPosition = widget.find(this.defaultFormPositionSelector);
            }

            form.appendTo(newPosition);
            form.find(this.parentCommentIdInputSelector).val(parentCommentId);
        },
        submitForm: function (event) {
            var form = $(event.target);
            var widgetOptions = event.data.options;

            // Send form to create action
            $.ajax({
                method: "POST",
                url: form.attr("action"),
                data: form.serialize(),
                dataType: "html",
                success: function (html) {
                    $(form).yiiActiveForm("resetForm");
                    form[0].reset();

                    // "html" contain new comment item
                    // event.data.options - custom event param, contain all plugin options
                    event.data.options.insertNewComment.call(widgetOptions, $(html), event.data.widget);
                }
            });

            // Preventing form submit. This is not a "submit" event, this is special yii.activeForm "beforeSubmit"
            return false;
        },
        insertNewComment: function (newComment, widget) {
            var form = widget.find(this.formBoxSelector);

            // Get parent comment item (exists if new comment is reply to it)
            var parentCommentItem = form.closest("li");
            // Determine if new comment is reply to other comment
            var isReply = parentCommentItem.length;
            // Wrap new comment item to <li> tag
            var newCommentItem = $("<li/>").html(newComment);
            // By default set parent comment as an widget object
            var parentObject = widget;

            if (isReply) {
                if (!parentCommentItem.find("ul:first").length) {
                    // Create new sub-comments section if not exists
                    parentCommentItem.append($("<ul/>"));
                }
                // Set parent comment object
                parentObject = parentCommentItem;
            }

            // Restore form position
            this.restoreCommentForm.call(this, widget);

            // Insert new comment
            parentObject.find("ul:first").append(newCommentItem);
        },
        restoreCommentForm: function (widget) {
            widget.find(this.restoreFormSelector).trigger("click");
        }
    };

}(window.jQuery));
