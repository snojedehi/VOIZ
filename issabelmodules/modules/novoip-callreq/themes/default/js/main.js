
var editModal=function(id){

    $.getJSON("/rest.php/novoip-callreq/Request/edit",{"eid":id}).then(function(res){
        console.log(res)
        $('#editModal').modal('show')
    })
    
}