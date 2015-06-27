if( gps === undefined ) var gps = new Object();

;(function(gps, $){
    function successfulSubmit( responseText, statusText, xhr, $form ){
        var $output = $('#ffOutput'),
            $link   = $('#link');
        $output.val( JSON.stringify(responseText) );
        $link.html('Your <a href="'+responseText.convertedFile+'">file.</a>');
        $('div#loading').hide();
    }
    function unSuccessfulSubmit( jqXHR, statusText, errorThrown ){
        $('div#loading').hide();
        console.log('Something went terribly wrong...2');
    }
    function submit( e, $form ){
        $('div#loading').show();
        var formSubmitOptionsObj = {
            dataType:'json', 
            data:{'encoding':'json'}, 
            beforeSubmit:function(){},
            success:successfulSubmit,
            error: unSuccessfulSubmit
            //complete: function(jqXHR, textStatus){ alert(textStatus);}
        };
        $form.ajaxSubmit(formSubmitOptionsObj);
        e.preventDefault();
        return false;
    }

    gps.form = $('#fForm').submit( function(evt){
        submit( evt, $(this) );
    });

    $(document).ajaxStart( function(){
        $('div#loading').show();
    });
    $(document).ajaxStop( function(){
        $('div#loading').hide();
    });

    //Drap drop
    var dropZone = document.getElementById('fForm');
    dropZone.addEventListener('dragover', function( e ){
        if (e.preventDefault) {
            e.preventDefault(); // Necessary. Allows us to drop.
        }
        e.dataTransfer.dropEffect = 'move';  // See the section on the DataTransfer object.
        return false;
    });
    dropZone.addEventListener('drop', function( e ){
        // this/e.target is current target element.
        if (e.stopPropagation) {
            e.stopPropagation(); // Stops some browsers from redirecting.
        }
        var files = e.dataTransfer.files;
        var formData = new FormData();
        var form = document.getElementById('fForm');
        for (var i = 0; i < files.length; i++) {
            formData.append('ffFile', files[i]);
        }

        // now post a new XHR request
        var xhr = new XMLHttpRequest();
        xhr.open('POST', form.getAttribute('action'));
        //xhr.open('POST', 'echo.php');
        xhr.onload = function(){
            var xhr = this;
            if (xhr.status === 200) {
                $('div#loading').hide();
                successfulSubmit( JSON.parse(xhr.responseText), xhr.status, xhr );
            } else {
                $('div#loading').hide();
                console.log('Something went terribly wrong...');
            }
        };

        $('div#loading').show();
        xhr.send(formData);
        e.preventDefault();
        return false;
    });
})(gps, jQuery);
