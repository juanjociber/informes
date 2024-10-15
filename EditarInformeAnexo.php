<?php
  session_start();
  if(!isset($_SESSION['UserName']) || !isset($_SESSION['CliId'])){
    header("location:/gesman");
    exit();
  }
  $ID = empty($_GET['id'])?0:$_GET['id'];
  $CLI_ID = $_SESSION['CliId'];
  require_once $_SERVER['DOCUMENT_ROOT']."/gesman/connection/ConnGesmanDb.php";
  require_once $_SERVER['DOCUMENT_ROOT']."/informes/datos/InformesData.php";

  $isAuthorized = false;
  $errorMessage = '';
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
        $Nombre = $informe->Nombre;
        $claseHabilitado = "btn-outline-primary";
        $atributoHabilitado = ""; 
        $archivos = FnBuscarArchivos($conmy, $ID);
      } 
    } else {
      throw new Exception('El ID es inválido.');
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
    <title>Editar Anexos | GPEM S.A.C</title>
    <link rel="shortcut icon" href="/mycloud/logos/favicon.ico">
    <link rel="stylesheet" href="/mycloud/library/fontawesome-free-5.9.0-web/css/all.css">
    <link rel="stylesheet" href="/mycloud/library/SweetAlert2/css/sweetalert2.min.css">
    <link rel="stylesheet" href="/mycloud/library/bootstrap-5.0.2-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/mycloud/library/select-gpem-1.0/css/select-gpem-1.0.css">
    <link rel="stylesheet" href="/mycloud/library/gpemsac/css/gpemsac.css"> 
    <link rel="stylesheet" href="/gesman/menu/sidebar.css">
    <style>
      #canvas{
        width:100%
      }
      @media(min-width:992px){.contenedor-imagen{grid-template-columns:1fr 1fr 1fr !important;}}
      @media(min-width:1200px){.contenedor-imagen{grid-template-columns:1fr 1fr 1fr 1fr !important;}}
    </style>
  </head>
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
          <input type="text" class="d-none" id="txtIdInforme" value="<?php echo $ID; ?>" readonly/>
          <p class="m-0 p-0 text-center text-secondary"><?php echo $isAuthorized ? $Nombre : 'UNKNOWN'; ?></p>
        </div>
      </div>

      <?php if ($isAuthorized): ?>
        <div class="row">
          <div class="col-12">
            <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
              <ol class="breadcrumb">                        
                <li class="breadcrumb-item fw-bold"><a href="/informes/EditarInforme.php?id=<?php echo ($ID) ?>" class="text-decoration-none">INFORME</a></li>
                <li class="breadcrumb-item fw-bold"><a href="/informes/EditarInformeEquipo.php?id=<?php echo ($ID) ?>" class="text-decoration-none">EQUIPO</a></li>
                <li class="breadcrumb-item fw-bold"><a href="/informes/EditarInformeResumen.php?id=<?php echo ($ID) ?>" class="text-decoration-none">RESUMEN</a></li>
                <li class="breadcrumb-item fw-bold"><a href="/informes/EditarInformeActividad.php?id=<?php echo ($ID) ?>" class="text-decoration-none">ACTIVIDAD</a></li>
                <li class="breadcrumb-item active fw-bold" aria-current="page">ANEXOS</li>
              </ol>
            </nav>
          </div>
        </div>
        <!-- BOTO AGREGAR -->
        <div class="row mb-1 mt-4 mb-2">
          <div class="col-12 col-md-3">
            <button id="descripcion" class="p-2 btn btn-light border border-1 fw-bold col-12 text-secondary" data-bs-toggle="modal" data-bs-target="#modalAnexo">ARCHIVO <i class="fas fa-paperclip" style="cursor: pointer; margin-left:10px;"></i></button>
          </div>
        </div>

        <div class="contenedor-imagen mt-4" style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
          <?php foreach($archivos as $archivo): ?>
            <?php if ($archivo['tabla'] ==='INFA'): ?>
            <div class="card p-0" id="<?php echo ($archivo['id']); ?>">
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="FnEliminarArchivo(<?php echo ($archivo['id']); ?>)"></button>
                <div class="card-header bg-transparent border-success"><?php echo ($archivo['titulo']); ?></div>
                <img src="/mycloud/gesman/files/<?php echo ($archivo['nombre']); ?>" class="img-fluid" alt="">
                <div class="card-footer bg-transparent border-success"><?php echo ($archivo['descripcion']); ?></div>
            </div>
            <?php endif; ?>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

    <div class="modal fade" id="modalAnexo" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-scrollable ">
        <div class="modal-content">
          <div class="modal-header bg-primary text-white">
            <h5 class="modal-title fs-5 fw-bold" id="modalAgregarImagenLabel">AGREGAR ARCHIVO </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body pb-1">
          <input type="hidden" id="cabeceraIdInput">
            <div class="row">
              <div class="col-12 mb-2">
                <label class="form-label mb-0">Título</label>
                <input type="text" class="form-control text-secondary text-uppercase" id="txtTitulo" <?php echo !$isAuthorized ? 'disabled' : ''; ?>>
              </div>
              <div class="col-12 mb-2">
                <label class="form-label mb-0">Descripción</label>
                <input type="text" class="form-control text-secondary text-uppercase" id="txtDescripcion" <?php echo !$isAuthorized ? 'disabled' : ''; ?>>
              </div>                        
              <div class="col-12">
                <label for="adjuntarImagenInput" class="form-label mb-0">Imagen</label>
                <input id="fileImagen" type="file" accept="image/*,.pdf" class="form-control mb-2" <?php echo !$isAuthorized ? 'disabled' : ''; ?>/>
              </div>
              <div class="col-12 m-0">
                  <div class="col-md-12 text-center" id="divImagen"><i class="fas fa-images fs-2"></i></div>
              </div>
            </div>
          </div>
          <div id="msjAgregarImagen" class="modal-body pt-1"></div>
          <div class="col-12 modal-footer">
            <button type="button" class="btn btn-primary fw-bold pt-2 pb-2 col-12 w-100" onclick="FnAgregarArchivo();" <?php echo !$isAuthorized ? 'disabled' : ''; ?>><i class="fas fa-save"></i>  GUADAR</button>
          </div>
        </div>
      </div>
    </div>
    <div class="container-loader-full">
      <div class="loader-full"></div>
    </div>

    <script src="/informes/js/EditarInformeAnexo.js"></script>
    <script src="/mycloud/library/SweetAlert2/js/sweetalert2.all.min.js"></script>
    <script src="/mycloud/library/bootstrap-5.0.2-dist/js/bootstrap.min.js"></script>
    <script src="/gesman/menu/sidebar.js"></script>
  </body>
</html>
