<?php
  session_start();

  if(!isset($_SESSION['UserName']) || !isset($_SESSION['CliId'])){
    header("location:/gesman");
    exit();
  }
  require_once $_SERVER['DOCUMENT_ROOT']."/gesman/connection/ConnGesmanDb.php";
  require_once $_SERVER['DOCUMENT_ROOT']."/informes/datos/InformesData.php";

  $ID = empty($_GET['id'])?0:$_GET['id'];
  $CLI_ID = $_SESSION['CliId'];
  $isAuthorized = false;
  $errorMessage = '';
  $Estado=0;
  $Nombre='';
  $supervisores = array();
  $contactos = array();
  $claseHabilitado = "btn-outline-secondary";
  $atributoHabilitado = " disabled";

  try {
    $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (is_numeric($ID) && $ID > 0) {
      $informe = FnBuscarInforme($conmy, $ID, $CLI_ID);
      if($informe && $informe->Estado !=3){
        $isAuthorized = true;
        $Nombre = $informe->Nombre;
        $claseHabilitado = "btn-outline-primary";
        $atributoHabilitado = ""; 
        $supervisores = FnBuscarSupervisores($conmy);
        $contactos = FnBuscarContacto($conmy, $CLI_ID);
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
    <title>Editar Informe | GPEM SAC</title>
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
          <p class="m-0 p-0 text-secondary"><?php echo $isAuthorized ? $_SESSION['CliNombre'] : 'UNKNOWN'; ?></p>
          <input type="text" class="d-none" id="idInforme" value="<?php echo $ID;?>" readonly/>
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
        <div class="row g-3">
          <!-- Nro. INFORME -->
          <div class="col-6 col-md-4 col-lg-3">
            <label for="nombreInformeInput" class="form-label mb-0">Nro. Informe</label>
            <input type="text" class="form-control text-secondary text-uppercase fw-bold" id="nombreInformeInput" value="<?php echo ($informe->Nombre); ?>" disabled>
          </div>
          <!-- FECHA -->
          <div class="col-6 col-md-4 col-lg-3">
            <label for="fechaInformeInput" class="form-label mb-0">Fecha</label>
            <input type="date" class="form-control text-secondary text-uppercase fw-bold" id="fechaInformeInput" value="<?php echo ($informe->Fecha); ?>">
          </div>
          <!-- ORDEN DE TRABAJO -->
          <div class="col-6 col-md-4 col-lg-3">
            <label for="OrdenTrabajoInput" class="form-label mb-0">Orden de trabajo</label>
            <input type="text" class="form-control text-secondary text-uppercase fw-bold" id="OrdenTrabajoInput" value="<?php echo ($informe->OrdNombre); ?>" disabled>
          </div>
          <!-- CLIENTE -->
          <div class="col-6 col-md-6 col-lg-3">
            <label for="nombreClienteInput" class="form-label mb-0">Cliente</label>
            <input type="text" class="form-control text-secondary text-uppercase fw-bold" id="nombreClienteInput" value="<?php echo ($informe->CliNombre); ?>" disabled>
          </div>
          <!-- CONTACTOS -->
          <div class="custom-select-container col-md-6 col-lg-4">
            <label for="contactoInput" class="form-label mb-0">Contacto</label>
            <div class="custom-select-wrapper">
              <input type="text" id="contactoInput" class="custom-select-input text-secondary text-uppercase fw-bold" value="<?php echo ($informe->CliContacto); ?>" />
              <span class="custom-select-arrow text-secondary text-uppercase fw-bold"><i class="bi bi-chevron-down"></i></span>
              <div id="contactoList" class="custom-select-list ">
                <?php foreach ($contactos as $contacto): ?>
                  <div class="custom-select-item" data-value="<?php echo ($contacto['idsupervisor']); ?>">
                    <?php echo ($contacto['supervisor']); ?>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>
          </div>
          <!-- LUGAR -->
          <div class="col-md-6 col-lg-4">
            <label for="ubicacionInput" class="form-label mb-0">Lugar</label>
            <input type="text" class="form-control text-secondary text-uppercase fw-bold" id="ubicacionInput" value="<?php echo ($informe->CliDireccion); ?>" >
          </div>      
          <!-- SUPERVISORES -->
          <div class="custom-select-container col-md-6 col-lg-4">
            <label for="supervisorInput" class="form-label mb-0">Supervisor</label>
            <div class="custom-select-wrapper">
              <input type="text" class="custom-select-input text-secondary text-uppercase fw-bold" id="supervisorInput" value="<?php echo  ($supervisorInputValue);?>"/>
              <span class="custom-select-arrow"><i class="bi bi-chevron-down"></i></span>
              <div id="supervisorList" class="custom-select-list">
                <!-- SUPERVISORES -->
                <?php foreach ($supervisores as $supervisor): ?>
                  <div class="custom-select-item" data-value="<?php echo ($supervisor['idsupervisor']); ?>">
                    <?php echo ($supervisor['supervisor']); ?>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>
          </div>
        </div>
        <!-- BOTON GUARDAR -->
        <div class="row mt-4">
          <div class="col-12 col-md-3 mt-2">
            <button id="guardarDataEquipo" class="btn btn-primary pt-2 pb-2 col-12 fw-bold" onclick="fnGuardarDatosGenerales();"><i class="fas fa-save" style="margin-right:10px;"></i>GUARDAR</button>
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
