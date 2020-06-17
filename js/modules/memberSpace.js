/**
 * Created by administrator on 2016-03-16.
 */

function register(){
    callAjax('php/members/registerMember.php', {}, 'html', function(html){
        customModal('Inscription',html,'S\'enregistrer','Annuler',function(){
            var firstname = $('#newMemberFirstname').val();
            var lastname = $('#newMemberLastname').val();
            var email = $('#newMemberEmail').val();
            var phone = $('#newMemberPhone').val();
            var postalCode = $('#newMemberPostalCode').val();
            var nickname = $('#newMemberUsername').val();
            var gender = $('#genderSelector0').val();
            var birthdate = new Date($('#yearSelector0').val() + '-' + $('#monthSelector0').val()+ '-' + $('#daySelector0').val());
            var password = $('#newMemberPassword').val();
            if(canSubmitForm()) {
                callAjax('php/members/registerMember.php', {
                    firstname: firstname,
                    lastname: lastname,
                    email: email,
                    phone: phone,
                    nickname: nickname,
                    gender: gender,
                    birthdate: birthdate.getTime(),
                    password: password,
                    postalCode:postalCode
                }, 'html', function () {
                    hideCustom();
                    redirect('members.php');
                    showInfo('Information', 'Vous vous êtes bien enregistrer en tant que nouveau membre!');
                });
            }
        });
    });
}

function login(){
    callAjax('php/members/loginMember.php', {}, 'html', function(html){
        customModal('Connexion',html,'Se connecter','Annuler',function(){
            var email = $('#loginMemberEmail').val();
            var password = $('#loginMemberPassword').val();
            if(canSubmitForm()) {
                callAjax('php/members/loginMember.php', {
                    email: email,
                    password: password
                }, 'html', function () {
                    hideCustom();
                    refresh();
                    showInfo('Information', 'Vous vous êtes bien connecté!');
                }, function(){
                    showInfo('Erreur',"L'email ou le mot de passe est invalide...");
                });
            }
        });
    });
}

function logout(){
    showYesNo('Information','Voulez-vous vraiment vous déconnecter?', function(){
        callAjax('php/members/logoutMember.php', {}, 'html', function(){
            redirect('index.php');
            showInfo('Information', 'Vous vous êtes bien déconnecté!');
        });
    });
}

function editPublicMember(id){
    callAjax('php/members/editPublicMember.php', {id: id}, 'html', function (html) {
        replaceContentOf('MemberContent' + id, html);
    });
}

function saveEditPublicMember(id){
    var firstname = $('#newMemberFirstname'+id).val();
    var lastname = $('#newMemberLastname'+id).val();
    var email = $('#newMemberEmail'+id).val();
    var phone = $('#newMemberPhone'+id).val();
    var postalCode = $('#newMemberPostalCode'+id).val();
    var nickname = $('#newMemberUsername'+id).val();
    var gender = $('#genderSelector'+id).val();
    var birthdate = new Date($('#yearSelector'+id).val() + '-' + $('#monthSelector'+id).val()+ '-' + $('#daySelector'+id).val());
    if(canSubmitForm()) {
        callAjax('php/members/editPublicMember.php', {
            id: id,
            firstname: firstname,
            lastname: lastname,
            email: email,
            phone: phone,
            nickname: nickname,
            gender: gender,
            birthdate: birthdate.getTime(),
            postalCode:postalCode
        }, 'html', function(html){
            isEditing = false;
            showInfo('Information', 'Les modifications de votre compte ont bien été enregistrées!');
            callAjax('php/members/getPublicMember.php', {id: id},'html', function(html){
                replaceContentOf('MemberContent'+id, html);
            });
        });
    }
}

function cancelEditPublicMember(id){
    actualPackageId = id;
    if(isEditing){
        showYesNo('Modifications', 'Voulez-vous vraiment annuler vos modifications?',cancellingEditPublicMember);
    }else {
        cancellingEditPublicMember();
    }
}

function cancellingEditPublicMember(){
    isEditing = false;
    callAjax('php/members/getPublicMember.php', {id: actualPackageId},'html', function(html){
        replaceContentOf('MemberContent'+actualPackageId, html);
    });
}

function changePassword(id){
    callAjax('php/members/editMemberPassword.php', {id: id}, 'html', function (html) {
        customModal('Changement du mot de passe', html,'Changer','Annuler',function(){
            var oldPassword = $('#oldMemberPassword').val();
            var newPassword = $('#newMemberPassword').val();
            if(canSubmitForm() && newPassword){
                callAjax('php/members/editMemberPassword.php',{
                    id: id,
                    oldPassword:oldPassword,
                    newPassword:newPassword},'html', function(){
                    hideCustom();
                    refresh();
                    showInfo('Succès','Vous avez bien changer le mot de passe!<br><br>Veuillez vous connecter à nouveau avec votre nouveau mot de passe.');
                })
            }
        })
    });
}
