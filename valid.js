jQuery(document).ready(function($){
    $('form#form').on('submit', function(e){
       e.preventDefault();
       var name =jQuery('#name').val();
       var email = jQuery('#email').val();
       var mobileno = jQuery('#mobileno').val();
       var message = jQuery('#message').val();
       // debugger;
       var text;
       if(name.length < 5){
         text = "Please Enter valid Name";
        alert(text);
         return false;
       }
       if(isNaN(mobileno) || mobileno.length != 10){
         text = "Please Enter valid mobileno Number";
         alert(text);
         return false;
       }
       if(email.indexOf("@") == -1 || email.length < 6){
         text = "Please Enter valid Email";
         alert(text);
         return false;
       }
       
       $.ajax({
          url: ajax_object.ajaxurl,
          type:"POST",
          dataType:'json',
          data: {
             action:'contact',
             name: name,
             email: email,
             mobileno: mobileno,
             message: message
        },   success: function(data){
            if (data.res == true){
                alert(data.message);    // success message
            }
         }, error: function(data){
            if (data.res == false){
                alert(data.message);    // success message
            }   
        }
       });
    $('#form')[0].reset();
      });
    });