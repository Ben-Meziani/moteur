$(function () {
    $("#navbarUl").show();

    let isMobile = false; //initiate as false
    // device detection
    if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|ipad|iris|kindle|Android|Silk|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(navigator.userAgent)
        || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(navigator.userAgent.substr(0,4))) {
        isMobile = true;
    }
    if(isMobile === true){
        $('.connexionNotMobile').hide();
        $('.connexionMobile').show();
    } else {
        $('.connexionNotMobile').show();
        $('.connexionMobile').hide();
    }

    if($("#navbarUl").width() > 1000 && isMobile === false){
        let widthNavMax = 700;
        let widthNav = 0;
        let elemntToAdd = 0;
        $("#navbarUl li.nav-item").each(function(index) {
            if((widthNav + $(this).width()) < widthNavMax){
                widthNav += $(this).width();
            } else {
                if(elemntToAdd === 0){
                    elemntToAdd = index-1;
                }
            }
        });

        if(elemntToAdd !== 0){

            let newLi = document.createElement('li');
            newLi.setAttribute('class','nav-item dropdown');
            newLi.innerHTML='<a id="collapsePagesMore" href="#" ' +
                'data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" ' +
                'class="nav-link dropdown-toggle" role="button"><i class="fas fa-ellipsis-h"></i></a>';
            let newUl = document.createElement("ul");
            newUl.setAttribute('aria-labelledby', 'collapsePagesMore');
            newUl.setAttribute('class', "dropdown-menu custom-dropdown border-0 shadow");

            let allLi = [];
            for (let d = 0; d < ($("#navbarUl li.nav-item:gt('"+elemntToAdd+"')").length); d++) {
                allLi.push($("#navbarUl li.nav-item:gt('"+elemntToAdd+"')")[d]);

            }

            for (let d = 0; d < (allLi).length; d++) {
                if(allLi[d].className === "nav-item dropdown"){
                    allLi[d].className = "dropdown-submenu submenu-left";
                    if(allLi[d].children[0].className === "nav-link dropdown-toggle"){
                        allLi[d].children[0].className = "dropdown-item dropdown-toggle"
                    }
                }


                newUl.appendChild(allLi[d])
            }

            newLi.appendChild(newUl);
            $('#navbarUl').children().each(function () {

                if($(this).hasClass("topbar-divider") && $(this).is(':last-child')){
                    $(this).remove();
                } else if($(this).hasClass("topbar-divider") && $(this).prev().hasClass("topbar-divider")) {
                    $(this).remove();
                }
            });

            // $("#navbarUl").append('<div class="topbar-divider d-none d-sm-block"></div>');

            $("#navbarUl").append(newLi);

        }

    }


    // ------------------------------------------------------- //
    // Multi Level dropdowns
    // ------------------------------------------------------ //
    $("ul.dropdown-menu [data-toggle='dropdown']").on("click", function(event) {
        if (!$(this).next().hasClass('show')) {
            $(this).parents('.dropdown-menu').first().find('.show').removeClass("show");
        }
        var $subMenu = $(this).next(".dropdown-menu");
        $subMenu.toggleClass('show');
        $(this).parents('li.nav-item.dropdown.show').on('hidden.bs.dropdown', function(e) {
            $('.dropdown-submenu .show').removeClass("show");
        });
        return false;
    });


});


setTimeout(function () {
    jQuery('#bloc_info').css('display', 'none');
}, 4000);


$(function () {
    $('a').popover()
});

$("select[name=ParPage]").change(function () {
    if (this.checked) {
        this.form.submit();
    } else {
        this.form.submit();
    }
});

$(".groupe").change(function () {
    if (this.checked) {
        this.form.submit();
    } else {
        this.form.submit();
    }
});

$(".min").change(function () {
    let maxId = $(this).attr('id').replace('min', 'max');
    if($('#'+maxId).length){
        document.getElementById(maxId).setAttribute('min',this.value);
    }else{
        this.form.submit();
    }

});


$(".input_date2_val").change(function () {
    let minId = $(this).attr('id').replace('max', 'min');
    var minVal = $("#"+minId).val();

    if (!Date.parse(minVal)) {
        $( "#"+minId ).animate({
                backgroundColor: "#e64a3b",
                color:"#fffff",
            }, 500,
            function(){ $("#"+minId ).animate({
                backgroundColor: "#ffffff",
                color:"#000000",
            }, 500 ) }
        );
    } else{
        this.form.submit();
    }





});
function AfficherMasquer() {
    divInfo = document.getElementById('menu_debug');
    if (divInfo.style.display == 'none') {
        divInfo.style.display = 'block';
    } else {
        divInfo.style.display = 'none';
    }
}

function AfficherModal() {
    $('#modalAddUser').modal('show');
}


function AfficherModalPlinfIter() {
    $('#modalCalendar').modal('show');
}

