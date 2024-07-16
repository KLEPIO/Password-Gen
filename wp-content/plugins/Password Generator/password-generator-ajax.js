jQuery(document).ready(function($) 
{
    $('#password-storage-form').on('submit', function(e) 
    {
        e.preventDefault();
        var passwordName = $('#password-name').val();
        var passwordValue = $('#password-value').val();
        $.ajax(
            {
            url: ajax_object.ajax_url,
            type: 'POST',
            data: 
            {
                action: 'store_password',
                password_name: passwordName,
                password_value: passwordValue
            },
            success: function(response) 
            {
                if (response.success) 
                {
                    $('#storage-result').text(response.data);
                } 
                    else 
                {
                    $('#storage-result').text('Error: ' + response.data);
                }
            }
        });
    });
});
