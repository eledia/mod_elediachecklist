$(function() {

    var btn = $("#btnKVB");
    btn.click(function() {
        btn.attr("disabled", "disabled"); // disable button
        window.setTimeout(function() {
            btn.removeAttr("disabled"); // enable button
        }, 4000 /* 2 sec */);
    });

    var btnKNB = $("#btnKNB");
    btnKNB.click(function() {
        btnKNB.attr("disabled", "disabled"); // disable button
        window.setTimeout(function() {
            btnKNB.removeAttr("disabled"); // enable button
        }, 4000 /* 2 sec */);
    });

    $('#selectExamDates').change(function(){

        var examId = $(this).val();

         changeExamDateDropdown(examId);
    });

    $("#btnAddTopic").click(function(){

        if ($("#topicDate").val() == "" || $("#topicName").val() == "" || $("#topictextmail").val() == "") {
            alert("ERROR: You must provide Name, Days and Text mail");
            $("#topicDays").val("");
            $("#topicName").val("");
            $("#topictextmail").val("");
            $("#topicIdEdit").val("");
            return;
        }

        var topicIdParamer = "";
        if ($("#topicIdEdit").val() != "")
            topicIdParamer = "&topicId=" + $("#topicIdEdit").val();

        $.get( "addnewtopic.php?name=" + $("#topicName").val() + "&date=" + $("#topicDays").val() + "&checklistId=" + $("#checkListId").html() + topicIdParamer + "&topictextmail=" + $("#topictextmail").val(), function( data ) {
            $("#topicDays").val("");
            $("#topicName").val("");
            $("#topicIdEdit").val("");
            $("#topictextmail").val("");
            $("#addTopic").modal("hide");
        });

        setTimeout(() => {  changeExamDateDropdown($('#exam_id').val(), $('#topicCourseId').val()); }, 2000);
    });

    $("#btnAddQMItem").click(function(){
        var QMIdParamer = "";
        if ($("#QMIdEdit").val() != "")
            QMIdParamer = "&QMId=" + $("#QMIdEdit").val();

        $.get( "addnewqmitem.php?name=" + $("#QMName").val()  + "&checklistId=" + $("#checkListId").html() + QMIdParamer, function( data ) {
            $("#QMName").val("");
            $("#QMIdEdit").val("");
            $("#addQMItem").modal("hide");
        });

        setTimeout(() => {  location.reload(); }, 2000);
    });

    $("#btnAddEAItem").click(function(){
        var EAIdParamer = "";
        if ($("#EAIdEdit").val() != "")
            EAIdParamer = "&EAId=" + $("#EAIdEdit").val();

        $.get( "addneweaitem.php?name=" + $("#EAName").val()  + "&checklistId=" + $("#checkListId").html() + EAIdParamer, function( data ) {
            $("#EAName").val("");
            $("#EAIdEdit").val("");
            $("#addEAItem").modal("hide");
        });

        setTimeout(() => {  location.reload(); }, 2000);
    });

    //Update Endabnahme date button (on modal)
    $("#btneditEADate").click(function(){

        $.get( "update_endabnahme_date.php?eadate=" + $("#TerminEADate").val()  + "&examId=" + $('#exam_id').val(), function( data ) {
            /*$("#EAName").val("");
            $("#EAIdEdit").val("");*/
            $("#editEADate").modal("hide");
        });

        setTimeout(() => {  location.reload(); }, 2000);
    });

    $("#btnExtraEmail").click(function(){
        sendMail($('#emailTypeValue').val());
        $("#extraEmailModal").modal("hide");
    });

});

function sendMail(mailType) {
    var exams = JSON.parse($("#examList").html());

    var contactPersonMail = "";

    for (let i in exams) {
        if (exams[i].id == $('#exam_id').val()) {
            //console.log(exams[i]);
            // examineremail      -> Dozent
            // contactpersonemail -> Ansprechpartner
            //contactPersonMail = exams[i].examineremail;
            contactPersonMail = exams[i].contactpersonemail;
        }
    }
    //console.log("Stop"); return false;

    if (mailType == "checkliste") {
        console.log("Sending checkliste");
        $.get( "sende_checkliste_pdf.php?&contactPersonMail=" + contactPersonMail + "&examid=" + $('#exam_id').val() + "&extraEmail=" + $('#extraEmail').val(), function( data ) {
            alert(data);
        });
    } else {
        console.log("Sending reminder");
        $.get("send_mail.php?mailType=" + mailType + "&contactPersonMail=" + contactPersonMail + "&examid=" + $('#exam_id').val() + "&extraEmail=" + $('#extraEmail').val(), function (data) {
            alert(data);
        });
    }
}

function exportPDF_ea(myExamId, comments) {
    console.log("EXPORTING EA..33xxxx...");

}

function toggleTopic(id, examid) {
    $.get( "updatetopiccheck.php?topicId=" + id + "&checked=" + $("#topicCheck" + id).is(':checked') + "&examId=" + examid, function( data ) {
        console.log(data);
    });
}

