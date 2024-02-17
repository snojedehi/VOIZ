
var editModal=function(id){

    $.getJSON("/rest.php/novoip-callreq/Request/get",{"eid":id}).then(function(res){
        console.log(res)
        $("#editReq").val(res.id)
        $("#editName").val(res.name)
        $("#editPrefix").val(res.prefix)
        $("#editRepeat").val(res.repeat)
        $("#editTrunk").val(res.trunk)
        $("#editNumbers").val("")
        // $("#editStatus").check(res.status)
        $("#editStatus")[0].checked=res.status=="1"?1:0
        $('#editModal').modal('show')
    })

}