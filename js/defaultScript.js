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
    $(".numero").on(
        {keyup: function(){
                if (isNaN($(this).val()))
                {
                    alert("non è un numero");
                }
               },
        blur  : function(){
                if (isNaN($(this).val()))
                {
                    alert("non è un numero");
                }
              }
        }
    );    
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
 *                    IMAC                   *
 *********************************************/

/**************************
 *          NEW           *
 **************************/

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

//onclick su radio group
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
        if (procedura=='manual')
        {
           $.post("core/gui.php",
                  {
                    manualInsert:1
                  },
                  function(data)
                  {
                    $("#adminContent").html(data);
                  }
                 );
        }
    });
});

//COUNTINUE - AUTO open upload form
$(document).ready(function(){
    $("#adminContent").on('click',"#openUploadForm",function(){
        window.open("_uploadHandler/handler.php?upload=1&mode=new","Upload Form","width=500px,height=200px");
    });
});

//COUNTINUE - AUTO - MANUAL EDIT close upload form
$(document).ready(function(){
    $("#closeHandler").click(function(){
        if (window.name=='Upload Form')
        {
            if ($("#numeroFilesOK").text()>0)
            {
               $("#openUploadForm",opener.document).hide();
               $("#pAuto_fetch",opener.document).show();
            }
            else $("#openUploadForm",opener.document).text('Ripeti Upload');
        }
        else
        {
            var index=parseInt(window.name)+1;
            //remove p7m ext - uploadHandler ha già estratto il file al momento dell'upload
            var fileName=$("#uploadedFileName").text();
            var p7mExtMin='.p7m';
            var p7mExtMax='.P7M';
            if (fileName.indexOf(p7mExtMin) >=0) 
            {
               fileName=fileName.replace(p7mExtMin,"");
            }
            if (fileName.indexOf(p7mExtMax)>=0)
            {
               fileName=fileName.replace(p7mExtMax,"");
            }
            
            //manual
            var row=window.opener.$("#imacManualList").find('tr').eq(index);
            $(row).find('.MANUALupFileClass').html(fileName);
            
            //edit
            var rowEdit=window.opener.$("#imacListEdit").find('tr').eq(index);                
            $(rowEdit).find('.newFileUppato').removeClass('nascosto').html(fileName);
            $(rowEdit).find('.addPATHFILE').addClass('nascosto');
            
        }
       window.close();
    });
});

//ALL fetch automatico
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

//ALL abilita data
$(document).ready(function(){
    $("#adminContent").on('mouseenter',"#FETCHdata,.DataManual",function(){
        $(this).datepicker({ dateFormat: 'yy-mm-dd' });
    });
});



