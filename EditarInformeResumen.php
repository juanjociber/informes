<?php
  session_start();
  if(!isset($_SESSION['UserName']) || !isset($_SESSION['CliId'])){
    header("location:/gesman");
    exit();
  }
  require_once $_SERVER['DOCUMENT_ROOT']."/gesman/connection/ConnGesmanDb.php";
  require_once $_SERVER['DOCUMENT_ROOT']."/informes/datos/InformesData.php";

  $CLI_ID = $_SESSION['CliId'];
  $ID = empty($_GET['id'])?0:$_GET['id'];
  $isAuthorized = false;
  $errorMessage = ''; 
  $datos = array();
  $Estado=0;
  $Nombre='';
  $claseHabilitado = "btn-outline-secondary";
  $atributoHabilitado = " disabled";

  try {
    $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    if (is_numeric($ID) && $ID > 0) {
      $informe = FnBuscarInforme($conmy, $ID, $CLI_ID);
      if ($informe && $informe->Estado !=3) {
        $isAuthorized = true;
        $claseHabilitado = "btn-outline-primary";
        $atributoHabilitado = ""; 
        $Nombre = $informe->Nombre;
        $datos = FnBuscarDetalleInformeActividades($conmy, $ID);
        $conclusiones=array();
        $recomendaciones=array();
        $antecedentes=array();
        $analisis=array();
        foreach($datos as $dato){
          if($dato['tipo']=='con'){
            $conclusiones[]=array('actividad'=>$dato['actividad'],'id'=>$dato['id'],'tipo'=>$dato['tipo']);
          }else if($dato['tipo']=='rec'){
            $recomendaciones[]=array('actividad'=>$dato['actividad'],'id'=>$dato['id'],'tipo'=>$dato['tipo']);
          }else if($dato['tipo']=='ant'){
            $antecedentes[]=array('actividad'=>$dato['actividad'],'id'=>$dato['id'],'tipo'=>$dato['tipo']);
          }else if($dato['tipo']=='ana'){
            $analisis[]=array('actividad'=>$dato['actividad'],'id'=>$dato['id'],'tipo'=>$dato['tipo']);
          }	
        }
      } 
    } 
  } catch (PDOException $ex) {
      $errorMessage = $ex->getMessage();
      $conmy = null;
  } catch (Exception $ex) {
      $errorMessage = $ex->getMessage();
      $conmy = null;
  }