function AfficherModalPlinfIterEdit() {
    $('#modalCalendarEdit').modal('show');
}

function modalDelInter() {
    $('#modalDelInter').modal('show');
}



var responseOptionCharged = new Array();

function selectFromListItem(xls, alias, key, id, valeurDefaut) {
    if(responseOptionCharged.indexOf(alias+"-"+key+"-"+id+"-"+valeurDefaut)==-1) {
        responseOptionCharged.push(alias+"-"+key+"-"+id+"-"+valeurDefaut);

        $.ajax({
            url: "index.php?p=Principal.callAjaxForSelectFromListItem&idItem="+ id +"&key=" + key + "&alias=" + alias +"&XLS="+xls+"&idOptiondefaut="+ valeurDefaut,
            type: 'GET',
            dataType: "html",
            success: function (data) {

                $('#'+alias+'_'+id+'option[class!="oldNoteValue"]').remove();

                // Affichage des option dans la vue avec les bonnes données.
                $('#'+alias+'_'+id).append(data);
                if(valeurDefaut !== ""){
                    $('#'+alias+'_'+id+' option[value="'+valeurDefaut+'"][class!="valueFromAjax"]').attr('selected', true);
                    $('#'+alias+'_'+id+' option[value="'+valeurDefaut+'"][class!="valueFromAjax"]').css('background-color', '#d1d3e2');
                }

            },
        });
    }
}

function updateSelectFromListItem(xls, alias, key, id, valeur, updateFieldsOnchange) {
    // Requete envoyé pour update l'infos.
    $.ajax({
        url: "index.php?p=Principal.updateSelectFromListItem&idItem="+ id +"&key=" + key + "&alias=" + alias +"&idOption=" + valeur +"&XLS="+xls,
        type: 'GET',
        dataType: "html",

        success: function (data) {
            data = JSON.parse(data);
            if(data){
                let inputItem =  "#"+alias+"_"+id
                if($( "#"+alias+"_"+id ).length === 0 ){
                    inputItem =  "#inputItem"+alias+"_"+id
                }
                $(inputItem).popover('dispose');
                if(data.return === 1 && (data.error === null)) {
                    $(inputItem).animate({
                            backgroundColor: "#207446",
                            borderColor: "#d1d3e2",
                            color:"#fffff",
                        }, 500,
                        function(){ $(inputItem ).animate({
                            backgroundColor: "#ffffff",
                            borderColor: "#d1d3e2",
                            color:"#000000",
                        }, 500 ) }
                    );
                    $(inputItem).parent().css('background-color','transparent');
                    $(inputItem).find('.valueFromAjax').remove();
                    if(JSON.parse(updateFieldsOnchange).length > 0 && key.includes('note')){
                        $.ajax({
                            url: "index.php?p=Principal.updateColumnsOnChange",
                            type: 'POST',
                            dataType:'json',
                            data: {
                                idItem : id ,
                                XLS : xls,
                                oldChampValue : $('#'+xls+'_note_mois_actuel_'+id).html().trim(),
                            },


                            success: function (data) {
                                for (const [key, value] of Object.entries(data)) {
                                    if($("#"+xls+"_"+key+"_"+id).is( "input" ) ){
                                        if($("#"+xls+"_"+key+"_"+id).attr('data-onchange') !== undefined){
                                            $("#"+xls+"_"+key+"_"+id).attr('placeholder', value);
                                        } else {
                                            $("#"+xls+"_"+key+"_"+id).val(value);
                                        }
                                    } else {
                                        $("#"+xls+"_"+key+"_"+id).html(value);
                                    }
                                    $("#"+xls+"_"+key+"_"+id).parent().animate({
                                            borderColor: "#207446",
                                            borderWidth: 2
                                        }, 1000,
                                        function(){$("#"+xls+"_"+key+"_"+id).parent().animate({
                                            borderColor: "#e3e6f0",
                                            borderWidth: 1
                                        }, 1000 ) });
                                }
                            }
                        });
                    }


                } else if(data.return === 0 && (data.error !== null)) {
                    $(inputItem).popover({ toggle:"popover", trigger: 'focus' ,content: '<div class="error-popover">'+data.error+'</div>', html: true, placement: 'bottom'});
                    $(inputItem).focus();
                    $(inputItem).animate({
                            backgroundColor: "#e64a3b",
                            borderColor: "#e64a3b",
                            color:"#fffff",
                        }, 500,
                        function(){ $(inputItem).animate({
                            backgroundColor: "#ffffff",
                            borderColor: "#e64a3b",
                            color:"#000000",
                        }, 500 ) }
                    );
                }

            }

        },

    });

}


