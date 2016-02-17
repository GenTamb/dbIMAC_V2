$(document).ready(function(){
    var probe;
    $("#dbName").keyup(function(){
        var text=$(this).val();
        testRegexNoSpecialChar(text);
    });
    $("#send1").click(function(event){
        event.preventDefault();
        var dbName=$("#dbName").val();
        var dbHost=$("#host").val();
        var dbUser=$("#user").val();
        var dbPassword=$("#psw").val();
        var salt=$("#salt").val();
        var adminName=$("#adminName").val();
        var adminPass=$("#adminPass").val();
        var adminRealName=$("#adminRealName").val();
        var adminRealSurname=$("#adminRealSurname").val();
        if (probe=="" || dbHost=="" || dbUser=="" || adminName=="" || adminPass=="" || adminRealName=="" || adminRealSurname=="")
        {
            alert("Uno o più campi obbligatori sono vuoti, riverifica!");
        }
        else
        {
            $.post("core/funcs.php",
                   {
                    setup:1,
                    dbName:dbName,
                    dbHost:dbHost,
                    dbUser:dbUser,
                    dbPassword:dbPassword,
                    adminName:adminName,
                    adminPass:adminPass,
                    adminRealName:adminRealName,
                    adminRealSurname:adminRealSurname,
                    salt:salt
                   },
                   function(data) {
                    if (data[0]=='yes')
                    {
                        $("#setupDBform").addClass("divHidden");
                        $("#setupComplete").removeClass("divHidden");
                        alert(data[1]);
                    }
                    else alert(data[1]);
                   },"json"
                   );
        }
    });   
});

$(document).ready(function(){
   $("#done").click(function(){
    $.post("core/funcs.php",
           {
            completeSetup:1
           },
           function(data)
           {
            alert(data[1]);
            if (data[0]=='yes')
            {
             window.location.href="index.php";   
            }
            else alert("C'è stato un errore, contatta il developer");
           },"json"
          );
    });
});


function testRegexNoSpecialChar(toTest) {
    var regex= new RegExp("[^a-z0-9_]+")
    var text=toTest;
    if (regex.test(text)) {
            alert("Non sono concessi caratteri speciali, caratteri maiuscoli o spazi!");
            return false;
        }
    else return true;
    
}