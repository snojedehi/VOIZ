
var editModal=function(id){

    $.getJSON("/rest.php/novoip-callreq/Request/get",{"eid":id}).then(function(res){
        console.log(res)
        $("#editReq").val(res.id)
        $("#editName").val(res.name)
        $("#editPrefix").val(res.prefix)
        $("#editRepeat").val(res.repeat)
        $("#editSoundRepeat").val(res.soundRepeat)
        $("#editTrunk").val(res.trunk)
        $("#editHook").val(res.hook)
        $("#editCallerID").val(res.callerID)
        $("#editReqNum").val(res.reqNum)
        $("#editNumbers").val("")
        for(l in res.destination){
            $("#inp").val(res.destination[l]["ac"])
            $("#des").val(res.destination[l]["des"])
            
        }
        // $("#editStatus").check(res.status)
        $("#editStatus")[0].checked=res.status=="1"?1:0
        $('#editModal').modal('show')
    })
}
var deleteItem=function(id){
    var cr=confirm("آیا این آیتم حذف شود؟")
    if(cr){
        document.location.href="/index.php?menu=novoip-callreq&del="+id
    }
}