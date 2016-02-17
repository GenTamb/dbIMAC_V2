$(document).on({
    ajaxStart: function() {$("body").addClass("loading").delay("500"); },
    ajaxStop: function(){$("body").delay("1000").removeClass("loading");}
});


$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip(); 
});

$(document).ready(function(){
    $("#adminContent").on('mouseover','.suggerimento',function(){
        $(this).tooltip();
});
});

/*************************************************************************
 *                               login script                            *
 *************************************************************************/

$(document).ready(function()
                  {
                    $("#send").click(function(e)
                                     {
                                      e.preventDefault();
                                      var userName=$("#userName").val();
                                      var userPass=$("#userPassword").val();
                                      if (userName=="" || userPass=="")
                                      {
                                        alert("Uno o più campi vuoti");
                                      }
                                      else
                                      {
                                        $.post("core/funcs.php",
                                               {
                                                loginAttempt:1,
                                                userName:userName,
                                                userPass:userPass
                                               },
                                               function(data)
                                               {
                                                alert(data[1]);
                                                if (data[0]=='yes')
                                                {
                                                    window.location.href="index.php";
                                                    
                                                }
                                               },"json");
                                      }
                                     });
                  });

/*************************************************************************
 *                               index script                            *
 *************************************************************************/

//mostra ultime 100 imac
$(document).ready(function()
                  {
                    $("#showLast100").click(function()
                                            {
                                                $.post("core/funcs.php",
                                                       {
                                                        showLastImac:1,
                                                        howMany:100
                                                       },
                                                       function(data)
                                                       {
                                                        $("#container").html(data);
                                                       });
                        
                                            });
                 });

//funzione pop up tasto note
$(document).ready(function(){
    $("#sideRightBody").on('click','.NOTE',function(){
        var nProtocollo=$(this).attr("id");
        $.post("core/funcs.php",
               {
                recuperaNote:1,
                nProtocollo:nProtocollo
               },
               function(data)
               {
                
                if ($("#popUP-note").attr("display","block"))
                {
                      $("#popUP-note").hide();
                      $("#noteContent").html(data);
                      $("#popUP-note").slideDown();
                }
                else
                {
                $("#popUP-note").slideDown();
                $("#noteContent").html(data);
                }
               });
    });
});

//funzione chiusura pop up
$(document).ready(function(){
    $("#closePOPUP").click(function(){
        $("#popUP-note").slideUp();
    });
});

//funzione download file
$(document).ready(function(){
    $("#sideRightBody").on('click','.PATHFILE',function(){
        var nProtocollo=$(this).attr("id");
        $.post("core/funcs.php",
               {
                recuperaFile:1,
                nProtocollo:nProtocollo
               },
               function(data)
               {
                window.open(data);
               });
    });
});

//logout
$(document).ready(function(){
    $("#logout").click(function(){
        var logout=confirm("Sei sicuro di voler uscire?");
        if(logout)
        {
            $.post("core/funcs.php",
                   {
                    logout:1
                   },
                   function()
                   {
                    window.location.href="login.php";
                   });
        }
    });
});

//admin
$(document).ready(function(){
    $("#adminConsole").click(function(){
        window.open("admin.php","Admin Console","width=1200, height=768");
    });
});
/*****************************************************
 *                  filters script                   *
 *****************************************************/

//check empty fields
function checkEmptyFilter(campo) {
    var response=false;
    if (campo.val()!='') response=true;
    return response;
}

//post filter value
$(document).ready(function(){
    $(".buttonFilter").click(function(){
        var idButton=$(this).attr("id");
        switch (idButton)
        {
            case "cercaXnprotocolloButton":
                if (checkEmptyFilter($("#cercaXnprotocollo")))
                {
                $.post("core/funcs.php",
                       {
                        cercaXnprocollo:1,
                        nprotocollo:$("#cercaXnprotocollo").val()
                       },
                       function(data)
                       {
                        $("#container").html(data);
                       });
                }
                else alert('Campo Vuoto');
                break;
            case "cercaXticketButton":
                if (checkEmptyFilter($("#cercaXticket")))
                {
                $.post("core/funcs.php",
                       {
                        cercaXticket:1,
                        ticket:$("#cercaXticket").val()
                       },
                       function(data)
                       {
                        $("#container").html(data);
                       });
                }
                else alert('Campo Vuoto');
                break;
            case "cercaXmatricolaButton":
                if (checkEmptyFilter($("#cercaXmatricola")))
                {
                $.post("core/funcs.php",
                       {
                        cercaXmatricola:1,
                        matricola:$("#cercaXmatricola").val()
                       },
                       function(data)
                       {
                        $("#container").html(data);
                       });
                }
                else alert('Campo Vuoto');
                break;
            case "cercaXdataButton":
                if (checkEmptyFilter($("#cercaXdata")))
                {
                    var data=$("#cercaXdata").val();
                    $.post("core/funcs.php",
                    {
                     cercaXdata:1,
                     data:data
                    },
                    function(data)
                    {
                     $("#container").html(data);
                    });
                }
                else alert('Campo vuoto');
                break;
            case "cercaXdataButtonRange":
                if (checkEmptyFilter($("#cercaXdata")) && checkEmptyFilter($("#cercaXdataRange")))
                {
                   if (validateRange())
                   {
                    $.post("core/funcs.php",
                           {
                            cercaXrange:1,
                            from:$("#cercaXdata").val(),
                            until:$("#cercaXdataRange").val()
                           },
                           function(data)
                           {
                           $("#container").html(data);
                           });
                   }
                   else alert("Errore: la data finale è inferiore a quella di inizio");
                }
                break;
                
        }
        
        
    });
});

