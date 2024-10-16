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
    }
  } catch (PDOException $e) {
      $errorMessage = $e->getMessage();
      $conmy = null;
  } catch (Exception $e) {
      $errorMessage = $e->getMessage();
      $conmy = null;
  }
?>

<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar Equipo | GPEM S.A.C</title>
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
      /* .contenedor-imagen{display:grid;grid-template-columns:50% 50% !important;gap:5px;}   */
      @media(min-width:768px){.contenedor-imagen{display: grid; grid-template-columns:1fr 1fr !important; gap:10px;}}  
      @media(min-width:992px){.contenedor-imagen{grid-template-columns:1fr 1fr 1fr !important;}}
      .imagen-ajustada {
        width: 100%;
        height: 200px;
        object-fit: contain;
      }
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
          <input type="hidden" id="txtInformeId" value="<?php echo $ID; ?>" readonly/>
          <p class="m-0 p-0 text-center text-secondary"><?php echo $isAuthorized ? $Nombre : 'UNKNOWN'; ?></p>
        </div>
      </div>
      <?php if ($isAuthorized): ?>
        <!-- ENLACES -->
        <div class="row">
          <div class="col-12">
            <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
              <ol class="breadcrumb">
              <li class="breadcrumb-item fw-bold"><a href="/informes/EditarInforme.php?id=<?php echo ($ID) ?>" class="text-decoration-none">INFORME</a></li>
                <li class="breadcrumb-item active fw-bold" aria-current="page">EQUIPO</li>
                <li class="breadcrumb-item fw-bold"><a href="/informes/EditarInformeResumen.php?id=<?php echo ($ID) ?>" class="text-decoration-none">RESUMEN</a></li>
                <li class="breadcrumb-item fw-bold"><a href="/informes/EditarInformeActividad.php?id=<?php echo ($ID) ?>" class="text-decoration-none">ACTIVIDAD</a></li>
                <li class="breadcrumb-item fw-bold"><a href="/informes/EditarInformeAnexo.php?id=<?php echo ($ID) ?>" class="text-decoration-none">ANEXOS</a></li>
              </ol>
            </nav>
          </div>
        </div>
        <!-- BOTON EDITAR -->
        <div class="row mb-3">
          <div class="d-flex justify-content-start align-items-center">
            <button class="btn btn-outline-primary" onclick="FnModalInformeModificarEquipo(<?php echo ($ID); ?>);" style="margin-right: 10px;"><i class="fas fa-edit" style="cursor: pointer; margin-left:10px;"></i> EDITAR</button>
            <button class="btn btn-outline-secondary" onclick="FnModalInformeAgregarArchivo();"><i class="fas fa-paperclip" style="cursor: pointer; margin-left:10px;"></i> ADJUNTAR</buttom>
          </div>
        </div>
        <!--DATOS EQUIPOS-->
        <div class="row border-top mb-3">
          <div class="col-6 col-md-4 mt-2">
            <label class="form-label mb-0">Nombre :</label>
            <p class="mb-0 text-secondary fw-bold" style="font-size:15px" id="txtEquNombre1"><?php echo ($informe->EquNombre); ?></p>
          </div>
          <div class="col-6 col-md-4 mt-2">
            <label class="form-label mb-0">Marca :</label>
            <p class="mb-0 text-secondary fw-bold" style="font-size:15px" id="txtEquMarca1"><?php echo ($informe->EquMarca); ?></p>
          </div>
          <div class="col-6 col-md-4 mt-2">
            <label class="form-label mb-0">Modelo :</label>
            <p class="mb-0 text-secondary fw-bold" style="font-size:15px" id="txtEquModelo1"><?php echo ($informe->EquModelo); ?></p>
          </div>
          <div class="col-6 col-md-4 mt-2">
            <label class="form-label mb-0">Serie :</label>
            <p class="mb-0 text-secondary fw-bold" style="font-size:15px" id="txtEquSerie1"><?php echo ($informe->EquSerie); ?></p>
          </div>
          <div class="col-12 col-md-4 mt-2">
            <label class="form-label mb-0" style="font-size: 15px;">Características :</label>
            <p class="m-0 text-secondary fw-bold" id="txtEquDatos1"><?php echo ($informe->EquDatos); ?></p>
          </div>
          <div class="col-6 col-md-4 mt-2">
            <label class="form-label mb-0">Km :</label>
            <p class="mb-0 text-secondary fw-bold" style="font-size:15px" id="txtEquKm1"><?php echo ($informe->EquKm); ?></p>
          </div>
          <div class="col-6 col-md-4 mt-2">
            <label class="form-label mb-0">Hm :</label>
            <p class="mb-0 text-secondary fw-bold" style="font-size:15px" id="txtEquHm1"><?php echo ($informe->EquHm); ?></p>
          </div>
        </div>
        <!-- ARCHIVOS (TÍTULOS-IMAGENES-DESCRIPCIÓN) -->
        <div class="row border-top contenedor-imagen pt-4">
          <?php if ($isAuthorized): ?>
            <?php foreach($archivos as $archivo): ?>
              <?php if($archivo['tabla']==='INFE'): ?>
                <div class="d-flex flex-column">
                  <span class="text-secondary" onclick="FnEliminarInformeArchivo(<?php echo ($archivo['id']); ?>)" style="font-size:25px; cursor:pointer;">&#x2715</span>
                  <div class="card text-center p-0 mb-4">
                    <div class="card-header text-secondary" style="text-align:justify;padding-left:5px;"><?php echo ($archivo['titulo']); ?></div>
                    <div class="card-body p-0">
                    <img src="/mycloud/gesman/files/<?php echo ($archivo['nombre']); ?>" class="imagen-ajustada" alt="">
                    </div>
                    <div class="card-footer text-secondary" style="text-align:justify;padding-left:5px;"><?php echo($archivo['descripcion']); ?></div>
                  </div>
                </div>
              <?php endif; ?>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      <?php endif; ?>
    </div>

    <!-- M O D A L   D A T O S  D E  E Q U I P O -->
    <div class="modal fade" id="modalEquipo" tabindex="-1" aria-labelledby="equipoModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header bg-primary text-white">
            <h5 class="modal-title fs-5 fw-bold" id="equipoModalLabel">MODIFICAR</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <!-- START MODAL-BODY -->
          <div class="modal-body mb-2" id='modal-body'>
            <div class="row">
              <div class="col-12 col-md-6 mt-2">
                <label for="" class="form-label mb-0">Nombre :</label>
                <input type="text" id="txtEquNombre2" class="form-control text-secondary" value="<?php echo $informe->EquNombre;?>"/>
              </div>
              <div class ="col-12 col-md-6 mt-2">
                <label for="" class="form-label mb-0">Marca :</label>
                <input type="text" id="txtEquMarca2" class="form-control text-secondary" value="<?php echo $informe->EquMarca;?>"/>
              </div>
              <div class="col-12 col-md-6 mt-2">
                <label for="" class="form-label mb-0">Modelo :</label>
                <input type="text" id="txtEquModelo2" class="form-control text-secondary" value="<?php echo $informe->EquModelo;?>"/>
              </div>
              <div class="col-12 col-md-6 mt-2">
                <label for="" class="form-label mb-0">Serie :</label>
                <input type="text" id="txtEquSerie2" class="form-control text-secondary" value="<?php echo $informe->EquSerie;?>"/>
              </div>
              <div class ="col-12 mt-2">
                <label for="" class="form-label mb-0">Características :</label>
                <input type="text" id="txtEquDatos2" class="form-control text-secondary" value="<?php echo $informe->EquDatos;?>"/>
              </div>
              <div class ="col-12 col-md-6 mt-2">
                <label for="" class="form-label mb-0">Km :</label>
                <input type="number" id="txtEquKm2" class="form-control text-secondary" value="<?php echo $informe->EquKm;?>"/>
              </div>
              <div class ="col-12 col-md-6 mt-2">
                <label for="" class="form-label mb-0">Hm :</label>
                <input type="number" id="txtEquHm2" class="form-control text-secondary" value="<?php echo $informe->EquHm;?>"/>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button id="guardarActividad" class="btn btn-primary fw-bold pt-2 pb-2 col-12"onclick="FnModificarInformeEquipo();" ><i class="fas fa-save"></i> GUARDAR</button>
          </div>
        </div>
      </div>
    </div><!-- END MODAL -->

    <!-- M O D A L - I M A G E N E S -->
    <div class="modal fade" id="modalAgregarImagen" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-scrollable ">
        <div class="modal-content">
          <div class="modal-header bg-primary text-white">
            <h5 class="modal-title fs-5 fw-bold" id="modalAgregarImagenLabel">AGREGAR IMAGEN </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body pb-1">
          <input type="hidden" id="cabeceraIdInput">
            <div class="row">
              <div class="col-12 mb-2">
                <label class="form-label mb-0">Título</label>
                <input type="text" class="form-control text-secondary" id="txtTitulo" <?php echo !$isAuthorized ? 'disabled' : ''; ?>>
              </div>
              <div class="col-12 mb-2">
                <label class="form-label mb-0">Descripción</label>
                <input type="text" class="form-control text-secondary" id="txtDescripcion" <?php echo !$isAuthorized ? 'disabled' : ''; ?>>
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
          <div class="modal-footer">
            <button type="button" class="btn btn-primary fw-bold pt-2 pb-2 col-12" onclick="FnAgregarInformeArchivo(); return false;"><i class="fas fa-save"></i>  GUARDAR</button>
          </div>
        </div>
      </div>
    </div><!-- END IMAGENES  -->

    <div class="container-loader-full">
      <div class="loader-full"></div>
    </div>

    <script src="/informes/js/EditarInformeEquipo.js"></script>
    <script src="/mycloud/library/bootstrap-5.0.2-dist/js/bootstrap.min.js"></script>
    <script src="/mycloud/library/SweetAlert2/js/sweetalert2.all.min.js"></script>
    <script src="/gesman/menu/sidebar.js"></script>
  </body>
</html>