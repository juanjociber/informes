<?php
  session_start();
  require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/SesionData.php";
  
  if(!FnValidarSesion()){
    header("location:/gesman/Salir.php");
    exit();
  }

  if(!FnValidarSesionManNivel2()){
    header("HTTP/1.1 403 Forbidden");
    exit();
  }

  if(empty($_GET['id'])){
    header("HTTP/1.1 404 Not Found");
    exit();
  }
  
  require_once $_SERVER['DOCUMENT_ROOT']."/gesman/connection/ConnGesmanDb.php";
  require_once $_SERVER['DOCUMENT_ROOT']."/informes/data/InformesData.php";
  require_once $_SERVER['DOCUMENT_ROOT']."/informes/data/SupervisoresData.php";
 
  $ID = empty($_GET['id'])?0:$_GET['id'];
  $isAuthorized = false;
  $errorMessage = '';
  $Nombre='';
  $supervisores = array();
  $contactos = array();
  $claseHabilitado = "btn-outline-secondary";
  $atributoHabilitado = " disabled";

  try {
    $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (is_numeric($ID) && $ID > 0) {
      $informe = FnBuscarInforme($conmy, $ID, $_SESSION['gesman']['CliId']);
      if($informe && $informe->Estado !=3){
        $isAuthorized = true;
        $Nombre = $informe->Nombre;
        $claseHabilitado = "btn-outline-primary";
        $atributoHabilitado = ""; 
        $supervisores = FnBuscarSupervisores($conmy);
        $contactos = FnBuscarContacto($conmy, $_SESSION['gesman']['CliId']);
      }
    } 
    $conmy = null;
  } catch (PDOException $ex) {
      $errorMessage = $ex->getMessage();
      $conmy = null;
  } catch (Exception $ex) {
      $errorMessage = $ex->getMessage();
      $conmy = null;
  }

  // VERIFICANDO SI SUPERVISOR PERTENECE AL CLIENTE
  $supervisorValido = false;
  foreach ($supervisores as $supervisor) {
    if ($supervisor['supervisor'] == $informe->Supervisor) {
      $supervisorValido = true;
      break;
    }
  }
  $supervisorInputValue = $supervisorValido ? $informe->Supervisor : '';
