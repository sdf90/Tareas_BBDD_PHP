function cerrar_Session(){

    console.log("Cerrar session");
   
   $.ajax({
    url:'tareas.php',
    type:'POST',
    dataType:'html',
    data:{condicion:'cerrarSession'},
    success:function(resultado){
        
    }
   });

  
 }