function updateListItem(xls, key, id,typeInput, valeur,dateException = null ) {

    if(typeInput === 'datetime'){
        if(valeur.length === 5 ){

            dateException =  $("input[name="+dateException+"]").val();
            valeur  = dateException+' ' + valeur+':00';
        }
        else{
            dateException =  $("input[name="+dateException+"]").val();
            valeur = valeur +' '+dateException+':00';
        }
    }


    // Requete envoyé pour update l'infos.
    $.ajax({
        url: "index.php?p=Principal.updateListItem",
        type: 'POST',
        data: {
            idItem : id ,
            key:  key,
            value : valeur,
            XLS : xls
        },
        dataType: "html",

        success: function (data) {
            data = JSON.parse(data);
            if(data){
                let selector =  "#"+xls+"_"+key+"_"+id;
                $(selector).removeClass('calculated');
                if(typeInput === 'datetime'){
                    selector = '.dateTime'+id;
                }
                $(selector).popover('dispose');
                if(data.return === 1 && (data.error === null)) {
                    $(selector).animate({
                            backgroundColor: "#207446",
                            borderColor: "#d1d3e2",
                            color:"#fffff",
                        }, 500,
                        function(){$(selector).animate({
                            backgroundColor: "#ffffff",
                            borderColor: "#d1d3e2",
                            color:"#000000",
                        }, 500 ) }
                    );
                    if(data.calculVal){
                        if($(selector).attr('data-onchange') !== undefined){
                            $(selector).attr('placeholder', data.calculVal);
                        } else {
                            $(selector).val(data.calculVal);
                        }
                        $(selector).addClass('calculated');
                    }
                    if($(selector).attr('data-onchange') !== undefined && $(selector).attr('data-onchange') !== ''){
                        let fieldsUpdate = $(selector).attr('data-onchange').split(",");

                        $.ajax({
                                url: "index.php?p=Principal.updateColumnsOnChange",
                                type: 'POST',
                                dataType:'json',
                                data: {
                                    idItem : id ,
                                    XLS : xls,
                                    fieldsUpdate : fieldsUpdate,
                                    currentField : $(selector).attr('name'),
                                    oldChampValue : $('#'+xls+'_note_mois_actuel_'+id).html().trim(),

                                },
                                success: function (data) {
                                    for (const [key, value] of Object.entries(data)) {
                                        if($("#"+xls+"_"+key+"_"+id).hasClass('last_value')){
                                            $("#"+xls+"_"+key+"_"+id).removeClass('last_value')
                                        }
                                        if($("#"+xls+"_"+key+"_"+id).is( "input" ) ){
                                            $("#"+xls+"_"+key+"_"+id).val(value);
                                        } else {
                                            $("#"+xls+"_"+key+"_"+id).html(value);
                                        }
                                        $("#"+xls+"_"+key+"_"+id).parent().animate({
                                                borderColor: "#207446",
                                                borderWidth: 2
                                            }, 1000,
                                            function(){$("#"+xls+"_"+key+"_"+id).parent().animate({
                                                borderColor: "#e3e6f0",
                                                borderWidth: 1
                                            }, 1000 ) });
                                    }
                                }
                            });
                    }


                } else if(data.return === 0 && (data.error !== null)) {
                    $(selector).popover({ toggle:"popover", trigger: 'focus' ,content: '<div class="error-popover">'+data.error+'</div>', html: true, placement: 'bottom'});
                    $(selector).focus();
                    $(selector).animate({
                            backgroundColor: "#e64a3b",
                            borderColor: "#e64a3b",
                            color:"#fffff",
                        }, 500,
                        function(){ $(selector).animate({
                            backgroundColor: "#ffffff",
                            borderColor: "#e64a3b",
                            color:"#000000",
                        }, 500 ) }
                    );
                }
            }


        },

    });

}



var responseOptionChargedUser = new Array();

function selectFromListUser(xls, alias, key, id, valeurDefaut) {

    if(responseOptionChargedUser.indexOf(alias+"-"+key+"-"+id+"-"+valeurDefaut)==-1) {
        responseOptionChargedUser.push(alias+"-"+key+"-"+id+"-"+valeurDefaut);
        $.ajax({
            url: "index.php?p=Users.callAjaxForSelectFromListItem&idItem="+ id +"&key=" + key + "&alias=" + alias +"&XLS="+xls+"&idOptiondefaut="+ valeurDefaut,
            type: 'GET',
            dataType: "html",
            success: function (data) {

                // Affichage des option dans la vue avec les bonnes données.
                $('#'+alias+'_'+id).append(data);
            },
        });

    }
}