function toggleCheck(type, id, examId) {
    $.get( "updatetopiccheck.php?topicId=" + id + "&checked=" + $("#itemCheck" + type.toUpperCase() + id).is(':checked') + "&examId=" + examId + "&type=" + type, function( data ) {
        console.log(data);
    });
}

function toggleSubChecks(type, id, examId, isChecked) {
    $("input[name='" + id + "']" ).prop('checked', isChecked);
    $("input[name='" + id + "']" ).each(function( index ) {
            console.log( index + ": " + $( this ).text() );
            $.get( "updatetopiccheck.php?topicId=" + $(this).val() + "&checked=" + isChecked + "&examId=" + examId + "&type=" + type, function( data ) {
                console.log(data);
            });
        });
}


function loadLeftPanelData(examId) {
     var exams = JSON.parse($("#examList").html());

    //Load left panel data
    for (let i in exams) {
        if (exams[i].id == examId) {

            var val_examiner = exams[i].examiner;
            if(exams[i].examinercount > 0) {
                val_examiner = exams[i].examinernames.join(', ');
            }
            //console.log('HIER');
            //alert(val_examiner);

            $("#exam_name").val(exams[i].examname);
            $("#exam_id").val(exams[i].id);
            //$("#dozent").val(exams[i].examiner);
            $("#dozent").val(val_examiner);
            $("#fachbereich").val(exams[i].departmentname);
            $("#ansprechpartner_fachgebiet").val(exams[i].contactperson);
            $("#scl_verantwortlicher").val(exams[i].responsibleperson);
            $("#erwartete_anzahl_pruflinge").val(exams[i].numberstudents);

            // ALT
            //var examDate = new Date(exams[i].examtimestart * 1000);
            //$("#datum").val(examDate.toLocaleString("de-DE"));
            // NEU
            $("#datum").val(exams[i].examtimestartinformat);

            $("#datumraw").val(exams[i].examtimestart * 1000);
        }
    }
}

/**
 * Tab: Termincheckliste
 */
function changeExamDateDropdown(examId, courseid) {
    var exams = JSON.parse($("#examList").html());

    //Load left panel data
    for (let i in exams) {
        if (exams[i].id == examId ) {

            var val_examiner = exams[i].examiner;
            if(exams[i].examinercount > 0) {
                val_examiner = exams[i].examinernames.join(', ');
            }
            //console.log('HIER');
            //alert(val_examiner);

            $("#exam_name").val(exams[i].examname);
            $("#exam_id").val(exams[i].id);
            //$("#dozent").val(exams[i].examiner);
            $("#dozent").val(val_examiner);
            $("#fachbereich").val(exams[i].departmentname);
            $("#ansprechpartner_fachgebiet").val(exams[i].contactperson);
            $("#scl_verantwortlicher").val(exams[i].responsibleperson);
            $("#erwartete_anzahl_pruflinge").val(exams[i].numberstudents);

            // ALT
            //var examDate = new Date(exams[i].examtimestart*1000);
            //$("#datum").val(examDate.toLocaleString("de-DE"));
            // NEU
            $("#datum").val(exams[i].examtimestartinformat);

            $("#datumraw").val(exams[i].examtimestart*1000);

            //Load exam topic list
            $.get( "getexamtopics.php?checklist=" + $("#checkListId").html() + "&examId=" + examId + "&examStart=" + exams[i].examtimestart + "&courseid=" + courseid, function( data ) {
                $("#topicList").html(data);
            });
        }
    }

    if (examId == -1) { //Show empty table if an examId is not provided and clear fields
        $.get( "getexamtopics.php?checklist=" + $("#checkListId").html() + "&examId=" + examId + "&examStart=" + $("#datumraw").val() + "&courseid=" + courseid, function( data ) {
            $("#topicList").html(data);
        });

        $("#exam_name").val("");
        $("#dozent").val("");
        $("#fachbereich").val("");
        $("#ansprechpartner_fachgebiet").val("");
        $("#scl_verantwortlicher").val("");
        $("#erwartete_anzahl_pruflinge").val("");
        $("#datum").val("");
        $("#datumraw").val("");
    }
}


function prepareEditTopic(id, tname, tdays, textmail) {
    $("#topicDays").val(tdays);
    $("#topicName").val(tname);
    $("#topicIdEdit").val(id);
    $("#topictextmail").val(textmail);
    $("#addTopic").modal("show");
}

function prepareEditQMItem(id, tname) {
    $("#QMName").val(tname);
    $("#QMIdEdit").val(id);
    $("#addQMItem").modal("show");
}

function prepareEditEAItem(id, tname) {
    $("#EAName").val(tname);
    $("#EAIdEdit").val(id);
    $("#addEAItem").modal("show");
}

function prepareEditEADate(topicId, examId, date) {
    $("#TerminEADate").val(date);
    $("#editEADate").modal("show");
}

function showExtraEmailModal() {
    $("#extraEmail").val("");
    $("#extraEmailModal").modal("show");
}