?>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar Resumen | GPEM S.A.C</title>
    <link rel="shortcut icon" href="/mycloud/logos/favicon.ico">
    <link rel="stylesheet" href="/mycloud/library/fontawesome-free-5.9.0-web/css/all.css">
    <link rel="stylesheet" href="/mycloud/library/SweetAlert2/css/sweetalert2.min.css">
    <link rel="stylesheet" href="/mycloud/library/bootstrap-5.0.2-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/mycloud/library/select-gpem-1.0/css/select-gpem-1.0.css">
    <link rel="stylesheet" href="/mycloud/library/gpemsac/css/gpemsac.css"> 
    <link rel="stylesheet" href="/gesman/menu/sidebar.css">
    <style>
      .input-grop-icons{ display: flex; justify-content: flex-end;}
      .bi-plus-lg::before{ font-weight:bold!important; }
      @media(min-width:1400px){.small{ padding-left: 15px; padding-right: 15px;}}
      #btn-antecedentes:hover, #btn-analisis:hover,#btn-conclusiones:hover, #btn-recomendaciones:hover { color:white !important; }      
    </style>
  </head>
  <body>
    <?php require_once $_SERVER['DOCUMENT_ROOT'].'/gesman/menu/sidebar.php';?>
    <div class="container section-top mb-4">
      <div class="row mb-3">
        <div class="col-12 btn-group p-0" role="group" aria-label="Basic example">
          <button type="button" class="btn btn-outline-primary fw-bold" onclick="FnListarInformes(); return false;"><i class="fas fa-list"></i><span class="d-none d-sm-block"> Informes</span></button>
          <button type="button" class="btn btn-outline-primary fw-bold <?php echo $claseHabilitado;?> <?php echo $atributoHabilitado;?>" onclick="FnResumenInforme(); return false;"><i class="fas fa-desktop"></i><span class="d-none d-sm-block"> Resúmen</span></button>
        </div>
      </div>
      <div class="row border-bottom mb-3 fs-5">
        <div class="col-12 fw-bold d-flex justify-content-between p-0">
          <p class="m-0 p-0 text-secondary"><?php echo $isAuthorized ? $_SESSION['CliNombre'] : 'UNKNOWN'; ?></p>
          <input type="text" class="d-none" id="txtIdInforme" value="<?php echo $ID; ?>" readonly/>
          <input type="text" class="d-none" id="txtIdtblDetalleInf">
          <p class="m-0 p-0 text-center text-secondary"><?php echo $isAuthorized ? $Nombre : 'UNKNOWN'; ?></p>
        </div>
      </div>
      <?php if ($isAuthorized): ?>
        <div class="row">
        <div class="col-12 p-0">
          <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
            <ol class="breadcrumb">
              <li class="breadcrumb-item fw-bold"><a href="/informes/EditarInforme.php?id=<?php echo $ID ?>" class="text-decoration-none">INFORME</a></li>
              <li class="breadcrumb-item fw-bold"><a href="/informes/EditarInformeEquipo.php?id=<?php echo $ID ?>" class="text-decoration-none">EQUIPO</a></li>
              <li class="breadcrumb-item active fw-bold" aria-current="page">RESUMEN</li>
              <li class="breadcrumb-item fw-bold"><a href="/informes/EditarInformeActividad.php?id=<?php echo $ID ?>" class="text-decoration-none">ACTIVIDAD</a></li>
              <li class="breadcrumb-item fw-bold"><a href="/informes/EditarInformeAnexo.php?id=<?php echo $ID ?>" class="text-decoration-none">ANEXOS</a></li>
            </ol>
          </nav>
        </div>
        </div>
        <!--RESUMEN-->
        <div class="row border-bottom pb-3" id="containerActividad">
          <label class="text-secondary fw-bold p-0 d-flex justify-content-between">ACTIVIDAD: <small>EDITAR</small></label>
          <!-- ACTIVIDAD -->
          <div class="mt-1 p-2 d-flex justify-content-between align-items-center border border-1 bg-light">
            <p class="mb-0" id="actividadId" style="text-align: justify;"><?php echo $informe->Actividad; ?></p>
            <span class="input-group-text bg-light border border-0 text-secondary" style="cursor:pointer;">
              <svg xmlns="http://www.w3.org/2000/svg" width="23" height="28" viewBox="0 0 59 64" onclick="FnModalModificarInformeActividad(<?php echo $informe->Id; ?>)">
                <title>Editar</title>
                <g fill="none" stroke="#6B6C6E" stroke-width="3">
                  <path d="M47,45v15c0,1.1-0.9,2-2,2H2c-1.1,0-2-0.9-2-2V2c0-1.1,0.9-2,2-2h25.9L47,18.1V33"/>
                  <path d="M47,18.9H30c-1.1,0-2-0.9-2-2V1"/><path d="M9,17h13"/><path d="M9,27h31"/>
                  <path d="M9,34h31"/><path d="M9,43h24"/><path d="M9,49h17"/>
                  <g transform="translate(27, 29)">
                    <path stroke-linejoin="round" d="M0,30l3.9-9.4L24.2,0.3c0.4-0.4,1.1-0.4,1.6,0l3.9,3.9c0.4,0.4,0.4,1.1,0,1.6L9.4,26.1L0,30z"/>
                    <path d="M21.9,2.7l5.4,5.4"/>
                  </g>
                </g>
              </svg>
            </span>
          </div>
        </div>      
        <!-- ITEM ANTECEDENTES -->
        <div class="row mt-3">
          <div class="fw-bold d-flex justify-content-between col-8 col-md-10 p-0"><button class="bg-white text-primary p-2 mb-1" style="border:unset; cursor: pointer; width:190px; text-align:left;" data-bs-toggle="tooltip" data-bs-placement="top" title="Agregar" data-tipo="ant" onclick="FnModalAgregarDetalleInforme('ant')" ><i class="fas fa-plus" style="margin-right: 5px"></i> ANTECEDENTES</button></div>
          <div class="fw-bold text-secondary d-flex justify-content-center align-items-center col-4 col-md-2 p-0"><small>EDITAR</small><small class="small" style="margin-left:5px !important; margin-right:5px; !important;" >|</small><small>ELIMINAR</small></div>
          <?php foreach ($antecedentes as $antecedente) : ?>
            <div class="row d-flex justify-content-between align-items-center border border-bottom m-0 mb-1 pt-1 pb-1" style="padding-left:0px; padding-right:0px;">
              <div class="d-flex col-8 col-md-10";>
                <span class="text-secondary" style="margin-right:10px;">&#x2713</span>
                <p class="mb-0 mb-2 text-secondary" style="margin-bottom: 0 !important; text-align:justify; line-height: 1.2;" data-tipo="<?php echo $antecedente['tipo']; ?>" id="antecedenteId" style="text-align: justify;"><?php echo $antecedente['actividad']; ?></p>
              </div>
              <div class="input-grop-icons col-4 col-md-2 d-flex justify-content-around align-items-center">
                <span class="input-group-text bg-white border border-0 text-secondary" style="cursor:pointer;">              
                  <svg xmlns="http://www.w3.org/2000/svg" width="23" height="28" viewBox="0 0 59 64" data-tipo="<?php echo $antecedente['tipo']; ?>" onclick="FnModalModificarDetalleInforme(<?php echo $antecedente['id']; ?>, 'antecedente')">
                    <title>Editar</title>
                    <g fill="none" stroke="#6B6C6E" stroke-width="3">
                      <path d="M47,45v15c0,1.1-0.9,2-2,2H2c-1.1,0-2-0.9-2-2V2c0-1.1,0.9-2,2-2h25.9L47,18.1V33"/>
                      <path d="M47,18.9H30c-1.1,0-2-0.9-2-2V1"/><path d="M9,17h13"/><path d="M9,27h31"/>
                      <path d="M9,34h31"/><path d="M9,43h24"/><path d="M9,49h17"/>
                      <g transform="translate(27, 29)">
                        <path stroke-linejoin="round" d="M0,30l3.9-9.4L24.2,0.3c0.4-0.4,1.1-0.4,1.6,0l3.9,3.9c0.4,0.4,0.4,1.1,0,1.6L9.4,26.1L0,30z"/>
                        <path d="M21.9,2.7l5.4,5.4"/>
                      </g>
                    </g>
                  </svg>
                </span>
                <span class="input-group-text bg-white border border-0 text-secondary" style="cursor:pointer;">
                  <svg xmlns="http://www.w3.org/2000/svg" width="23" height="28" viewBox="0 0 300 343" onclick="FnModalEliminarDetalleInformeActividad(<?php echo $antecedente['id']; ?>)">
                    <title>Eliminar</title>
                    <g fill="none" stroke="#6B6C6E" stroke-width="7">
                      <path d="M86 2.6c-6.2 2.2-11.7 6.4-15.7 12-4.2 6-5.3 10-5.3 19.5 0 4.9-.4 8-1.3 8.7-.7.6-9.3 1.2-21.2 1.4-20 .3-20 .3-25.3 3.2C10.7 51 6.5 55.6 3.1 63 .6 68.2.5 69.3.5 85.5c0 15.7.2 17.3 2.2 21.2 2.9 5.3 8.1 9.3 14.1 10.8l4.7 1.2.5 95.9c.5 104.8.2 98.4 6.3 108 3.2 5.2 11.4 12.7 16.7 15.4 9.5 4.9 11.5 5 104.6 5 58.8 0 89-.4 93-1.1 16.4-3 29.7-15.2 34-31.3 1.1-4.1 1.4-22.6 1.4-98.9V118h2.3c3.4-.1 10.4-3.6 13.3-6.7 4.7-5.1 5.4-8.3 5.4-25.3 0-20.4-1.1-24.2-9.5-32.6-8.5-8.6-11.3-9.3-35-9.7l-19-.2-.6-10c-.4-8.1-1-11-3.1-15-3.1-6-8.6-11.4-14.8-14.6l-4.5-2.4-61-.2c-53.4-.2-61.6 0-65.5 1.3zM211.6 25.4c3.4 3.4 3.5 3.6 3.2 10.8l-.3 7.3-63.7.2L86 44v-7.8c0-8.1 1.1-10.6 5.5-13 1.4-.8 19.4-1.1 58.8-1.2h56.9l3.4 3.4zM257 210.4c0 101 .2 97.6-5.8 103.9-1.5 1.6-4.5 3.9-6.7 5l-4 2.2H59.4l-4.9-2.6c-3.5-1.8-5.9-3.9-8-7.4l-3-4.8-.3-94.4L43 118h214v92.4z"/>
                      <path d="M67.9 142.9l-2.9 2.9v26.4c-.1 95 .1 121.1 1 122.9 2.3 4.2 6.3 5.1 21.1 4.7 13.6-.3 13.9-.3 16.6-3.1l2.8-2.7.3-73.6.2-73.6-3.4-3.4-3.4-3.4H70.8l-2.9 2.9zM96 220v70.1l-10.2-.3-10.3-.3-.3-69.8L75 150h21v70zm35.9-77.1l-2.9 2.9v73.9c.1 56.5.3 74.2 1.3 75.3 3.5 4.5 5.7 5 20 5h13.9l3.4-3.4 3.4-3.4v-72.6c0-78.9.1-76.8-5.5-79.4-1.6-.8-7.9-1.2-16.6-1.2h-14.1l-2.9 2.9zm28.6 77.1v69.5h-21l-.3-69.8-.2-69.8 10.7.3 10.8.3V220zm37.6-78.6c-5.2 2.9-5 .9-5.1 79v72.8l3.4 3.4 3.4 3.4h28.4l3.4-3.4 3.4-3.4v-73.3c0-68-.2-73.6-1.8-75.5-.9-1.2-2.7-2.7-3.9-3.3-3.2-1.6-28.1-1.4-31.2.3zm26.4 78.6v69.5h-21l-.3-68.5c-.1-37.7 0-69.1.3-69.8.3-.9 3.1-1.2 10.7-1l10.3.3V220z"/>
                    </g>
                  </svg>
              </span>
              </div>
            </div>
          <?php endforeach ?>
        </div>

        <!-- ITEM ANÁLISIS -->
        <div class="row mt-3">
          <div class="fw-bold d-flex justify-content-between col-8 col-md-10 p-0"><button class="bg-white text-primary p-2 mb-1 text-left" style="border:unset; width:190px; text-align:left; cursor: pointer;" data-bs-toggle="tooltip" data-bs-placement="top" title="Agregar" data-tipo="ana" onclick="FnModalAgregarDetalleInforme('ana')"><i class="fas fa-plus" style="margin-right: 5px"></i> ANÁLISIS</button></div>
          <div class="fw-bold text-secondary d-flex justify-content-center align-items-center col-4 col-md-2 p-0"><small>EDITAR</small><small class="small" style="margin-left:5px !important; margin-right:5px; !important;" >|</small><small>ELIMINAR</small></div>
          <?php foreach ($analisis as $analisi) : ?>
            <div class="row d-flex justify-content-between align-items-center border border-bottom m-0 mb-1 pt-1 pb-1" style="padding-left:0px; padding-right:0px;">
              <div class="d-flex col-8 col-md-10";>
                <span class="text-secondary" style="margin-right:10px;">&#x2713</span>
                <p class="mb-0 mb-2 text-secondary" style="margin-bottom: 0 !important; text-align:justify; line-height: 1.2;" data-tipo="<?php echo $analisi['tipo']; ?>" id="analisisId" style="text-align: justify;"><?php echo $analisi['actividad']; ?></p>
              </div>
              <div class="input-grop-icons col-4 col-md-2 d-flex justify-content-around align-items-center">
                <span class="input-group-text bg-white border border-0 text-secondary" style="cursor:pointer;">
                  <svg xmlns="http://www.w3.org/2000/svg" width="23" height="28" viewBox="0 0 59 64" data-tipo="<?php echo $analisi['tipo']; ?>" onclick="FnModalModificarDetalleInforme(<?php echo $analisi['id']; ?>, 'analisis')">
                    <title>Editar</title>
                    <g fill="none" stroke="#6B6C6E" stroke-width="3">
                      <path d="M47,45v15c0,1.1-0.9,2-2,2H2c-1.1,0-2-0.9-2-2V2c0-1.1,0.9-2,2-2h25.9L47,18.1V33"/>
                      <path d="M47,18.9H30c-1.1,0-2-0.9-2-2V1"/><path d="M9,17h13"/><path d="M9,27h31"/>
                      <path d="M9,34h31"/><path d="M9,43h24"/><path d="M9,49h17"/>
                      <g transform="translate(27, 29)">
                        <path stroke-linejoin="round" d="M0,30l3.9-9.4L24.2,0.3c0.4-0.4,1.1-0.4,1.6,0l3.9,3.9c0.4,0.4,0.4,1.1,0,1.6L9.4,26.1L0,30z"/>
                        <path d="M21.9,2.7l5.4,5.4"/>
                      </g>
                    </g>
                  </svg>
                </span>
                <span class="input-group-text bg-white border border-0 text-secondary" style="cursor: pointer;">
                  <svg xmlns="http://www.w3.org/2000/svg" width="23" height="28" viewBox="0 0 300 343" onclick="FnModalEliminarDetalleInformeActividad(<?php echo $analisi['id']; ?>)">
                    <title>Eliminar</title>
                    <g fill="none" stroke="#6B6C6E" stroke-width="7">
                      <path d="M86 2.6c-6.2 2.2-11.7 6.4-15.7 12-4.2 6-5.3 10-5.3 19.5 0 4.9-.4 8-1.3 8.7-.7.6-9.3 1.2-21.2 1.4-20 .3-20 .3-25.3 3.2C10.7 51 6.5 55.6 3.1 63 .6 68.2.5 69.3.5 85.5c0 15.7.2 17.3 2.2 21.2 2.9 5.3 8.1 9.3 14.1 10.8l4.7 1.2.5 95.9c.5 104.8.2 98.4 6.3 108 3.2 5.2 11.4 12.7 16.7 15.4 9.5 4.9 11.5 5 104.6 5 58.8 0 89-.4 93-1.1 16.4-3 29.7-15.2 34-31.3 1.1-4.1 1.4-22.6 1.4-98.9V118h2.3c3.4-.1 10.4-3.6 13.3-6.7 4.7-5.1 5.4-8.3 5.4-25.3 0-20.4-1.1-24.2-9.5-32.6-8.5-8.6-11.3-9.3-35-9.7l-19-.2-.6-10c-.4-8.1-1-11-3.1-15-3.1-6-8.6-11.4-14.8-14.6l-4.5-2.4-61-.2c-53.4-.2-61.6 0-65.5 1.3zM211.6 25.4c3.4 3.4 3.5 3.6 3.2 10.8l-.3 7.3-63.7.2L86 44v-7.8c0-8.1 1.1-10.6 5.5-13 1.4-.8 19.4-1.1 58.8-1.2h56.9l3.4 3.4zM257 210.4c0 101 .2 97.6-5.8 103.9-1.5 1.6-4.5 3.9-6.7 5l-4 2.2H59.4l-4.9-2.6c-3.5-1.8-5.9-3.9-8-7.4l-3-4.8-.3-94.4L43 118h214v92.4z"/>
                      <path d="M67.9 142.9l-2.9 2.9v26.4c-.1 95 .1 121.1 1 122.9 2.3 4.2 6.3 5.1 21.1 4.7 13.6-.3 13.9-.3 16.6-3.1l2.8-2.7.3-73.6.2-73.6-3.4-3.4-3.4-3.4H70.8l-2.9 2.9zM96 220v70.1l-10.2-.3-10.3-.3-.3-69.8L75 150h21v70zm35.9-77.1l-2.9 2.9v73.9c.1 56.5.3 74.2 1.3 75.3 3.5 4.5 5.7 5 20 5h13.9l3.4-3.4 3.4-3.4v-72.6c0-78.9.1-76.8-5.5-79.4-1.6-.8-7.9-1.2-16.6-1.2h-14.1l-2.9 2.9zm28.6 77.1v69.5h-21l-.3-69.8-.2-69.8 10.7.3 10.8.3V220zm37.6-78.6c-5.2 2.9-5 .9-5.1 79v72.8l3.4 3.4 3.4 3.4h28.4l3.4-3.4 3.4-3.4v-73.3c0-68-.2-73.6-1.8-75.5-.9-1.2-2.7-2.7-3.9-3.3-3.2-1.6-28.1-1.4-31.2.3zm26.4 78.6v69.5h-21l-.3-68.5c-.1-37.7 0-69.1.3-69.8.3-.9 3.1-1.2 10.7-1l10.3.3V220z"/>
                    </g>
                  </svg>              
                </span>
              </div>
            </div>
          <?php endforeach ?>
        </div>
        
        <!-- ITEM CONCLUSION -->
        <div class="row mt-3">
          <div class="fw-bold d-flex justify-content-between col-8 col-md-10 p-0"><button class="bg-white text-primary p-2 mb-1" style="border:unset; width:190px; text-align:left; cursor: pointer;" data-bs-toggle="tooltip" data-bs-placement="top" title="Agregar" data-tipo="con" onclick="FnModalAgregarDetalleInforme('con')"><i class="fas fa-plus" style="margin-right: 5px"></i> CONCLUSIONES</button></div>
          <div class="fw-bold text-secondary d-flex justify-content-center align-items-center col-4 col-md-2 p-0"><small>EDITAR</small><small class="small" style="margin-left:5px !important; margin-right:5px; !important;" >|</small><small>ELIMINAR</small></div>
            <?php foreach ($conclusiones as $conclusion) : ?>
            <div class="row d-flex justify-content-between align-items-center border border-bottom m-0 mb-1 pt-1 pb-1" style="padding-left:0px; padding-right:0px;">
              <div class="d-flex col-8 col-md-10">
                <span class="text-secondary" style="margin-right:10px;">&#x2713</span>
                <p class="mb-0 mb-2 text-secondary" style="margin-bottom: 0 !important; text-align:justify; line-height: 1.2;" data-tipo="<?php echo $conclusion['tipo']; ?>" id="conclusionId>" style="text-align: justify;"><?php echo $conclusion['actividad']; ?></p>
              </div>
              <div class="input-grop-icons col-4 col-md-2 d-flex justify-content-around align-items-center">
                <span class="input-group-text bg-white border border-0 text-secondary" style="cursor: pointer;">
                  <svg xmlns="http://www.w3.org/2000/svg" width="23" height="28" viewBox="0 0 59 64" data-tipo="<?php echo $conclusion['tipo']; ?>" onclick="FnModalModificarDetalleInforme(<?php echo $conclusion['id']; ?>, 'conclusion')">
                    <title>Editar</title>
                    <g fill="none" stroke="#6B6C6E" stroke-width="3">
                      <path d="M47,45v15c0,1.1-0.9,2-2,2H2c-1.1,0-2-0.9-2-2V2c0-1.1,0.9-2,2-2h25.9L47,18.1V33"/>
                      <path d="M47,18.9H30c-1.1,0-2-0.9-2-2V1"/><path d="M9,17h13"/><path d="M9,27h31"/>
                      <path d="M9,34h31"/><path d="M9,43h24"/><path d="M9,49h17"/>
                      <g transform="translate(27, 29)">
                        <path stroke-linejoin="round" d="M0,30l3.9-9.4L24.2,0.3c0.4-0.4,1.1-0.4,1.6,0l3.9,3.9c0.4,0.4,0.4,1.1,0,1.6L9.4,26.1L0,30z"/>
                        <path d="M21.9,2.7l5.4,5.4"/>
                      </g>
                    </g>
                  </svg>
                </span>
                <span class="input-group-text bg-white border border-0 text-secondary" style="cursor: pointer;">
                  <svg xmlns="http://www.w3.org/2000/svg" width="23" height="28" viewBox="0 0 300 343" onclick="FnModalEliminarDetalleInformeActividad(<?php echo $conclusion['id']; ?>)">
                    <title>Eliminar</title>
                    <g fill="none" stroke="#6B6C6E" stroke-width="7">
                      <path d="M86 2.6c-6.2 2.2-11.7 6.4-15.7 12-4.2 6-5.3 10-5.3 19.5 0 4.9-.4 8-1.3 8.7-.7.6-9.3 1.2-21.2 1.4-20 .3-20 .3-25.3 3.2C10.7 51 6.5 55.6 3.1 63 .6 68.2.5 69.3.5 85.5c0 15.7.2 17.3 2.2 21.2 2.9 5.3 8.1 9.3 14.1 10.8l4.7 1.2.5 95.9c.5 104.8.2 98.4 6.3 108 3.2 5.2 11.4 12.7 16.7 15.4 9.5 4.9 11.5 5 104.6 5 58.8 0 89-.4 93-1.1 16.4-3 29.7-15.2 34-31.3 1.1-4.1 1.4-22.6 1.4-98.9V118h2.3c3.4-.1 10.4-3.6 13.3-6.7 4.7-5.1 5.4-8.3 5.4-25.3 0-20.4-1.1-24.2-9.5-32.6-8.5-8.6-11.3-9.3-35-9.7l-19-.2-.6-10c-.4-8.1-1-11-3.1-15-3.1-6-8.6-11.4-14.8-14.6l-4.5-2.4-61-.2c-53.4-.2-61.6 0-65.5 1.3zM211.6 25.4c3.4 3.4 3.5 3.6 3.2 10.8l-.3 7.3-63.7.2L86 44v-7.8c0-8.1 1.1-10.6 5.5-13 1.4-.8 19.4-1.1 58.8-1.2h56.9l3.4 3.4zM257 210.4c0 101 .2 97.6-5.8 103.9-1.5 1.6-4.5 3.9-6.7 5l-4 2.2H59.4l-4.9-2.6c-3.5-1.8-5.9-3.9-8-7.4l-3-4.8-.3-94.4L43 118h214v92.4z"/>
                      <path d="M67.9 142.9l-2.9 2.9v26.4c-.1 95 .1 121.1 1 122.9 2.3 4.2 6.3 5.1 21.1 4.7 13.6-.3 13.9-.3 16.6-3.1l2.8-2.7.3-73.6.2-73.6-3.4-3.4-3.4-3.4H70.8l-2.9 2.9zM96 220v70.1l-10.2-.3-10.3-.3-.3-69.8L75 150h21v70zm35.9-77.1l-2.9 2.9v73.9c.1 56.5.3 74.2 1.3 75.3 3.5 4.5 5.7 5 20 5h13.9l3.4-3.4 3.4-3.4v-72.6c0-78.9.1-76.8-5.5-79.4-1.6-.8-7.9-1.2-16.6-1.2h-14.1l-2.9 2.9zm28.6 77.1v69.5h-21l-.3-69.8-.2-69.8 10.7.3 10.8.3V220zm37.6-78.6c-5.2 2.9-5 .9-5.1 79v72.8l3.4 3.4 3.4 3.4h28.4l3.4-3.4 3.4-3.4v-73.3c0-68-.2-73.6-1.8-75.5-.9-1.2-2.7-2.7-3.9-3.3-3.2-1.6-28.1-1.4-31.2.3zm26.4 78.6v69.5h-21l-.3-68.5c-.1-37.7 0-69.1.3-69.8.3-.9 3.1-1.2 10.7-1l10.3.3V220z"/>
                    </g>
                  </svg> 
                </span>
              </div>
            </div>
            <?php endforeach ?>
        </div>
        <!-- ITEM RECOMENDACIÓN -->
        <div class="row mt-3">
          <div class="fw-bold d-flex justify-content-between col-8 col-md-10 p-0"><button class="bg-white text-primary p-2 mb-1" style="border:unset; width:190px; text-align:left; cursor: pointer;" data-bs-toggle="tooltip" data-bs-placement="top" title="Agregar" data-tipo="rec" onclick="FnModalAgregarDetalleInforme('rec')"><i class="fas fa-plus" style="margin-right: 5px"></i> RECOMENDACIONES</button></div>
          <div class="fw-bold text-secondary d-flex justify-content-center align-items-center col-4 col-md-2 p-0"><small>EDITAR</small><small class="small" style="margin-left:5px !important; margin-right:5px; !important;" >|</small><small>ELIMINAR</small></div>
            <?php foreach ($recomendaciones as $recomendacion) : ?>
              <div class="row d-flex justify-content-between align-items-center border border-bottom m-0 mb-1 pt-1 pb-1" style="padding-left:0px; padding-right:0px;">
                <div class="d-flex col-8 col-md-10">
                  <span class="text-secondary" style="margin-right:10px;">&#x2713</span>
                  <p class="mb-0 mb-2 text-secondary" style="margin-bottom: 0 !important; text-align:justify; line-height: 1.2;" data-tipo="<?php echo $recomendacion['tipo']; ?>" id="recomendacionId" style="text-align: justify;"><?php echo $recomendacion['actividad']; ?></p>
                </div>
                <div class="input-grop-icons col-4 col-md-2 d-flex justify-content-around align-items-center">
                  <span class="input-group-text bg-white border border-0 text-secondary" style="cursor: pointer;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="23" height="28" viewBox="0 0 59 64" data-tipo="<?php echo $recomendacion['tipo']; ?>" onclick="FnModalModificarDetalleInforme(<?php echo $recomendacion['id']; ?>, 'recomendacion')">
                      <title>Editar</title>
                      <g fill="none" stroke="#6B6C6E" stroke-width="3">
                        <path d="M47,45v15c0,1.1-0.9,2-2,2H2c-1.1,0-2-0.9-2-2V2c0-1.1,0.9-2,2-2h25.9L47,18.1V33"/>
                        <path d="M47,18.9H30c-1.1,0-2-0.9-2-2V1"/><path d="M9,17h13"/><path d="M9,27h31"/>
                        <path d="M9,34h31"/><path d="M9,43h24"/><path d="M9,49h17"/>
                        <g transform="translate(27, 29)">
                          <path stroke-linejoin="round" d="M0,30l3.9-9.4L24.2,0.3c0.4-0.4,1.1-0.4,1.6,0l3.9,3.9c0.4,0.4,0.4,1.1,0,1.6L9.4,26.1L0,30z"/>
                          <path d="M21.9,2.7l5.4,5.4"/>
                        </g>
                      </g>
                    </svg>
                  </span>
                  <span class="input-group-text bg-white border border-0 text-secondary" style="cursor: pointer;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="23" height="28" viewBox="0 0 300 343" onclick="FnModalEliminarDetalleInformeActividad(<?php echo $recomendacion['id']; ?>)">
                      <title>Eliminar</title>
                      <g fill="none" stroke="#6B6C6E" stroke-width="7">
                        <path d="M86 2.6c-6.2 2.2-11.7 6.4-15.7 12-4.2 6-5.3 10-5.3 19.5 0 4.9-.4 8-1.3 8.7-.7.6-9.3 1.2-21.2 1.4-20 .3-20 .3-25.3 3.2C10.7 51 6.5 55.6 3.1 63 .6 68.2.5 69.3.5 85.5c0 15.7.2 17.3 2.2 21.2 2.9 5.3 8.1 9.3 14.1 10.8l4.7 1.2.5 95.9c.5 104.8.2 98.4 6.3 108 3.2 5.2 11.4 12.7 16.7 15.4 9.5 4.9 11.5 5 104.6 5 58.8 0 89-.4 93-1.1 16.4-3 29.7-15.2 34-31.3 1.1-4.1 1.4-22.6 1.4-98.9V118h2.3c3.4-.1 10.4-3.6 13.3-6.7 4.7-5.1 5.4-8.3 5.4-25.3 0-20.4-1.1-24.2-9.5-32.6-8.5-8.6-11.3-9.3-35-9.7l-19-.2-.6-10c-.4-8.1-1-11-3.1-15-3.1-6-8.6-11.4-14.8-14.6l-4.5-2.4-61-.2c-53.4-.2-61.6 0-65.5 1.3zM211.6 25.4c3.4 3.4 3.5 3.6 3.2 10.8l-.3 7.3-63.7.2L86 44v-7.8c0-8.1 1.1-10.6 5.5-13 1.4-.8 19.4-1.1 58.8-1.2h56.9l3.4 3.4zM257 210.4c0 101 .2 97.6-5.8 103.9-1.5 1.6-4.5 3.9-6.7 5l-4 2.2H59.4l-4.9-2.6c-3.5-1.8-5.9-3.9-8-7.4l-3-4.8-.3-94.4L43 118h214v92.4z"/>
                        <path d="M67.9 142.9l-2.9 2.9v26.4c-.1 95 .1 121.1 1 122.9 2.3 4.2 6.3 5.1 21.1 4.7 13.6-.3 13.9-.3 16.6-3.1l2.8-2.7.3-73.6.2-73.6-3.4-3.4-3.4-3.4H70.8l-2.9 2.9zM96 220v70.1l-10.2-.3-10.3-.3-.3-69.8L75 150h21v70zm35.9-77.1l-2.9 2.9v73.9c.1 56.5.3 74.2 1.3 75.3 3.5 4.5 5.7 5 20 5h13.9l3.4-3.4 3.4-3.4v-72.6c0-78.9.1-76.8-5.5-79.4-1.6-.8-7.9-1.2-16.6-1.2h-14.1l-2.9 2.9zm28.6 77.1v69.5h-21l-.3-69.8-.2-69.8 10.7.3 10.8.3V220zm37.6-78.6c-5.2 2.9-5 .9-5.1 79v72.8l3.4 3.4 3.4 3.4h28.4l3.4-3.4 3.4-3.4v-73.3c0-68-.2-73.6-1.8-75.5-.9-1.2-2.7-2.7-3.9-3.3-3.2-1.6-28.1-1.4-31.2.3zm26.4 78.6v69.5h-21l-.3-68.5c-.1-37.7 0-69.1.3-69.8.3-.9 3.1-1.2 10.7-1l10.3.3V220z"/>
                      </g>
                    </svg>
                  </span>
                </div>
              </div>
            <?php endforeach ?>
        </div>
      <?php endif; ?>
    </div>

    <!-- MODAL EDITAR : ACTIVIDAD -->
    <div class="modal fade" id="modalActividad" tabindex="-1" aria-labelledby="modalGeneralLabel">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header bg-primary text-white">
            <h5 class="modal-title fw-bold" id="modalGeneralLabel">MODIFICAR ACTIVIDAD</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="formGeneral">
              <textarea type="text" class="form-control text-secondary" id="modalActividadInput" name="actividad" rows="3"><?php echo $informe->Actividad; ?></textarea>
              <textarea type="text" class="form-control text-secondary d-none" id="diagnosticoModalInput" name="diagnostico" rows="3"></textarea>
              <textarea type="text" class="form-control text-secondary d-none" id="trabajoModalInput" name="trabajos" rows="3"></textarea>
              <textarea type="text" class="form-control text-secondary d-none" id="observacionModalInput" name="observaciones" rows="3"></textarea>
              <div class="modal-footer">
                <button type="button" class="btn btn-primary fw-bold" id="modalGuardarBtn" onclick="FnModificarInformeActividad()">
                  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" widht="18px" height="23px" x="0px" y="0px" viewBox="0 0 611.923 611.923" xml:space="preserve" style='margin-right:10px'>
                    <g fill="none" stroke="#FFFFFF" stroke-width="20">
                      <path d="M606.157,120.824L489.908,4.575c-2.46-2.46-6.612-4.152-10.764-4.152H434.32H175.988H40.672   C18.222,0.423,0,18.721,0,41.095v529.734c0,22.45,18.298,40.672,40.672,40.672h86.341h368.661h75.577   c22.45,0,40.672-18.299,40.672-40.672V131.665C611.077,128.359,609.463,124.207,606.157,120.824z M419.328,31.177v136.162   c0,0.846-0.846,0.846-0.846,0.846h-42.363V31.177H419.328z M344.596,31.177v137.008H192.595c-0.846,0-0.846-0.846-0.846-0.846   V31.177H344.596z M141.929,580.9V390.688c0-35.674,29.062-64.737,64.737-64.737h208.434c35.674,0,64.737,29.062,64.737,64.737   v190.135H141.929V580.9z M580.401,570.905c0,4.997-4.152,9.995-9.995,9.995h-59.816V390.688c0-52.281-43.209-95.49-95.49-95.49   H207.511c-52.281,0-95.49,43.209-95.49,95.49v190.135H40.595c-4.997,0-9.995-4.152-9.995-9.995V41.095   c0-4.997,4.152-9.995,9.995-9.995h120.401v136.162c0,17.453,14.147,31.523,31.523,31.523h225.886   c17.453,0,31.523-14.147,31.523-31.523V31.177h23.219l107.1,107.1L580.401,570.905L580.401,570.905z M422.634,490.33   c0,8.304-6.612,14.916-14.916,14.916H217.506c-8.304,0-14.916-6.612-14.916-14.916c0-8.303,6.612-14.916,14.916-14.916h189.289   C415.945,475.415,422.634,482.027,422.634,490.33z M422.634,410.678c0,8.303-6.612,14.916-14.916,14.916H217.506   c-8.304,0-14.916-6.612-14.916-14.916s6.612-14.916,14.916-14.916h189.289C415.945,394.84,422.634,401.529,422.634,410.678z"/>
                    </g>
                  </svg> 
                  GUARDAR
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- MODAL REGISTRAR : ANTECEDENTE-CONCLUSION-RECOMENDACIÓN -->
    <div class="modal fade" id="agregarActividadModal" tabindex="-1" aria-labelledby="cabeceraRegistrarModal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header bg-primary text-white">
            <h5 class="modal-title text-uppercase fw-bold" id="cabeceraRegistrarModal"></h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="formGeneral">
              <textarea type="text" class="form-control text-secondary " id="registroActividadInput" name="actividad" rows="3"></textarea>
              <textarea type="text" class="form-control text-secondary d-none" id="registroDiagnosticoInput" name="diagnostico" rows="3"></textarea>
              <textarea type="text" class="form-control text-secondary d-none" id="registroTrabajoInput" name="trabajos" rows="3"></textarea>
              <textarea type="text" class="form-control text-secondary d-none" id="registroObservacionInput" name="observaciones" rows="3"></textarea>
              <div class="modal-footer">
                <button type="button" class="btn btn-primary fw-bold" id="modalGuardarBtn" onclick="FnAgregarDetalleInformeActividad()">
                  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" widht="18px" height="23px" x="0px" y="0px" viewBox="0 0 611.923 611.923" xml:space="preserve" style='margin-right:10px'>
                    <g fill="none" stroke="#FFFFFF" stroke-width="20">
                      <path d="M606.157,120.824L489.908,4.575c-2.46-2.46-6.612-4.152-10.764-4.152H434.32H175.988H40.672   C18.222,0.423,0,18.721,0,41.095v529.734c0,22.45,18.298,40.672,40.672,40.672h86.341h368.661h75.577   c22.45,0,40.672-18.299,40.672-40.672V131.665C611.077,128.359,609.463,124.207,606.157,120.824z M419.328,31.177v136.162   c0,0.846-0.846,0.846-0.846,0.846h-42.363V31.177H419.328z M344.596,31.177v137.008H192.595c-0.846,0-0.846-0.846-0.846-0.846   V31.177H344.596z M141.929,580.9V390.688c0-35.674,29.062-64.737,64.737-64.737h208.434c35.674,0,64.737,29.062,64.737,64.737   v190.135H141.929V580.9z M580.401,570.905c0,4.997-4.152,9.995-9.995,9.995h-59.816V390.688c0-52.281-43.209-95.49-95.49-95.49   H207.511c-52.281,0-95.49,43.209-95.49,95.49v190.135H40.595c-4.997,0-9.995-4.152-9.995-9.995V41.095   c0-4.997,4.152-9.995,9.995-9.995h120.401v136.162c0,17.453,14.147,31.523,31.523,31.523h225.886   c17.453,0,31.523-14.147,31.523-31.523V31.177h23.219l107.1,107.1L580.401,570.905L580.401,570.905z M422.634,490.33   c0,8.304-6.612,14.916-14.916,14.916H217.506c-8.304,0-14.916-6.612-14.916-14.916c0-8.303,6.612-14.916,14.916-14.916h189.289   C415.945,475.415,422.634,482.027,422.634,490.33z M422.634,410.678c0,8.303-6.612,14.916-14.916,14.916H217.506   c-8.304,0-14.916-6.612-14.916-14.916s6.612-14.916,14.916-14.916h189.289C415.945,394.84,422.634,401.529,422.634,410.678z"/>
                    </g>
                  </svg>  
                  GUARDAR
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- MODAL EDITAR : ANTECEDENTE-CONCLUSION-RECOMENDACIÓN- -->
    <div class="modal fade" id="modalGeneral" tabindex="-1" aria-labelledby="cabeceraModal" >
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header bg-primary text-white">
            <h5 class="modal-title text-uppercase fw-bold" id="cabeceraModal"></h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="formGeneral">
              <textarea type="text" class="form-control text-secondary" id="actividadModalInput" name="actividad" rows="3" placeholder=""></textarea>
              <textarea type="text" class="form-control text-secondary d-none" id="diagnosticoModalInput" name="diagnostico" rows="3" placeholder=""></textarea>
              <textarea type="text" class="form-control text-secondary d-none" id="trabajoModalInput" name="trabajos" rows="3" placeholder=""></textarea>
              <textarea type="text" class="form-control text-secondary d-none" id="observacionModalInput" name="observaciones" rows="3" placeholder=""></textarea>
              <div class="modal-footer">
                <button type="button" class="btn btn-primary fw-bold" id="modalGuardarBtn" onclick="FnModificarDetalleInformeActividad()">
                  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" widht="18px" height="23px" x="0px" y="0px" viewBox="0 0 611.923 611.923" xml:space="preserve" style='margin-right:10px'>
                    <g fill="none" stroke="#FFFFFF" stroke-width="20">
                      <path d="M606.157,120.824L489.908,4.575c-2.46-2.46-6.612-4.152-10.764-4.152H434.32H175.988H40.672   C18.222,0.423,0,18.721,0,41.095v529.734c0,22.45,18.298,40.672,40.672,40.672h86.341h368.661h75.577   c22.45,0,40.672-18.299,40.672-40.672V131.665C611.077,128.359,609.463,124.207,606.157,120.824z M419.328,31.177v136.162   c0,0.846-0.846,0.846-0.846,0.846h-42.363V31.177H419.328z M344.596,31.177v137.008H192.595c-0.846,0-0.846-0.846-0.846-0.846   V31.177H344.596z M141.929,580.9V390.688c0-35.674,29.062-64.737,64.737-64.737h208.434c35.674,0,64.737,29.062,64.737,64.737   v190.135H141.929V580.9z M580.401,570.905c0,4.997-4.152,9.995-9.995,9.995h-59.816V390.688c0-52.281-43.209-95.49-95.49-95.49   H207.511c-52.281,0-95.49,43.209-95.49,95.49v190.135H40.595c-4.997,0-9.995-4.152-9.995-9.995V41.095   c0-4.997,4.152-9.995,9.995-9.995h120.401v136.162c0,17.453,14.147,31.523,31.523,31.523h225.886   c17.453,0,31.523-14.147,31.523-31.523V31.177h23.219l107.1,107.1L580.401,570.905L580.401,570.905z M422.634,490.33   c0,8.304-6.612,14.916-14.916,14.916H217.506c-8.304,0-14.916-6.612-14.916-14.916c0-8.303,6.612-14.916,14.916-14.916h189.289   C415.945,475.415,422.634,482.027,422.634,490.33z M422.634,410.678c0,8.303-6.612,14.916-14.916,14.916H217.506   c-8.304,0-14.916-6.612-14.916-14.916s6.612-14.916,14.916-14.916h189.289C415.945,394.84,422.634,401.529,422.634,410.678z"/>
                    </g>
                  </svg>  
                  GUARDAR
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <div class="container-loader-full">
      <div class="loader-full"></div>
    </div>
    
    <script src="/informes/js/EditarInformeResumen.js"></script>
    <script src="/mycloud/library/bootstrap-5.0.2-dist/js/bootstrap.min.js"></script>
    <script src="/mycloud/library/SweetAlert2/js/sweetalert2.all.min.js"></script>
    <script src="/gesman/menu/sidebar.js"></script>
  </body>
</html>