//ALL auto fill campo cognome,nome a partire da matricola
$(document).ready(function(){
    $("#adminContent").on('mouseover','#imacFetchList,#imacManualList,#newDipList',function(){
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

//ALL suggerimento su campo matricola
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

//ALL nascondi campo suggerimento on blur
$(document).ready(function(){
    $("#adminContent").on('blur',"#FETCHmatUtente",function(){
            $("#hintContent").slideUp("slow");
    });
});


//ALL check numeri su campo ticket e data
$(document).ready(function(){
    $("#adminContent").on('mouseover','.numero',function(){
          $(this).on(
                    {keyup: function(){
                            if (isNaN($(this).val()))
                            {
                                alert("non è un numero");
                                                            }
                           },
                    blur  : function(){
                            if (isNaN($(this).val()))
                            {
                                alert("non è un numero");
                                $(this).focus();
                            }
                          }
                    }
                );   
  });
});

//COUNTINUE - AUTO funzionamento add
$(document).ready(function(){
    $("#adminContent").on('click',"#FETCHadd",function(){
        var currentTR=$(this).closest("tr");
        var FIELDfileName=$(currentTR).find("#FETCHfileName").text();
        var FIELDmatricola=$(currentTR).find("#FETCHmatUtente").val();
        var FIELDcognomeNome=$(currentTR).find("#FETCHcognomeNome").val();
        var FIELDticket=$(currentTR).find("#FETCHticket").val();
        var FIELDtipo=$(currentTR).find(".selettore_tipo").val();
        var FIELDnote=$(currentTR).find("#FETCHnote").val();
        var FIELDdata=$(currentTR).find("#FETCHdata").val();
        var FIELDprot=$(currentTR).find("#FETCHprotImac");
        if (FIELDmatricola=='' ||  FIELDcognomeNome=='' )
        {
            alert('Campi matricola e/o nome,cognome vuoti!');
        }
        else if (FIELDmatricola=='Errore in lettura' || FIELDcognomeNome=='aggiungere dipendente,dal pannello')
        {
            alert('Valori matricola/cognome,nome non accettati!');
        }
        else
        {
            $.post("core/funcs.php",
                   {
                    addRecord:1,
                    ticket:FIELDticket,
                    matUtente:FIELDmatricola,
                    tipoRichiesta:FIELDtipo,
                    fileName:FIELDfileName,
                    note:FIELDnote,
                    dataApertura:FIELDdata
                   },
                   function (data)
                   {
                    if (data[0]=='yes')
                    {
                        FIELDprot.text('N'+data[1]);
                        $(currentTR).find(".IMAC-CD").hide("slow");
                    }
                    else alert(data[1]);
                   },"json"
                  );
        }
    });
});

//CONTINUE - AUTO funzionamento del
$(document).ready(function(){
    $("#adminContent").on('click',"#FETCHdel",function(){
        var currentTR=$(this).closest("tr");
        var fileName=$(currentTR).find("#FETCHfileName").text();
        var go=confirm("Sei sicuro di voler cancellare il record per il file "+ fileName);
        if (go)
        {
            $.post("core/funcs.php",
                   {
                    deleteSingleTempFile:1,
                    fileName:fileName
                   },
                   function(data)
                   {
                    if (data[0]=='yes')
                    {
                        $(currentTR).hide("slow");
                    }
                    else alert(data[1]);
                   },"json");
        }    
    });
});

//COUNTINUE - AUTO funzionamento clear all
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

//MANUAL funzionamento bottone Add Row
$(document).ready(function(){
    $("#adminContent").on('click',"#newRow",function(){
        $("#blankManualRow").clone().appendTo('tbody').removeClass('nascosto');
    });
});


//MANUAL open upload form
$(document).ready(function(){
    $("#adminContent").on('click','#MANUALupFile',function(){
        var thisTR=$(this).closest('tr');
        var windowName=thisTR.index();
        window.open("_uploadHandler/handler.php?upload=1&mode=manual&row="+windowName,windowName,"width=500px,height=200px");
     });
});

//MANUAL add imac
$(document).ready(function(){
    $("#adminContent").on('click',"#MANUALadd",function(){
        var currentTR=$(this).closest("tr");
        var FIELDfileName=$(currentTR).find("#MANUALupFile").text();
        var FIELDmatricola=$(currentTR).find("#FETCHmatUtente").val();
        var FIELDcognomeNome=$(currentTR).find("#FETCHcognomeNome").val();
        var FIELDticket=$(currentTR).find("#FETCHticket").val();
        var FIELDtipo=$(currentTR).find(".selettore_tipo").val();
        var FIELDnote=$(currentTR).find("#FETCHnote").val();
        var FIELDdata=$(currentTR).find("#FETCHdata").val();
        var FIELDprot=$(currentTR).find("#FETCHprotImac");
        if (FIELDmatricola=='' ||  FIELDcognomeNome=='' )
        {
            alert('Campi matricola e/o nome,cognome vuoti!');
        }
        else if (FIELDmatricola=='Errore in lettura' || FIELDcognomeNome=='aggiungere dipendente,dal pannello')
        {
            alert('Valori matricola/cognome,nome non accettati!');
        }
        else
        {
            $.post("core/funcs.php",
                   {
                    addRecord:1,
                    ticket:FIELDticket,
                    matUtente:FIELDmatricola,
                    tipoRichiesta:FIELDtipo,
                    fileName:FIELDfileName,
                    note:FIELDnote,
                    dataApertura:FIELDdata
                   },
                   function (data)
                   {
                    if (data[0]=='yes')
                    {
                        FIELDprot.text('N'+data[1]);
                        $(currentTR).find(".IMAC-CD").hide("slow");
                    }
                    else alert(data[1]);
                   },"json"
                  );
        }
    });
});

//MANUAL funzionamento del
$(document).ready(function(){
    $("#adminContent").on('click',"#MANUALdel",function(){
        var currentTR=$(this).closest("tr");
        var fileName=$(currentTR).find("#MANUALupFile").text();
        var go=confirm("Sei sicuro di voler cancellare il record per il file "+ fileName);
        if (go)
        {
            $.post("core/funcs.php",
                   {
                    deleteSingleTempFile:1,
                    fileName:fileName
                   },
                   function(data)
                   {
                    if (data[0]=='yes')
                    {
                        $(currentTR).hide("slow");
                    }
                    else alert(data[1]);
                   },"json");
        }    
    });
});

/**************************
 *          EDIT          *
 **************************/
//menu IMAC - edit
$(document).ready(function(){
   $("#editIMAC").click(function(){
    $.post("core/gui.php",
           {
            editIMAC:1
           },
           function (gui)
           {
            $("#adminContent").html(gui);
           });
   });
});

/*****************************************************
 *              filters script  on EDIT              *
 *****************************************************/


//post filter value
$(document).ready(function(){
    $("#adminContent").on('click',".buttonFilter",function(){
        var idButton=$(this).attr("id");
        switch (idButton)
        {
            case "cercaXnprotocolloButton":
                if (checkEmptyFilter($("#cercaXnprotocollo")))
                {
                $.post("core/funcs.php",
                       {
                        cercaXnprocolloEDIT:1,
                        nprotocollo:$("#cercaXnprotocollo").val()
                       },
                       function(data)
                       {
                        $("#adminContent").html(data);
                       });
                }
                else alert('Campo Vuoto');
                break;
            case "cercaXticketButton":
                if (checkEmptyFilter($("#cercaXticket")))
                {
                $.post("core/funcs.php",
                       {
                        cercaXticketEDIT:1,
                        ticket:$("#cercaXticket").val()
                       },
                       function(data)
                       {
                        $("#adminContent").html(data);
                       });
                }
                else alert('Campo Vuoto');
                break;
            case "cercaXmatricolaButton":
                if (checkEmptyFilter($("#cercaXmatricola")))
                {
                $.post("core/funcs.php",
                       {
                        cercaXmatricolaEDIT:1,
                        matricola:$("#cercaXmatricola").val()
                       },
                       function(data)
                       {
                        $("#adminContent").html(data);
                       });
                }
                else alert('Campo Vuoto');
                break;                
        }
        
        
    });
});

//check numeri su campi filter
$(document).ready(function(){
    $("#adminContent").on('keyup',".numero",function(){
                if (isNaN($(this).val()))
                {
                    alert("non è un numero");
                }
               });
});
$(document).ready(function(){
    $("#adminContent").on('blur',".numero",function(){
                if (isNaN($(this).val()))
                {
                    alert("non è un numero");
                }
               });
});

//EDIT suggerimento su campo matricola
$(document).ready(function(){
    $("#adminContent").on('keyup',"#matricola",function(){
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

//EDIT  edit button
$(document).ready(function(){
    $("#adminContent").on('click',"#EDITimac",function(){
    var currentTR=$(this).closest("tr");
    var FIELDnProtocollo=$(currentTR).find("#nProtocollo").text();
    var FIELDticket=$(currentTR).find("#ticket").val();
    var FIELDmatricola=$(currentTR).find("#matricola").val();
    var FIELDcognomeNome=$(currentTR).find("#cognome_nome").val();
    var FIELDstato=$(currentTR).find("#STATO").val();
    var FIELDtipo=$(currentTR).find(".selettore_tipo").val();
    var FIELDnote=$(currentTR).find("#note").val();
    var FIELDnewFile=$(currentTR).find(".newFileUppato").text();
    var FIELDdata=$(currentTR).find("#FETCHdata").val();
    
    if (FIELDmatricola=='' ||  FIELDcognomeNome=='' )
    {
        alert('Campi matricola e/o nome,cognome vuoti!');
    }
    else if (FIELDmatricola=='Errore in lettura' || FIELDcognomeNome=='aggiungere dipendente,dal pannello')
    {
        alert('Valori matricola/cognome,nome non accettati!');
    }
    else
    {
        $.post("core/funcs.php",
               {
                editRecord:1,
                nProtocollo:FIELDnProtocollo,
                ticket:FIELDticket,
                matUtente:FIELDmatricola,
                stato:FIELDstato,
                tipoRichiesta:FIELDtipo,
                fileName:FIELDnewFile,
                note:FIELDnote,
                dataApertura:FIELDdata
               },
               function (data)
               {
                if (data[0]=='yes')
                {
                    $(currentTR).find(".IMAC-CD").hide("slow");
                }
                alert(data[1]);
               },"json"
              );
        }
    });
});

//EDIT open file
$(document).ready(function(){
    $("#adminContent").on('click','.PATHFILE',function(){
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

//EDIT open upload form
$(document).ready(function(){
    $("#adminContent").on('click','#addNewFile',function(){
        var thisTR=$(this).closest('tr');
        var windowName=thisTR.index();
        window.open("_uploadHandler/handler.php?upload=1&mode=manual&row="+windowName,windowName,"width=500px,height=200px");
     });
});

//EDIT delete record
$(document).ready(function(){
    $("#adminContent").on('click',"#DELimac",function(){
       var currentTR=$(this).closest("tr");
       var nProtocollo=$(currentTR).find("#nProtocollo").text();
       var go=confirm("Sei sicuro di voler cancellare il record "+ nProtocollo);
        if (go)
        {
            $.post("core/funcs.php",
                   {
                    deleteIMAC:1,
                    nProtocollo:nProtocollo
                   },
                   function(data)
                   {
                    if (data[0]=='yes')
                    {
                        $(currentTR).hide("slow");
                    }
                    else alert(data[1]);
                   },"json");
        }    
    });
});

/*********************************************
 *                 DIPENDENTI                *
 *********************************************/

/**************************
 *          NEW           *
 **************************/

//open menu new Dipendente
$(document).ready(function(){
    $("#newDIP").click(function(){
        $.post("core/gui.php",
               {
                newDIP:1
               },
               function(data)
               {
                $("#adminContent").html(data);
               }
              );
    });
});

//add row new Dipendente
$(document).ready(function(){
    $("#adminContent").on('click',"#newRow",function(){
        $("#blankDipRow").clone().appendTo('tbody').removeClass('nascosto');
    });
});

//funzionamento bottone add dipendente
$(document).ready(function(){
     $("#adminContent").on('click',"#ADDdip",function(){
        var currentTR=$(this).closest("tr");
        var FIELDmatricola=$(currentTR).find("#FETCHmatUtente").val();
        var DIPcognomeUtente=$(currentTR).find("#DIPcognomeUtente").val();
        var DIPnomeUtente=$(currentTR).find("#DIPnomeUtente").val();
        if (FIELDmatricola=='' ||  DIPcognomeUtente=='' || DIPnomeUtente=='')
        {
            alert('Campi matricola e/o nome,cognome vuoti!');
        }
        else
        {
            $.post("core/funcs.php",
                   {
                    addDip:1,
                    matricola:FIELDmatricola,
                    cognome:DIPcognomeUtente,
                    nome:DIPnomeUtente
                   },
                   function (data)
                   {
                    if (data[0]=='yes')
                    {
                        $(currentTR).find(".DIP-CD").hide("slow");
                    }
                    alert(data[1]);
                   },"json"
                  );
        }
    });
});

//funzionamento bottone del in menu add utente
$(document).ready(function(){
    $("#adminContent").on('click','#DELdip',function(){
        var currentTR=$(this).closest("tr");
        $(currentTR).hide("slow");
    });
});


/**************************
 *          EDIT          *
 **************************/
//menu DIPENDENTI - edit
$(document).ready(function(){
   $("#editDIP").click(function(){
    $.post("core/gui.php",
           {
            editDIP:1
           },
           function (gui)
           {
            $("#adminContent").html(gui);
           });
   });
});

/*****************************************************
 *              filters script  on EDIT              *
 *****************************************************/


//post filter value
$(document).ready(function(){
    $("#adminContent").on('click',".buttonFilter",function(){
        var idButton=$(this).attr("id");
        switch (idButton)
        {
            case "cercaXmatricolaButton":
                if (checkEmptyFilter($("#cercaXmatricola")))
                {
                $.post("core/funcs.php",
                       {
                        cercaDIPxMatricola:1,
                        matricola:$("#cercaXmatricola").val()
                       },
                       function(data)
                       {
                        $("#adminContent").html(data);
                       });
                }
                else alert('Campo Vuoto');
                break;
            case "cercaXcognomeButton":
                if (checkEmptyFilter($("#cercaXcognome")))
                {
                $.post("core/funcs.php",
                       {
                        cercaDIPxCognome:1,
                        cognome:$("#cercaXcognome").val()
                       },
                       function(data)
                       {
                        $("#adminContent").html(data);
                       });
                }
                else alert('Campo Vuoto');
                break;
        }
    });
});
//funzionamento new mat button
$(document).ready(function(){
   $("#adminContent").on("click","#newMatButton",function(){
     var currentTR=$(this).closest("tr");
     var go=confirm('Sei sicuro di voler cambiare la matricola?');
     if (go)
     {
        $(this).addClass('nascosto');
        $(currentTR).find("#newMatToAdd").removeClass('nascosto');
     }
   });
});
//funzionamento bottone EDIT
$(document).ready(function(){
    $("#adminContent").on('click','#EDITdiplist',function(){
        var currentTR=$(this).closest('tr');
        var matricola=$(currentTR).find("#matUtenteRec").text();
        var cognome=$(currentTR).find("#cognomeUtenteRec").val();
        var nome=$(currentTR).find("#nomeUtenteRec").val();
       
        if ($(currentTR).find("#newMatToAdd").hasClass('nascosto'))
        {
            var nuovaMat='NO';
        }
        else  var nuovaMat=$(currentTR).find("#newMatToAdd").val();
        if (matricola=='' || cognome=='' || nome=='' || nuovaMat=='')
        {
            alert('Campi matricola e/o cognome,nome vuoti!');
        }
        else
        {
            $.post("core/funcs.php",
                   {
                    editDipendente:1,
                    matricola:matricola,
                    cognome:cognome,
                    nome:nome,
                    nuovaMat:nuovaMat
                   },
                   function(data)
                   {
                    if (data[0]=='yes')
                    {
                        $(currentTR).find(".DIP-CD").hide("slow");
                    }
                    alert(data[1]);
                   },"json"
                  );
        }
    });
});