
var editModal=function(id){

    $.getJSON("/rest.php/novoip-callreq/Request/get",{"eid":id}).then(function(res){
        console.log(res)
        $("#editReq").val(res.id)
        $("#editName").val(res.name)
        $("#editPrefix").val(res.prefix)
        $("#editRepeat").val(res.repeat)
        $("#editTrunk").val(res.trunk)
        $("#editStatus").check(res.status)
        $('#editModal').modal('show')
    })

}