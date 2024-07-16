jQuery(document).ready(function($) 
{
    $('.delete-password-button').on('click', function() 
    {
        var passwordId = $(this).data('id');
        $.ajax(
            {
            url: ajax_object.ajax_url,
            type: 'POST',
            data: 
            {
                action: 'delete_password',
                password_id: passwordId
            },
            success: function(response) 
            {
                if (response.success) 
                {
                    location.reload();
                } 
                    else 
                {
                    alert('Failed to delete password. Please try again.');
                }
            }
        });
    });
});
