<!-- Content Header (Page header) -->
<div class="content-header">

    <div class="container-fluid">

        <div class="row mb-2">

            <div class="col-sm-6">

                <h1>Administrar Módulos y Perfiles</h1>

            </div><!-- /.col -->

            <div class="col-sm-6">

                <ol class="breadcrumb float-sm-right">

                    <li class="breadcrumb-item"><a href="#">Inicio</a>
                    </li>

                    <li class="breadcrumb-item active">Administrar Módulos y Perfiles</li>

                </ol>

            </div><!-- /.col -->

        </div><!-- /.row -->

    </div>

</div>

<div class="content">

    <div class="container-fluid">

        <ul class="nav nav-tabs" id="tabs-asignar-modulos-perfil" role="tablist">

            <li class="nav-item">
                <a class="nav-link active" id="content-modulo-perfil-tab" data-toggle="pill" href="#content-modulo-perfil" role="tab" aria-controls="content-modulo-perfil" aria-selected="false">Asignar Modulo a Perfil</a>
            </li>

        </ul>

        <div class="tab-content" id="tabsContent-asignar-modulos-perfil">

            <div class="tab-pane fade active show mt-4 px-4" id="content-modulo-perfil" role="tabpanel" aria-labelledby="content-modulo-perfil-tab">

                <div class="row">

                    <div class="col-md-8">

                        <div class="card card-gray shadow">

                            <div class="card-header">

                                <h3 class="card-title"><i class="fas fa-list"></i> Listado de Perfiles</h3>

                            </div>

                            <div class="card-body">

                                <table id="tbl_perfiles_asignar" class="display nowrap table-striped w-100 shadow rounded">

                                    <thead class="bg-gray text-left">
                                        <th>id Perfil</th>
                                        <th>Perfil</th>
                                        <th>Estado</th>
                                        <th>F. Creación</th>
                                        <th>F. Actualización</th>
                                        <th class="text-center">Opciones</th>
                                    </thead>

                                    <tbody class="small text left">

                                    </tbody>

                                </table>

                            </div>

                        </div>

                    </div>

                    <div class="col-md-4">

                        <div class="card card-gray shadow" style="display:block" id="card-modulos">

                            <div class="card-header">

                                <h3 class="card-title"><i class="fas fa-laptop"></i> Módulos del Sistema</h3>

                            </div>

                            <div class="card-body" id="card-body-modulos">

                                <div class="row m-2">

                                    <div class="col-md-6">

                                        <button class="btn btn-success btn-small  m-0 p-0 w-100" id="marcar_modulos">Marcar todo</button>

                                    </div>

                                    <div class="col-md-6">

                                        <button class="btn btn-danger btn-small m-0 p-0 w-100" id="desmarcar_modulos">Desmarcar todo</button>

                                    </div>

                                </div>

                                <!-- AQUI SE CARGAN TODOS LOS MODULOS DEL SISTEMA -->
                                <div id="modulos" class="demo"></div>

                                <div class="row m-2">

                                    <div class="col-md-12">

                                        <div class="form-group">

                                            <label>Seleccione el modulo de inicio</label>
                                            <select class="custom-select" id="select_modulos">
                                            </select>

                                        </div>

                                    </div>

                                </div>

                                <div class="row m-2">

                                    <div class="col-md-12">

                                        <button class="btn btn-success btn-small w-50 text-center" id="asignar_modulos">Asignar</button>

                                    </div>

                                </div>

                            </div>

                        </div>
                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<script>
    /* =============================================================
    VARIABLES GLOBALES
    ============================================================= */
    var tbl_perfiles_asignar, modulos_usuario, modulos_sistema;

    $(document).ready(function() {

        //variables para registrar el perfil y los modulos seleccionados
        var idPerfil = 0;
        var selectedElmsIds = [];

        $('#tbl_perfiles_asignar tbody').on('click', '.btnSeleccionarPerfil', function() {

            var data = tbl_perfiles_asignar.row($(this).parents('tr')).data();

            if ($(this).parents('tr').hasClass('selected')) {

                $(this).parents('tr').removeClass('selected');

                $('#modulos').jstree("deselect_all", false);

                $("#select_modulos option").remove();

                idPerfil = 0;

            } else {

                tbl_perfiles_asignar.$('tr.selected').removeClass('selected');

                $(this).parents('tr').addClass('selected');

                idPerfil = data[0];
                
                //alert(idPerfil);

                $.ajax({
                    async: false,
                    url: "ajax/modulo.ajax.php",
                    method: 'POST',
                    data: {
                        accion : 2,
                        id_perfil: idPerfil
                    },
                    dataType: 'json',
                    success: function(respuesta){
                        console.log(respuesta);

                        modulos_usuario = respuesta;

                        seleccionarModulosPerfil(idPerfil);
                    }
                });

            }
        })

        $("#modulos").on("changed.jstree", function(evt, data){
            $("#select_modulos option").remove();
            var selectedElms = $('#modulos').jstree("get_selected", true);
            console.log(selectedElms);
            $.each(selectedElms, function(){
                for(let i = 0; i < modulos_sistema.length; i++){
                    if(modulos_sistema[i]["id"] == this.id && modulos_sistema[i]["vista"]){
                        $('#select_modulos').append($('<option>', {
                            value: this.id,
                            text: this.text
                        }));
                    }
                }
            })
            if($("#select_modulos").has('option').length <= 0){
                $('#select_modulos').append($('<option>', {
                            value: 0,
                            text: "-- No hay modulos Seleccionados --"
                        }));
            }
        })

        $("#marcar_modulos").on('click', function(){
            $('#modulos').jstree('select_all');
        })

        $("#desmarcar_modulos").on('click', function(){
            $('#modulos').jstree('deselect_all', false);
            $("#select_modulos option").remove();
            $('#select_modulos').append($('<option>', {
                            value: 0,
                            text: "-- No hay modulos Seleccionados --"
                        }));
        })

        $("#asignar_modulos").on('click', function(){
            selectedElmsIds = []
            var selectedElms = $('#modulos').jstree("get_selected", true);
            $.each(selectedElms, function(){

                selectedElmsIds.push(this.id);
                if(this.parent != "#"){
                    selectedElmsIds.push(this.parent);
                }

            });

            let modulosSeleccionados = [...new Set(selectedElmsIds)];

            let modulo_inicio = $("#select_modulos").val();

            if (idPerfil != 0 && modulosSeleccionados.length > 0){
                registrarPerfilModulos(modulosSeleccionados, idPerfil, modulo_inicio);
            }else{
                Swal.fire({
                    position: 'center',
                    icon: 'warning',
                    title: 'Debe seleccionar el perfil y modulos a registrar',
                    ShowConfirmButton: false,
                    timer: 3000 
                })
            }
        })


        /* =============================================================
        FUNCIONES PARA LAS CARGAS INICIALES DE DATATABLES, ARBOL DE MODULOS Y REAJUSTE DE CABECERAS DE DATATABLES
        ============================================================= */
        cargarDataTables();
        iniciarArbolModulos();


        function cargarDataTables() {

            tbl_perfiles_asignar = $('#tbl_perfiles_asignar').DataTable({
                ajax: {
                    async: false,
                    url: 'ajax/perfil.ajax.php',
                    type: 'POST',
                    dataType: 'json',
                    dataSrc: "",
                    data: {
                        accion: 1
                    }
                },
                columnDefs: [{
                        targets: 2,
                        sortable: false,
                        createdCell: function(td, cellData, rowData, row, col) {

                            if (parseInt(rowData[2]) == 1) {
                                $(td).html("Activo")
                            } else {
                                $(td).html("Inactivo")
                            }

                        }
                    },
                    {
                        targets: 5,
                        sortable: false,
                        render: function(data, type, full, meta) {
                            return "<center>" +
                                "<span class='btnSeleccionarPerfil text-primary px-1' style='cursor:pointer;' data-bs-toggle='tooltip' data-bs-placement='top' title='Seleccionar perfil'> " +
                                "<i class='fas fa-check fs-5'></i> " +
                                "</span> " +
                                "</center>";
                        }
                    }
                ],
                language: {
                    "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
                }

            });


        }

        function iniciarArbolModulos() {

            $.ajax({
                async: false,
                url: "ajax/modulo.ajax.php",
                method: 'POST',
                data: {
                    accion: 1
                },
                dataType: 'json',
                success: function(respuesta) {

                    modulos_sistema = respuesta;

                    // console.log(respuesta);

                    // inline data demo
                    $('#modulos').jstree({
                        'core': {
                            "check_callback": true,
                            'data': respuesta
                        },
                        "checkbox": {
                            "keep_selected_style": true
                        },
                        "types": {
                            "default": {
                                "icon": "fas fa-laptop text-warning"
                            }
                        },
                        "plugins": ["wholerow", "checkbox", "types", "changed"]

                    }).bind("loaded.jstree", function(event, data) {
                        // you get two params - event & data - check the core docs for a detailed description
                        $(this).jstree("open_all");
                    });

                }
            })
        }

        function seleccionarModulosPerfil(pin_idPerfil){

            $('#modulos').jstree('deselect_all');

            for(let i = 0; i < modulos_sistema.length; i++){
                if(modulos_sistema[i]["id"] == modulos_usuario[i]["id"] && modulos_usuario[i]["sel"] == 1){
                    $("#modulos").jstree("select_node", modulos_sistema[i]["id"]);
                }
            }

            if(pin_idPerfil == 1){
                $("#modulos").jstree(true).hide_node(13);
            }else{
                $("#modulos").jstree(true).show_all();
            }

        }

        function registrarPerfilModulos(modulosSeleccionados, idPerfil, idModulo_inicio){
            $.ajax({
                async: false,
                url: "ajax/perfil_modulo.ajax.php",
                method: 'POST',
                data:{
                    accion: 1,
                    id_modulosSeleccionados: modulosSeleccionados,
                    id_perfil: idPerfil,
                    id_modulo_inicio: idModulo_inicio
                },
                dataType: 'json',
                success: function(respuesta){

                    if(respuesta > 0){
                        Swal.fire({
                            position: 'center',
                            icon: 'success',
                            title: 'Se registro el cambio correctamente',
                            ShowConfirmButton: false,
                            timer: 2000
                        });

                        $("#select_modulos option").remove();

                        $('#modulos').jstree('deselect_all', false);

                        tbl_perfiles_asignar.reload();

                    }else{
                        Swal.fire({
                            position: 'center',
                            icon: 'error',
                            title: 'Error al realizar el Cambio',
                            ShowConfirmButton: false,
                            timer: 2000
                        });
                    }

                }
            });
        }


    }) // FIN DOCUMENT READY
</script>