function updateSelectFromListUser(xls, alias, key, id, valeur) {

    // Requete envoyé pour update l'infos.
    $.ajax({
        url: "index.php?p=Users.updateSelectFromListItem&idItem="+ id +"&key=" + key + "&alias=" + alias +"&idOption=" + valeur +"&XLS="+xls,
        type: 'GET',
        dataType: "html",

        success: function (data) {
            data = JSON.parse(data);
            if(data){
                let inputItem =  "#"+alias+"_"+id
                if($( "#"+alias+"_"+id ).length === 0 ){
                    inputItem =  "#inputItem"+alias+"_"+id
                }
                $(inputItem).popover('dispose');
                if(data.return === 1 && (data.error === null)) {
                    $(inputItem).animate({
                            backgroundColor: "#207446",
                            borderColor: "#d1d3e2",
                            color:"#fffff",
                        }, 500,
                        function(){ $(inputItem ).animate({
                            backgroundColor: "#ffffff",
                            borderColor: "#d1d3e2",
                            color:"#000000",
                        }, 500 ) }
                    );
                } else if(data.return === 0 && (data.error !== null)) {
                    $(inputItem).popover({ toggle:"popover", trigger: 'focus' ,content: '<div class="error-popover">'+data.error+'</div>', html: true, placement: 'bottom'});
                    $(inputItem).focus();
                    $(inputItem).animate({
                            backgroundColor: "#e64a3b",
                            borderColor: "#e64a3b",
                            color:"#fffff",
                        }, 500,
                        function(){ $(inputItem).animate({
                            backgroundColor: "#ffffff",
                            borderColor: "#e64a3b",
                            color:"#000000",
                        }, 500 ) }
                    );
                }

            }

        },

    });

}

function updateListUsersItem(xls, key, id,typeInput, valeur,dateException = null ) {

    if(typeInput === 'datetime'){
        if(valeur.length === 5 ){

            dateException =  $("input[name="+dateException+"]").val();
            valeur  = dateException+' ' + valeur+':00';
        }
        else{
            dateException =  $("input[name="+dateException+"]").val();
            valeur = valeur +' '+dateException+':00';
        }
    }


    // Requete envoyé pour update l'infos.
    $.ajax({
        url: "index.php?p=Users.updateListItem&idItem="+ id +"&key=" + key +"&value=" + valeur +"&XLS="+xls,
        type: 'GET',
        dataType: "html",

        success: function (data) {
            data = JSON.parse(data);
            if(data){
                let selector = "#"+xls+"_"+key+"_"+id;
                if(typeInput === 'datetime'){
                    selector = '.dateTime'+id;
                }
                $(selector).popover('dispose');
                if(data.return === 1 && (data.error === null)) {
                    $(selector).animate({
                            backgroundColor: "#207446",
                            borderColor: "#d1d3e2",
                            color:"#fffff",
                        }, 500,
                        function(){$(selector).animate({
                            backgroundColor: "#ffffff",
                            borderColor: "#d1d3e2",
                            color:"#000000",
                        }, 500 ) }
                    );
                } else if(data.return === 0 && (data.error !== null)) {
                    $(selector).popover({ toggle:"popover", trigger: 'focus' ,content: '<div class="error-popover">'+data.error+'</div>', html: true, placement: 'bottom'});
                    $(selector).focus();
                    $(selector).animate({
                            backgroundColor: "#e64a3b",
                            borderColor: "#e64a3b",
                            color:"#fffff",
                        }, 500,
                        function(){ $(selector).animate({
                            backgroundColor: "#ffffff",
                            borderColor: "#e64a3b",
                            color:"#000000",
                        }, 500 ) }
                    );
                }
            }
        },

    });

}



/*--------------------------------------------------------------------------------------------------------------------*/
/* Formulaire Autocomplétion  ----------------------------------------------------------------------------------------*/
/*--------------------------------------------------------------------------------------------------------------------*/
var ArrayValSecto = [];
var cache = {};
$(function () {

    //console.log(siteDejaAffi);

    $(".autocompSecto").autocomplete({
        source: function (request, response) {

            var conf = $(this.element).attr('data-conf');
            var champs = $(this.element).attr('data-champs');
            var key = $(this.element).attr('data-key');
            var siteDejaAffi = $(".div_champ_secto_site").find('.span_site').attr('data-item');


            objData = {[champs]: request.term, maxRows: 10};
            $.ajax({
                url: "index.php?p=Users.listUsers&conf=" + conf + "&champs=" + champs + "&label=" + champs,
                dataType: "json",
                data: objData,
                type: 'POST',
                success: function (data) {
                    //Ajout de reponse dans le cache
                    cache[(request.term)] = data;
                    response($.map(data, function (item) {
                        $('.ui-autocomplete').css('z-index', 99999999999999);
                        return {
                            [champs]: item.label,
                            value: function () {
                                if ($(this).attr('id') == 'inputItem' + conf) {
                                    $("#labelDisable" + conf).val(item.label);
                                    $("#labelhidden" + conf).val(item.idItem);
                                    var getClass = $('.div_champ_secto_site').find('.span_site').text();
                                    if (!getClass.includes(item.label)) {
                                        $("#div_champ_secto_site").prepend('<span class="span_site" data-item="' + item.label + '">' + item.label + '<em class="delete_site">x</em><input type="hidden" id="inputHidden' + conf + '" class="inputHiddenSite" value="' + item.idItem + '" name="' + key + '[]"/><input type="hidden" id="inputHidden' + conf + '" class="inputHiddenSite" value="' + item.label + '" name="' + key + '_label[]"/></span>');
                                    }
                                    return item.label;
                                } else {
                                    $(this.element).attr('value', item.label);
                                    return item.label;
                                }
                            }
                        }
                    }));
                }
            });

        },
        minLength: 1, // Nb de caractère à taper pour déclancher la requête.
        delay: 600
    });
});

