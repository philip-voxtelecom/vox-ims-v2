function GeneratePassword(passFieldN, passSize) {

    var noPunction = 1;
    var length = passSize;
    var sPassword = '';

    for (i=0; i < length; i++) {

        numI = getRandomNum();
        if (noPunction) {
            while (checkPunc(numI)) {
                numI = getRandomNum();
            }
        }

        sPassword = sPassword + String.fromCharCode(numI);
    }

    var passField = document.getElementById(passFieldN);

    passField.value = sPassword

    return true;
}

function getRandomNum() {

    // between 0 - 1
    var rndNum = Math.random()

    // rndNum from 0 - 1000
    rndNum = parseInt(rndNum * 1000);

    // rndNum from 33 - 127
    rndNum = (rndNum % 94) + 33;

    return rndNum;
}

function checkPunc(num) {

    if ((num >=33) && (num <=47)) {
        return true;
    }
    if ((num >=58) && (num <=64)) {
        return true;
    }
    if ((num >=91) && (num <=96)) {
        return true;
    }
    if ((num >=123) && (num <=126)) {
        return true;
    }

    return false;
}

function switch_style ( doc, css_title )
{
    var i, link_tag ;
    for (i = 0, link_tag = doc.getElementsByTagName("link") ;
        i < link_tag.length ; i++ ) {
        if ((link_tag[i].rel.indexOf( "stylesheet" ) != -1) &&
            link_tag[i].title) {
            link_tag[i].disabled = true ;
            if (link_tag[i].title == css_title) {
                link_tag[i].disabled = false ;
            }
        }
    }
}

function printpage (srcEl) {
    printWindow = window.open('','_blank','width=600,height=550,toolbar=0,menubar=0,location=0,directories=0,status=0,copyhistory=0,scrollbars=1');
    printWindow.document.write('<html><head><title>Print</title></head><body><div id="printContent"></div></body></html>');
    printWindow.document.close();
    detel=document.getElementById(srcEl);
    headID = printWindow.document.getElementsByTagName('head')[0];
    cssNode = printWindow.document.createElement('link');
    cssNode.type = 'text/css';
    cssNode.rel = 'stylesheet';
    cssNode.href = '/css/print.css';
    cssNode.media = 'print';
    cssNode2 = printWindow.document.createElement('link');
    cssNode2.type = 'text/css';
    cssNode2.rel = 'stylesheet';
    cssNode2.href = '/css/print.css';
    cssNode2.media = 'screen';
    headID.appendChild(cssNode);
    headID.appendChild(cssNode2);
    printel=printWindow.document.getElementById('printContent');
    printel.innerHTML=detel.innerHTML;
    printWindow.print();
}

function removeClass(el, classNames) {
    if(el && el.className && classNames){
        el.className = el.className.replace(new RegExp("\\b(" + classNames.replace(/\s+/g, "|") + ")\\b", "g"), " ").replace(/\s+/g, " ").replace(/^\s+|\s+$/g, "");
    }
}

function togglePrint(el) {
    if (el.className.match('noprint')) {
        removeClass(el,"noprint")
        el.className += " print"
        return
    }
    removeClass(el,"print")
    el.className += " noprint"
}

jQuery.noConflict();
(function($) {
    $(function(){
        $('.inputDateType').live('click', function() {
            $(this).datepicker({
                minDate: 0,
                dateFormat: 'yy-mm-dd',
                showOn:'both'
            }).focus();
        });
    });
    $(function(){
        $('.inputDateTypeUnrestricted').live('click', function() {
            $(this).datepicker({
                dateFormat: 'yy-mm-dd',
                showOn:'both'
            }).focus();
        });
    });
    $(function() {
        $('.monthYearPicker').live('click', function() {
            $(this).datepicker({
                changeMonth: true,
                changeYear: true,
                dateFormat: 'yy-mm',
                showButtonPanel: true,
                diabled: true,
                showMonthAfterYear: true,
                yearRange: "-15:+00",
                showOn:'focus',
                    onClose: function(dateText, inst) {
                        var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
                        var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
                        $(this).datepicker('setDate', new Date(year, month, 1));
                    }
            }).focus();
        });
    });
})(jQuery);


function initLoading() {
    //xajax.loadingFunction = showLoading;
    //xajax.doneLoadingFunction = hideLoading;
    xajax.callback.global.onRequest = function()
    {
        xajax.$('loading').style.display = 'block';
    };
    xajax.callback.global.onComplete = function()
    {
        xajax.$('loading').style.display = 'none';
    };
    
}

function ismaxlength(obj){
    var mlength=obj.getAttribute? parseInt(obj.getAttribute("maxlength")) : ""
    if (obj.getAttribute && obj.value.length>mlength)
        obj.value=obj.value.substring(0,mlength)
}