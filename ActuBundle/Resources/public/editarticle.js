$(document).ready(function () {
    
    var Sy = $(window).height();

    function clear_img(contenu) {
        // clear class img
        //introduit id sur img
        var result = contenu.replace(/(<img alt="(?:[a-zA-Z0-9-_éèçà@&ù]+)")( class="[a-zA-Z0-9-_éèçà@&ù ]+")?( height=".*?)?( id=".+?)?( src=.+?>)/g, function (str, p1, p2, p3, p4, p5) {
        //p1= image + alt
        //p2= class        del
        //p3= height       facultatif
        //p4= id           facultatif
        //p5= le reste
            if (/id="/.test(str)) {
                if (/height="/.test(str)) {
                    return (p1 + p3 + p4 + p5);
                }
                return (p1 + p4 + p5);
            }
            p4 = " id=\"i" + new Date().getTime() + Math.floor(Math.random() * 1000) + "\" ";
            if (/height="/.test(str)) {
                return (p1 + p3 + p4 + p5);
            }
            return (p1 + p4 + p5);
        });
        return result;
    }
//clic bouton peupler medium//
    $("#peuplermedium").on("click", function () {
        var datafull = CKEDITOR.instances.actubundle_actu_actuLgTa.getData();
        var result = clear_img(datafull);
        CKEDITOR.instances.actubundle_actu_actuLgTa.setData(result);
        CKEDITOR.instances.actubundle_actu_actuMdTa.setData(result);
    });

    //clic bouton peupler small//
    $("#peuplersmall").on("click", function () {
        var datamedium = CKEDITOR.instances.actubundle_actu_actuMdTa.getData();
        CKEDITOR.instances.actubundle_actu_actuSmTa.setData(datamedium);
    });

    //clic bouton peupler extra//
    $("#peuplerextra").on("click", function () {
        var datasmall = CKEDITOR.instances.actubundle_actu_actuSmTa.getData();
        CKEDITOR.instances.actubundle_actu_actuXsTa.setData(datasmall);
    });

    function img_center(contenu) {
        var result = contenu.replace(/(<p style="text-align: center;"><img )(.*?)( \/><\/p>)/g, function (str, p1, p2, p3, p4, p5) {
//p1= <p style="text-align: center;"><img 
//p2= attributs img
//p3=  /></p>
            return ("<p style=\"text-align: center;\"><img " + p2 + " class=\"marginauto\"" + p3);
        });
        return result;
    }
//ajuste formulaire avant envoie
    $("#valide_form").on('click', function (e) {
        e.preventDefault();
        if (CKEDITOR.instances.actubundle_actu_actuXsTa.getData() == false) {
            alert("ARTICLE INCOMPLET:\n\
            Créez le comtenu XS");
            return false;
        }
        var contenu_lg = CKEDITOR.instances.actubundle_actu_actuLgTa.getData();
        var result_lg = clear_img(contenu_lg);
        var result_lgM = img_center(result_lg);
        CKEDITOR.instances.actubundle_actu_actuLgTa.setData(result_lgM);

        var contenu_md = CKEDITOR.instances.actubundle_actu_actuMdTa.getData();
        var result_md = clear_img(contenu_md);
        var result_mdM = img_center(result_md);
        CKEDITOR.instances.actubundle_actu_actuMdTa.setData(result_mdM);

        var contenu_sm = CKEDITOR.instances.actubundle_actu_actuSmTa.getData();
        var result_sm = clear_img(contenu_sm);
        var result_smM = img_center(result_sm);
        CKEDITOR.instances.actubundle_actu_actuSmTa.setData(result_smM);

        var contenu_xs = CKEDITOR.instances.actubundle_actu_actuXsTa.getData();
        var result_xs = clear_img(contenu_xs);
        var result_xsM = img_center(result_xs);
        CKEDITOR.instances.actubundle_actu_actuXsTa.setData(result_xsM);

        $("form[name=actubundle_actu]").submit();
    });


    //gere taille ckeditor
    var heightck = Sy - 150;
    CKEDITOR.instances.actubundle_actu_actuLgTa.resize(1221, heightck, true);
    CKEDITOR.instances.actubundle_actu_actuMdTa.resize(1013, heightck, true);
    CKEDITOR.instances.actubundle_actu_actuSmTa.resize(789, heightck, true);
    CKEDITOR.instances.actubundle_actu_actuXsTa.resize(318, heightck, true);

});