?>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar Informe | GPEM S.A.C</title>
    <link rel="shortcut icon" href="/mycloud/logos/favicon.ico">
    <link rel="stylesheet" href="/mycloud/library/fontawesome-free-5.9.0-web/css/all.css">
    <link rel="stylesheet" href="/mycloud/library/SweetAlert2/css/sweetalert2.min.css">
    <link rel="stylesheet" href="/mycloud/library/bootstrap-5.0.2-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/mycloud/library/select-gpem-1.0/css/select-gpem-1.0.css">
    <link rel="stylesheet" href="/mycloud/library/gpemsac/css/gpemsac.css"> 
    <link rel="stylesheet" href="/gesman/menu/sidebar.css">
  </head>
  <style>
    .custom-select-arrow { top: 20%; right: 10px; }
    #guardarInforme:hover svg g {
      stroke: #FFFFFF; 
    }
  </style>
  <body>
    <?php require_once $_SERVER['DOCUMENT_ROOT'].'/gesman/menu/sidebar.php';?>
    <div class="container section-top">
      <div class="row mb-3">
          <div class="col-12 btn-group" role="group" aria-label="Basic example">
            <button type="button" class="btn btn-outline-primary fw-bold" onclick="FnListarInformes(); return false;"><i class="fas fa-list"></i><span class="d-none d-sm-block"> Informes</span></button>
            <button type="button" class="btn btn-outline-primary fw-bold <?php echo $claseHabilitado;?> <?php echo $atributoHabilitado;?>" onclick="FnResumenInforme(); return false;"><i class="fas fa-desktop"></i><span class="d-none d-sm-block"> Resúmen</span></button>
          </div>
      </div>
      <div class="row border-bottom mb-3 fs-5">
        <div class="col-12 fw-bold d-flex justify-content-between">
          <p class="m-0 p-0 text-secondary"><?php echo $isAuthorized ? $_SESSION['gesman']['CliNombre'] : 'UNKNOWN'; ?></p>
          <input type="hidden" id="txtInformeId" value="<?php echo $ID;?>" readonly/>
          <p class="m-0 p-0 text-center text-secondary"><?php echo $isAuthorized ? $Nombre : 'UNKNOWN'; ?></p>
        </div>
      </div>
      <!--DATOS GENERALES-->
      <?php if ($isAuthorized): ?>
        <div class="row">
          <div class="col-12">
            <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
              <ol class="breadcrumb">                        
                <li class="breadcrumb-item active fw-bold" aria-current="page">INFORME</li>
                <li class="breadcrumb-item fw-bold"><a href="/informes/EditarInformeEquipo.php?id=<?php echo ($ID);?>" class="text-decoration-none">EQUIPO</a></li>
                <li class="breadcrumb-item fw-bold"><a href="/informes/EditarInformeResumen.php?id=<?php echo ($ID);?>" class="text-decoration-none">RESUMEN</a></li>
                <li class="breadcrumb-item fw-bold"><a href="/informes/EditarInformeActividad.php?id=<?php echo ($ID);?>" class="text-decoration-none">ACTIVIDAD</a></li>
                <li class="breadcrumb-item fw-bold"><a href="/informes/EditarInformeAnexo.php?id=<?php echo ($ID);?>" class="text-decoration-none">ANEXOS</a></li>
              </ol>
            </nav>
          </div>
        </div>

        <?php
          $html=''; 
          $html.='
          <div class="row g-3">
            <!-- FECHA -->
            <div class="col-6 col-md-4 col-lg-4">
              <label for="dpfecha" class="form-label mb-0">Fecha :</label>
              <input type="date" class="form-control text-secondary fw-bold" id="dpfecha" value="'.$informe->Fecha.'">
            </div>
            <!-- ORDEN DE TRABAJO -->
            <div class="col-6 col-md-4 col-lg-4">
              <label for="txtOrdNombre" class="form-label mb-0">Orden de trabajo :</label>
              <input type="text" class="form-control text-secondary fw-bold" id="txtOrdNombre" value="'.$informe->OrdNombre.'" disabled>
            </div>
            <!-- CONTACTOS -->
            <div class="custom-select-container col-12 col-md-4 col-lg-4">
              <label for="cbCliContacto" class="form-label mb-0">Contacto :</label>
              <div class="custom-select-wrapper">
                <input type="text" id="cbCliContacto" class="custom-select-input text-secondary fw-bold" value="'.$informe->CliContacto.'" />
                <span class="custom-select-arrow text-secondary fw-bold"><i class="bi bi-chevron-down"></i></span>
                <div id="contactoList" class="custom-select-list ">';
                  foreach ($contactos as $contacto){
                    $html.='<div class="custom-select-item" data-value="<'.$contacto['idsupervisor'].'">'.$contacto['supervisor'].'</div>';
                  }
                $html.='</div>
              </div>
            </div>
            
            <!-- SUPERVISORES -->
            <div class="custom-select-container col-12 col-md-6">
              <label for="cbSupervisor" class="form-label mb-0">Supervisor :</label>
              <div class="custom-select-wrapper">
                <input type="text" class="custom-select-input text-secondary fw-bold" id="cbSupervisor" value="'.$supervisorInputValue.'"/>
                <input type="hidden" id="txtPerId" value="" />
                <span class="custom-select-arrow"><i class="bi bi-chevron-down"></i></span>
                <div id="supervisorList" class="custom-select-list">';
                  foreach ($supervisores as $supervisor){
                    $html.='
                      <div class="custom-select-item" data-value="'.$supervisor['idsupervisor'].'">
                        <input type="hidden" class="perid-value" value="'.$supervisor['perid'].'" />
                        '.$supervisor['supervisor'].'
                      </div>';
                  }
                $html.='</div>
              </div>
            </div>

            <!-- DIRECCIÓN -->
            <div class="col-12 col-md-6">
              <label for="txtCliDireccion" class="form-label mb-0">Lugar :</label>
              <input type="text" class="form-control text-secondary fw-bold" id="txtCliDireccion" value="'.$informe->CliDireccion.'" >
            </div>      
          </div>';
          echo $html;
        ?>
        <!-- BOTON GUARDAR -->
        <div class="row mt-4">
          <div class="col-12 mt-2">
            <button id="guardarInforme" class="btn btn-outline-primary pt-2 pb-2 col-12 fw-bold" onclick="FnModificarInforme();">
              <svg id="editarInforme" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" width="28px" height="33px">
                <g data-name="Layer 24" id="Layer_24">
                  <path fill="#0d6efd" d="M26.72,10.79a.49.49,0,0,0-.15-.34L21.32,5.19A.5.5,0,0,0,21,5H6.78a1.5,1.5,0,0,0-1.5,1.5V25.46A1.5,1.5,0,0,0,6.78,27H25.22a1.5,1.5,0,0,0,1.5-1.5V10.8ZM18,6V8.17a.5.5,0,0,0,1,0V6h.92V8.39A1.91,1.91,0,0,1,18,10.3H14a1.91,1.91,0,0,1-1.91-1.91V6ZM10.71,26V21.21a1.5,1.5,0,0,1,1.5-1.5h7.58a1.5,1.5,0,0,1,1.5,1.5V26Zm14.51,0H22.29V21.21a2.5,2.5,0,0,0-2.5-2.5H12.21a2.5,2.5,0,0,0-2.5,2.5V26H6.78a.5.5,0,0,1-.5-.5V6.54a.5.5,0,0,1,.5-.5h4.3V8.39A2.91,2.91,0,0,0,14,11.3h4a2.91,2.91,0,0,0,2.91-2.91V6.19L25.72,11V25.46A.5.5,0,0,1,25.22,26Z"/>
                  <path fill="#0d6efd" d="M18.75,22.33h-5.5a.5.5,0,1,0,0,1h5.5a.5.5,0,0,0,0-1Z"/><path fill="#0d6efd" d="M18.75,24.33h-5.5a.5.5,0,1,0,0,1h5.5a.5.5,0,0,0,0-1Z"/><path fill="#0d6efd" d="M18.75,20.33h-5.5a.5.5,0,1,0,0,1h5.5a.5.5,0,0,0,0-1Z"/>
                </g>
              </svg>
              GUARDAR
            </button>
          </div>
        </div>
      <?php endif ?>
    </div><!-- END CONTAINER -->
    <div class="container-loader-full">
      <div class="loader-full"></div>
    </div>
    <script src="/informes/js/EditarInforme.js"></script>
    <script src="/mycloud/library/bootstrap-5.0.2-dist/js/bootstrap.min.js"></script>
    <script src="/mycloud/library/SweetAlert2/js/sweetalert2.all.min.js"></script>
    <script src="/gesman/menu/sidebar.js"></script>
  </body>
</html>