//check numeri su campi filter
$(document).ready(function(){
    $(".numero").keyup(function(){
        if (isNaN($(this).val()))
        {
            alert("non è un numero");
        }
    });
});

//abilita datepicker
$(document).ready(function(){
    $("#cercaXdata").datepicker({ dateFormat: 'yy-mm-dd' });
    $("#cercaXdataRange").datepicker({ dateFormat: 'yy-mm-dd' });
});

//checkbox data range
$(document).ready(function(){
    $("#range").change(function()
                       {
                        $("#rangeDiv").toggle();
                        $("#cercaXdataButton").toggle();
                       });
});

//check validità range date
function validateRange()
{
    var from=new Date();
    from=$.datepicker.parseDate("yy-mm-dd",$("#cercaXdata").val());
    var until=new Date();
    until=$.datepicker.parseDate("yy-mm-dd",$("#cercaXdataRange").val());
    var ret;
    (until<from)? ret=false : ret=true;
    return ret;
}

/*************************************************************************
 *                               admin script                            *
 *************************************************************************/

/*********************************************
 *                   IMAC                    *
 *********************************************/

//menu IMAC - new
$(document).ready(function(){
   $("#newIMAC").click(function(){
    $.post("core/gui.php",
           {
            newIMAC:1
           },
           function (gui)
           {
            $("#adminContent").html(gui);
           });
   });
});

//onclick su 'automatico'
$(document).ready(function(){
    $("#adminContent").on('click',"[name='procedura']",function(){
        var procedura=$(this).val();
        if (procedura=='auto')
        {
            $("#sceltaProcedura").addClass("nascosto");
            $("#pAuto").removeClass("nascosto");
        }
        if (procedura=='continue')
        {
            $.post("core/gui.php",
               {
                fetchIMAC:1
               },
               function(data)
               {
                $("#adminContent").html(data);
               }
            );
        }
    });
});

//open upload form
$(document).ready(function(){
    $("#adminContent").on('click',"#openUploadForm",function(){
        window.open("_uploadHandler/handler.php?upload=new","Upload Form","width=500px,height=200px");
    });
});

//close upload form
$(document).ready(function(){
    $("#closeHandler").click(function(){
        if ($("#numeroFilesOK").text()>0)
        {
           $("#openUploadForm",opener.document).hide();
           $("#pAuto_fetch",opener.document).show();
        }
        else $("#openUploadForm",opener.document).text('Ripeti Upload');
        window.close();
    });
});

//fetch automatico
$(document).ready(function(){
    $("#adminContent").on('click','#startFetchOps',function(){
        $.post("core/gui.php",
               {
                fetchIMAC:1
               },
               function(data)
               {
                $("#adminContent").html(data);
               }
                );
    });
});

//abilita data
$(document).ready(function(){
    $("#adminContent").on('mouseenter',"#FETCHdata",function(){
        $(this).datepicker({ dateFormat: 'yy-mm-dd' });
    });
});

//auto fill campo cognome,nome a partire da matricola
$(document).ready(function(){
    $("#adminContent").on('mouseover','#imacFetchList',function(){
        $("tr").each(function(){
            var current=this;
            $.post("core/funcs.php",
               {
                cercaUtenteByMat:1,
                matUtente:$("#FETCHmatUtente",$(this)).val()
               },
               function(data)
               {
                $("#FETCHcognomeNome",$(current)).val(data[0]+","+data[1]);
               },"json");
               });
        });
});

//suggerimento su campo matricola
$(document).ready(function(){
    $("#adminContent").on('keyup',"#FETCHmatUtente",function(){
            var mat=$(this).val();
            if (mat.length>=4)
            {
              $.post("core/funcs.php",
                     {
                      hintMatricola:1,
                      mat:mat
                     },
                     function(data)
                     {
                        
                      $("#hintContent").slideDown("slow").html(data);
                     });
            }
            else $("#hintContent").addClass('nascosto');
    });
});

//nascondi campo suggerimento on blur
$(document).ready(function(){
    $("#adminContent").on('blur',"#FETCHmatUtente",function(){
            $("#hintContent").slideUp("slow");
    });
});


//check numeri su campo e data
$(document).ready(function(){
    $("#adminContent").on('mouseover','.numero',function(){
        $(this).keyup(function(){
            if (isNaN($(this).val()))
            {
                alert("non è un numero");
            }
        });
  });
});

//funzionamento clear all
$(document).ready(function(){
    $("#adminContent").on('click','#clearFiles',function(){
        var chaining=false;
        var prosegui=confirm('Sei sicuro di voler cancellare i files temporanei?');
        if (prosegui)
        {
            $.post("core/funcs.php",
                   {
                    deleteTempFiles:1,
                   },
                   function(data)
                   {
                    if (data[0]=='deleted')
                    {
                     chaining=true;
                    }
                   },"json").done(function()
                            {
                                if (chaining)
                                {
                                    $.post("core/gui.php",
                                                {
                                                 fetchIMAC:1
                                                },
                                                function(result)
                                                {
                                                 $("#adminContent").html(result);
                                                }
                                            );
                                }
                            });
        }
    });
});