$(".autocompSecto").keyup(function () {
    var conf = $(this).attr('data-conf');
    $("#labelDisable" + conf).val('');
    $("#labelhidden" + conf).val('');
});
var divSecto = $("#div_champ_secto_site");
divSecto.on('click', '.delete_site', function () {
    $(this).closest('.span_site').remove();
});

$(".autocomp").keyup(function () {

    var alias = $(this).attr('data-alias');
    $("#labelDisable" + alias).val('');
    $("#labelhidden" + alias).val('');
});

$(function () {

    $(".autocomp").autocomplete({
        source: function (request, response) {
            var confName = $(this.element).attr('data-confName');
            var conf = $(this.element).attr('data-conf');
            var champs = $(this.element).attr('data-champs');
            var alias = $(this.element).attr('data-alias');
            var key = $(this.element).attr('data-key');
            objData = {[champs]: request.term, maxRows: 10};

            $.ajax({
                url: "index.php?p=Users.editUser&XLS=" + confName + "&conf=" + conf + "&champs=" + champs + "&label=" + alias + "&key=" + key + "&autocomp=1",
                dataType: "json",
                data: objData,
                type: 'POST',
                success: function (data) {
                    //Ajout de reponse dans le cache
                    cache[(request.term)] = data;
                    response($.map(data, function (item) {
                        $('.ui-autocomplete').css('z-index', 99999999999999);
                        return {
                            [champs]: item.label,
                            value: function () {
                                if ($(this).attr('id') == 'inputItem' + alias) {
                                    $("#labelDisable" + alias).val(item.label);
                                    $("#labelhidden" + alias).val(item.idItem);
                                    return item.label;
                                } else {
                                    $(this.element).attr('value', item.label);
                                    return item.label;
                                }
                            }
                        }
                    }));
                }
            });

        },
        minLength: 1, // Nb de caractère à taper pour déclancher la requête.
        delay: 600
    });
});



$(".changeSelected").on('change', function () {
    let selectorChange = $(this).attr('data-input');
    let idSelect = $(this).attr('id');
    $('#'+selectorChange).val($('#'+idSelect+' option:selected').text());
});



$(".autocompPrincipal").keyup(function () {
    var alias = $(this).attr('data-alias');
    $("#labelDisable" + alias).val('');
    $("#labelhidden" + alias).val('');
});

$(".onCalcul").keyup(function () {
    if($(this).val() != ''){
        $(this).removeClass('calculated')
    } else {
        $(this).addClass('calculated')
    }
})


$(function () {

    $(".autocompPrincipal").autocomplete({
        source: function (request, response) {
            var confName = $(this.element).attr('data-confName');
            var conf = $(this.element).attr('data-conf');
            var champs = $(this.element).attr('data-champs');
            var alias = $(this.element).attr('data-alias');
            var key = $(this.element).attr('data-key');
            objData = {[champs]: request.term, maxRows: 10};

            $.ajax({
                url: "index.php?p=Principal.addItem&XLS=" + confName + "&conf=" + conf + "&champs=" + champs + "&label=" + alias + "&key=" + key + "&autocomp=1",
                dataType: "json",
                data: objData,
                type: 'POST',
                success: function (data) {
                    //Ajout de reponse dans le cache
                    cache[(request.term)] = data;
                    response($.map(data, function (item) {
                        $('.ui-autocomplete').css('z-index', 99999999999999);
                        return {
                            [champs]: item.label,
                            value: function () {
                                if ($(this).attr('id') == 'inputItem' + alias) {
                                    $("#labelDisable" + alias).val(item.label);
                                    $("#labelhidden" + alias).val(item.idItem);
                                    return item.label;
                                } else {
                                    $(this.element).attr('value', item.label);
                                    return item.label;
                                }
                            }
                        }
                    }));
                }
            });

        },
        minLength: 1, // Nb de caractère à taper pour déclancher la requête.
        delay: 600
    });
});



/* Fin Autocomplétion  -----------------------------------------------------------------------------------------------*/
/*--------------------------------------------------------------------------------------------------------------------*/

/*--------------------------------------------------------------------------------------------------------------------*/
/* Détails & Historique  ---------------------------------------------------------------------------------------------*/
/*--------------------------------------------------------------------------------------------------------------------*/

