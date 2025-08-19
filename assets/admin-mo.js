jQuery(document).ready(function($){

    $(document).on('click', 'button[value="dzsedg_enable_max_input_vars_5000"]', function(){
        var r = confirm("WARNING - high risk in breaking htaccess - only click if you know what you are doing");

        if(r){

        }else{
            return false;
        }
    })
    $(document).on('click', '.delete-debug-data', function(){
        var r = confirm("WARNING - delete debug data ? ");

        if(r){

        }else{
            return false;
        }
    })
})
