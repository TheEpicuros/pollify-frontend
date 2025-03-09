
(function($) {
    'use strict';
    
    // Vote on a poll
    $(document).on('submit', '.pollify-poll-form', function(e) {
        e.preventDefault();
        
        var $form = $(this);
        var pollId = $form.data('poll-id');
        var optionId = $form.find('input[name="poll_option"]:checked').val();
        var $message = $form.find('.pollify-poll-message');
        
        if (!optionId) {
            $message.text('Please select an option.').show();
            return;
        }
        
        $form.find('button').prop('disabled', true).text('Submitting...');
        
        $.ajax({
            url: pollifyData.ajaxUrl,
            type: 'POST',
            data: {
                action: 'pollify_vote',
                nonce: pollifyData.nonce,
                poll_id: pollId,
                option_id: optionId
            },
            success: function(response) {
                if (response.success) {
                    // Create results HTML
                    var resultsHtml = '<div class="pollify-poll-results">';
                    
                    $.each(response.data.results, function(index, result) {
                        resultsHtml += '<div class="pollify-poll-result">' +
                            '<div class="pollify-poll-option-text">' +
                            result.text +
                            '<span class="pollify-poll-option-count">' +
                            result.votes + ' votes (' + result.percentage + '%)' +
                            '</span>' +
                            '</div>' +
                            '<div class="pollify-poll-option-bar">' +
                            '<div class="pollify-poll-option-bar-fill" style="width: ' + result.percentage + '%"></div>' +
                            '</div>' +
                            '</div>';
                    });
                    
                    resultsHtml += '<div class="pollify-poll-total">' +
                        'Total votes: ' + response.data.totalVotes +
                        '</div>' +
                        '</div>';
                    
                    // Replace form with results
                    $form.replaceWith(resultsHtml);
                } else {
                    $message.text(response.data.message).show();
                    $form.find('button').prop('disabled', false).text('Vote');
                }
            },
            error: function() {
                $message.text('An error occurred. Please try again.').show();
                $form.find('button').prop('disabled', false).text('Vote');
            }
        });
    });
    
    // Add poll option in create form
    $(document).on('click', '#add-poll-option-btn', function() {
        var optionHtml = '<div class="pollify-poll-option-input">' +
            '<input type="text" name="poll_options[]" required>' +
            '</div>';
        
        $('#poll-options-container').append(optionHtml);
    });
    
    // Create poll form submission
    $(document).on('submit', '#pollify-create-poll-form', function(e) {
        e.preventDefault();
        
        var $form = $(this);
        var $message = $form.find('.pollify-create-poll-message');
        
        $form.find('button[type="submit"]').prop('disabled', true).text('Creating...');
        
        $.ajax({
            url: pollifyData.ajaxUrl,
            type: 'POST',
            data: $form.serialize() + '&action=pollify_create_poll&nonce=' + pollifyData.nonce,
            success: function(response) {
                if (response.success) {
                    $message.removeClass('error').addClass('success').text(response.data.message).show();
                    
                    // Redirect to the new poll after a short delay
                    setTimeout(function() {
                        window.location.href = response.data.pollUrl;
                    }, 1500);
                } else {
                    $message.removeClass('success').addClass('error').text(response.data.message).show();
                    $form.find('button[type="submit"]').prop('disabled', false).text('Create Poll');
                }
            },
            error: function() {
                $message.removeClass('success').addClass('error').text('An error occurred. Please try again.').show();
                $form.find('button[type="submit"]').prop('disabled', false).text('Create Poll');
            }
        });
    });
    
})(jQuery);