$("#lienModal").click(function () {
    let urlAjax = "index.php?p=Users.editUser&historique=1&id_item=" + $('#lienModal').attr('data-ajax') + "&nom_feuille=" + $('#lienModal').attr('data-feuille');
    if($('#lienModal').attr('data-feuille') !== "Utilisateurs"){
        urlAjax = "index.php?p=Principal.editItem&historique=1&id_item=" + $('#lienModal').attr('data-ajax') + "&nom_feuille=" + $('#lienModal').attr('data-feuille');
    }
    $.ajax({
        url: urlAjax,
        type: 'POST',
        dataType: "html",

        success: function (data) {

            $('#div_data_historique').html(data)
        },

    });

});
var lenghtSelectedInput=null;
if (typeof selectedInput !== 'undefined' || typeof selectedInput === "string"){
    lenghtSelectedInput = JSON.parse(selectedInput);
    selectedInput = JSON.parse(selectedInput);
}
function handleLimit(evt) {
    lenghtSelectedInput.push(evt['id'])
}
$('.div_checkbox_dep_nro input').on('change', function (e) {
    e.preventDefault();
    handleLimit(this);
})
function EditDemo() {
    lenghtSelectedInput.forEach(function (id) {
        if (!$('#' + id).length) {
            return $("<input />").attr("type", "checkbox")
                .attr("name", "SectoId[]")
                .attr("value", id)
                .attr("id", id)
                .attr("checked", true)
                .appendTo("form");
        }
    });
    return true;
}
$('#nroFilter').keyup(function () {
    if ($("#nroFilter").val()){
        $('.div_champ_secto_site_nro').show();
        filterListNro();
    }else{
        $('.div_champ_secto_site_nro').hide();
    }


});
function filterListNro() {
    $.ajax({
        url: "index.php?p=CasParticulier.nrosList",
        type: 'POST',
        data: {
            name: $("#nroFilter").val()
        },

        success: function (data) {
            inputs = [];
            JSON.parse(data).forEach(function (el) {
                divDOM = document.createElement('div')
                if (selectedInput.includes(el.id)) {
                    divDOM.style.backgroundColor = '#81BAE0'
                    divDOM.style.color = '#ffffff'
                }
                inputDOM = document.createElement('input')
                inputDOM.setAttribute('id', el.id)
                inputDOM.setAttribute('type', 'checkbox')
                inputDOM.setAttribute('value', el.id)
                inputDOM.setAttribute('name', 'SectoId[]')
                inputDOM.addEventListener('change', function () {
                    handleLimit(this);
                })
                inputDOM.checked = lenghtSelectedInput.includes(el.id)
                labelDOM = document.createElement('label')
                labelDOM.setAttribute('for', el.id)
                labelDOM.innerHTML = el.name
                divDOM.appendChild(inputDOM)
                divDOM.appendChild(labelDOM)
                inputs.push(divDOM)
            })
            $('.div_checkbox_dep_nro').html(inputs)
        },

    });
}
function showDeleteNroAffected(id) {
    $(".modal-body #idNroTodelete").val( id );
    $('#affectAlertSucces').hide();
    $('#affectAlertFail').hide();
    $('#modalDelAffectNro').modal("show");
}
function deleteNroAffected(id_user) {
    $.ajax({
        url: "index.php?p=CasParticulier.deleteAffectedNro",
        type: 'POST',
        data: {
            id_site: $('#idNroTodelete').val(),
            id_user: id_user
        },

        success: function (data) {
            if (data){
                filterListNro();
                if(lenghtSelectedInput.includes($('#idNroTodelete').val())){
                    lenghtSelectedInput.splice(lenghtSelectedInput.indexOf($('#idNroTodelete').val()), 1);
                }
                if(selectedInput.includes($('#idNroTodelete').val())){
                    selectedInput.splice(selectedInput.indexOf($('#idNroTodelete').val()), 1);
                }
                $( "#affectedNros" ).load( document.URL+" #affectedNros" );
                $('#affectAlertSucces').show();
                setTimeout(function(){
                    $('#modalDelAffectNro').modal('hide');
                }, 800);


            }else {
                $('#affectAlertFail').show();
                setTimeout(function(){
                    $('#modalDelAffectNro').modal('hide');
                }, 800);
            }
        },

    });
}

/*--------------------------------------------------------------------------------------------------------------------*/
/* Formulaire <-> input file  ----------------------------------------------------------------------------------------*/
/*--------------------------------------------------------------------------------------------------------------------*/
(function ($) {
    $(document).ready(function () {
        $.fn.tooltip.Constructor.Default.whiteList['*'].push('style');
        uploadImage();

        function uploadImage() {

            $('.ico_poubelle').hover(function () {
                $(this).parents('.img').toggleClass('nonHover');
                $(this).parent().find('.span_delete').toggleClass('span_delete2');

            });


            // Le div "Ajouter un fichier" devient le bouton qui permet de séléctionner un fichier.
            var button = $('.images .pic');
            // Assigniation de l'input type file à la variable uploader.
            var uploader = $('.fileUpload');
            // Assigniation du contenue de la div images à la variable.
            var images = $('.images');


            //  var detachElement =  jQuery('.notificationMax2').detach();
            //  jQuery('.notificationMax').prepend(detachElement);

            // Déclanche l'input type file quand on clic sur la div button.
            button.on('click', function () {
                var uploaderbis = $(this).parents('.sections').find('.fileUpload');
                uploaderbis.click();

            });


            // Quand une image a été séléctionnée affichage dans une div.
            uploader.on('change', function () {

                var uploaderbis = $(this).parents('.sections').find('.fileUpload');
                var imagesbis = $(this).parents('.sections').find('.images');

                var imageBisEdit = $(this).parents('.sections').find('.div_new_img');

                var nbFileMax = uploaderbis.attr('data-nbFile');
                var nameInputFile = uploaderbis.attr('data-nameInput');
                var extInputFileData = uploaderbis.attr('data-nameExt');
                var extInputFile = extInputFileData + '_ext[]';
                var NameFichier = extInputFileData + '_Name[]';
                var fic = uploaderbis[0]['value'];
                var ext = fic.split('.').reverse();
                var nameFileArray = fic.split('\\').reverse();
                var nameFile = nameFileArray[0].split('.');
                var extSub = String(ext).substring(0, 3);
                var nameFileView = nameFile[0].substring(0, 10) + '.' + extSub;
                var patchDefaultImg = "../public/image/forfile/" + extSub + ".png";

                var url = document.location.search;
                var paramUrl = url.search('editItem');
                var paramUrl2 = url.search('editUser');

                var getContainer = $(this).parents('.sections').find('.img');
                nbContainer = getContainer.length;

                var test = $(this).parents('.sections').find('.data_section').attr('value');

                var reader = new FileReader();
                reader.onload = function (event) {

                    if (paramUrl > 0 || paramUrl2 > 0) {
                        if (uploaderbis[0].files[0]['type'] == 'image/jpeg' ||  uploaderbis[0].files[0]['type'] == 'image/png') {
                            imageBisEdit.prepend('<div class="img imgdelete nonHover" style="background-image: url(' + event.target.result + '); margin-right: 10px;" rel="' + event.target.result + '"><input class="file_hidden" type="hidden" name="' + nameInputFile + '" value="' + event.target.result + '"/><input type="hidden" name="' + extInputFile + '" value="' + ext[0] + '"/><input type="hidden" name="' + NameFichier + '" value="' + nameFile[0] + '"/><span class="ticket_new">Nouveau</span><span class="nom_file">' + nameFileView + '</span><span data-toggle="modal" data-target="#modalDeleteItem"  class="ico_poubelle"><i class="far fa-fw fa-trash-alt"></i></span></div>');
                        } else {
                            imageBisEdit.prepend('<div class="img imgdelete nonHover" style="background-image: url(' + patchDefaultImg + '); margin-right: 10px;" rel="' + event.target.result + '"><input class="file_hidden" type="hidden" name="' + nameInputFile + '" value="' + event.target.result + '"/><input type="hidden" name="' + extInputFile + '" value="' + ext[0] + '"/><input type="hidden" name="' + NameFichier + '" value="' + nameFile[0] + '"/><span class="ticket_new">Nouveau</span><span class="nom_file">' + nameFileView + '</span><span data-toggle="modal" data-target="#modalDeleteItem"  class="ico_poubelle"><i class="far fa-fw fa-trash-alt"></i></span></div>');
                        }
                    } else {
                        if (uploaderbis[0].files[0]['type'] == 'image/jpeg' || uploaderbis[0].files[0]['type'] == 'image/png') {
                            imagesbis.prepend('<div class="img imgdelete" style="background-image: url(' + event.target.result + '); margin-right: 10px;" rel="' + event.target.result + '"><span class="span_delete span_delete_add">Supprimer</span><input class="file_hidden" type="hidden" name="' + nameInputFile + '" value="' + event.target.result + '"/><input type="hidden" name="' + extInputFile + '" value="' + ext[0] + '"/><input type="hidden" name="' + NameFichier + '" value="' + nameFile[0] + '"/><span class="nom_file">' + nameFileView + '</span></div>');
                        } else {
                            imagesbis.prepend('<div class="img imgdelete" style="background-image: url(' + patchDefaultImg + '); margin-right: 10px;" rel="' + event.target.result + '"><span class="span_delete span_delete_add">Supprimer</span><input class="file_hidden" type="hidden" name="' + nameInputFile + '" value="' + event.target.result + '"/><input type="hidden" name="' + extInputFile + '" value="' + ext[0] + '"/><input type="hidden" name="' + NameFichier + '" value="' + nameFile[0] + '"/><span class="nom_file">' + nameFileView + '</span></div>');
                        }
                    }

                    test++;
                    uploaderbis.parents('.sections').find('.data_section').attr('value', test);
                    //uploaderbis.attr('data-section', test);


                    if (test >= nbFileMax) {
                        imagesbis.parents('.sections').find('.notificationMax').html(nbFileMax + ' fichiers MAX');
                        imagesbis.parents('.sections').find('.pic').fadeOut("slow");
                    }

                };
                reader.readAsDataURL(uploaderbis[0].files[0]);


            });


            var getSuppresionClass = '';
            // Permet de supprimer un fichier après upload.
            images.on('click', '.ico_poubelle', function () {
                $(this).parents('.img').addClass('suppression');
                getSuppresionClass = $('#wrapper').find('.suppression');
            });


            // Permet de supprimer un fichier après upload.
            images.on('click', '.span_delete_add', function () {

                var testSup = $(this).parents('.sections').find('.data_section').attr('value');
                testSup--;
                $(this).closest('.wrapper').find('.data_section').attr('value', testSup);
                $(this).closest('.wrapper').find('.notificationMax').html('');
                $(this).closest('.wrapper').find('.pic').fadeIn("slow");
                $(this).parents('.img').remove();

            });


            // Permet de supprimer tous les fichiers après upload.
            $('.reset').on('click', function () {
                $(this).closest('.wrapper').find('.images .img').remove();
                testSup = 0;
                $(this).closest('.wrapper').find('.data_section').attr('value', testSup);
                $(this).closest('.wrapper').find('.notificationMax').html('');
                $(this).closest('.wrapper').find('.pic').fadeIn("slow");
            });


            // Permet de supprimer un fichier après upload.
            images.on('click', '.imgdl', function () {
                $(this).find('.dl_a').click();
            });

            // Permet de supprimer un fichier après upload.
            $('.confirmDelItem').on('click', function () {

                var nbFileMaxSup = $('.suppression').parents('.sections').find('.fileUpload').attr('data-nbFile');
                var testSup = $('.suppression').parents('.sections').find('.data_section').attr('value');

                var id_item_ = getSuppresionClass.find('.ico_poubelle').attr('data-id_item');
                var conf_file_ = getSuppresionClass.find('.ico_poubelle').attr('data-confFile');
                var champ_file_ = getSuppresionClass.find('.ico_poubelle').attr('data-champFile');
                var champ_xls_ = $('.confirmDelItem').attr('data-xls');

                testSup--;
                $('.suppression').parents('.sections').find('.data_section').attr('value', testSup);

                if (testSup < nbFileMaxSup) {

                    $('.suppression').parents('.sections').find('.notificationMax').html('');
                    $('.suppression').parents('.sections').find('.notificationMax2').html('');
                    $('.suppression').parents('.sections').find('.pic').fadeIn("slow");

                }

                $('.suppression').remove();

                if (champ_xls_ == 'Users') {
                    $.post(
                        'index.php?p=Users.deleteFile',
                        {
                            id_item: id_item_,
                            conf_file: conf_file_,
                            champ_file: champ_file_
                        },
                    );
                } else {
                    $.post(
                        'index.php?p=Principal.deleteFile',
                        {
                            id_item: id_item_,
                            conf_file: conf_file_,
                            champ_file: champ_file_,
                            champ_xls: champ_xls_
                        },
                    );
                }
            });
        }

        //overflow on TOP datatable
        //-----------------------------------------------------------------------------------------//

        //overflow on TOP datatable
        //-----------------------------------------------------------------------------------------//
        $('.div1, .div2').width($('#dataTable').width());
        $('.wrapper1').on('scroll', function (e) {
            $('.wrapper2').scrollLeft($('.wrapper1').scrollLeft());
        });

        let oldScrollTop = $('.wrapper2').scrollTop();
        let oldScrollLeft = $('.wrapper2').scrollLeft();
        $('.wrapper2').on('scroll', function (e) {
                $('.wrapper1').scrollLeft($('.wrapper2').scrollLeft());

            $('#dataTable>tbody>tr').each(function(index, tr) {
                let color = $(tr).css( "background-color");
                if(color === 'rgba(0, 0, 0, 0)'){
                    color = '#f8f9fc';
                }
                $(tr).children(".sticky-column").addClass("sticky-col");
                $(tr).children(".sticky-column").css("background-color", color);
            });

            if(oldScrollTop != $('.wrapper2').scrollTop()) {
                $("#dataTable>thead").css("z-index",1 );
            }
            oldScrollTop = $('.wrapper2').scrollTop();
            oldScrollLeft = $('.wrapper2').scrollLeft();
        });
    })
})(jQuery);
