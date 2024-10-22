<?php
  session_start();
  if(!isset($_SESSION['UserName']) || !isset($_SESSION['CliId'])){
    header("location:/gesman");
    exit();
  }
  require_once $_SERVER['DOCUMENT_ROOT']."/gesman/connection/ConnGesmanDb.php";
  require_once $_SERVER['DOCUMENT_ROOT']."/informes/datos/InformesData.php";
  require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/ArchivosData.php";

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
        $archivos = FnBuscarArchivos2($conmy, $ID);
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
      @media(min-width:768px){.contenedor-imagen{display: grid; grid-template-columns:1fr 1fr !important; gap:10px;}}  
      /* @media(min-width:992px){.contenedor-imagen{grid-template-columns:1fr 1fr 1fr !important;}} */
      .imagen-ajustada {
        width: 100%;
        height: 200px;
        object-fit: contain;
      }
      #editarInformeEquipo:hover svg g {
        stroke: #FFFFFF; 
      }
      #adjuntarInformeEquipoArchivo:hover svg #Archivo {
        stroke: #FFFFFF; 
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
          <?php
          $html=''; 
          $html.='<div class="d-flex justify-content-start align-items-center">
            <button id="editarInformeEquipo" class="btn btn-outline-primary" onclick="FnModalModificarInformeEquipo('.$ID.');" style="margin-right: 10px;">
              <svg id="editarDatoEquipo" xmlns="http://www.w3.org/2000/svg" width="23" height="28" viewBox="0 0 59 64">
                <title>Editar</title>
                <g fill="none" stroke="#0d6efd" stroke-width="3">
                  <path d="M47,45v15c0,1.1-0.9,2-2,2H2c-1.1,0-2-0.9-2-2V2c0-1.1,0.9-2,2-2h25.9L47,18.1V33"/><path d="M47,18.9H30c-1.1,0-2-0.9-2-2V1"/><path d="M9,17h13"/><path d="M9,27h31"/><path d="M9,34h31"/><path d="M9,43h24"/><path d="M9,49h17"/>
                  <g transform="translate(27, 29)">
                    <path stroke-linejoin="round" d="M0,30l3.9-9.4L24.2,0.3c0.4-0.4,1.1-0.4,1.6,0l3.9,3.9c0.4,0.4,0.4,1.1,0,1.6L9.4,26.1L0,30z"/><path d="M21.9,2.7l5.4,5.4"/>
                  </g>
                </g>
              </svg>
              EDITAR
            </button>
            <button id="adjuntarInformeEquipoArchivo" class="btn btn-outline-secondary" onclick="FnModalInformeAgregarArchivo();">
              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="23px" height="28px" viewBox="0 0 59 63" version="1.1" id="guardarId2">
                <title>Archivo</title>
                <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                  <path d="M36.3,46.5 L12.1,22.4 C9.4,19.7 9.1,15.6 11.8,12.9 L11.8,12.9 C14.5,10.2 19.1,10.1 21.8,12.8 L53.6,44.4 C57.8,48.6 58.2,54.9 54.5,58.5 L54.5,58.5 C50.8,62.1 44.5,61.7 40.3,57.6 L4.7,22.1 C-0.1,17.4 -0.2,9.8 4.3,5.3 L5.3,4.3 C9.8,-0.2 17.4,-0.1 22.2,4.7 L46,28.4" id="Archivo" stroke="#6c757d" stroke-width="3"/>
                </g>
              </svg>
              ADJUNTAR
            </button>
          </div>';
          echo $html;
          ?>
        </div>
        <!--DATOS EQUIPOS-->
        <?php
          $html='';
          $html.=' 
            <div class="row border-top mb-3">
              <div class="col-6 col-md-4 mt-2">
                <label class="form-label mb-0">Nombre :</label>
                <p class="mb-0 text-secondary fw-bold" style="font-size:15px" id="txtEquNombre1">'.$informe->EquNombre.'</p>
              </div>
              <div class="col-6 col-md-4 mt-2">
                <label class="form-label mb-0">Marca :</label>
                <p class="mb-0 text-secondary fw-bold" style="font-size:15px" id="txtEquMarca1">'.$informe->EquMarca.'</p>
              </div>
              <div class="col-6 col-md-4 mt-2">
                <label class="form-label mb-0">Modelo :</label>
                <p class="mb-0 text-secondary fw-bold" style="font-size:15px" id="txtEquModelo1">'.$informe->EquModelo.'</p>
              </div>
              <div class="col-6 col-md-4 mt-2">
                <label class="form-label mb-0">Serie :</label>
                <p class="mb-0 text-secondary fw-bold" style="font-size:15px" id="txtEquSerie1">'.$informe->EquSerie.'</p>
              </div>
              <div class="col-12 col-md-4 mt-2">
                <label class="form-label mb-0" style="font-size: 15px;">Características :</label>
                <p class="m-0 text-secondary fw-bold" id="txtEquDatos1">'.$informe->EquDatos.'</p>
              </div>
              <div class="col-6 col-md-4 mt-2">
                <label class="form-label mb-0">Km :</label>
                <p class="mb-0 text-secondary fw-bold" style="font-size:15px" id="txtEquKm1">'.$informe->EquKm.'</p>
              </div>
              <div class="col-6 col-md-4 mt-2">
                <label class="form-label mb-0">Hm :</label>
                <p class="mb-0 text-secondary fw-bold" style="font-size:15px" id="txtEquHm1">'.$informe->EquHm.'</p>
              </div>
            </div>';
          echo $html;
        ?>
        <!-- ARCHIVOS (TÍTULOS-IMAGENES-DESCRIPCIÓN) -->
        <?php
          $html=''; 
          $html.='
          <div class="row border-top contenedor-imagen pt-4">';
            if ($isAuthorized){
              foreach($archivos as $archivo){
                if($archivo['tabla']==='INFE'){
                  $html.='
                  <div class="d-flex flex-column">
                    <span class="text-secondary" style="padding: 5px; cursor:pointer;">
                      <svg xmlns="http://www.w3.org/2000/svg" width="23" height="28" viewBox="0 0 300 343" onclick="FnEliminarInformeArchivo('.$archivo['id'].')">
                        <title>Eliminar</title>
                        <g fill="none" stroke="#6B6C6E" stroke-width="7">
                          <path d="M86 2.6c-6.2 2.2-11.7 6.4-15.7 12-4.2 6-5.3 10-5.3 19.5 0 4.9-.4 8-1.3 8.7-.7.6-9.3 1.2-21.2 1.4-20 .3-20 .3-25.3 3.2C10.7 51 6.5 55.6 3.1 63 .6 68.2.5 69.3.5 85.5c0 15.7.2 17.3 2.2 21.2 2.9 5.3 8.1 9.3 14.1 10.8l4.7 1.2.5 95.9c.5 104.8.2 98.4 6.3 108 3.2 5.2 11.4 12.7 16.7 15.4 9.5 4.9 11.5 5 104.6 5 58.8 0 89-.4 93-1.1 16.4-3 29.7-15.2 34-31.3 1.1-4.1 1.4-22.6 1.4-98.9V118h2.3c3.4-.1 10.4-3.6 13.3-6.7 4.7-5.1 5.4-8.3 5.4-25.3 0-20.4-1.1-24.2-9.5-32.6-8.5-8.6-11.3-9.3-35-9.7l-19-.2-.6-10c-.4-8.1-1-11-3.1-15-3.1-6-8.6-11.4-14.8-14.6l-4.5-2.4-61-.2c-53.4-.2-61.6 0-65.5 1.3zM211.6 25.4c3.4 3.4 3.5 3.6 3.2 10.8l-.3 7.3-63.7.2L86 44v-7.8c0-8.1 1.1-10.6 5.5-13 1.4-.8 19.4-1.1 58.8-1.2h56.9l3.4 3.4zM257 210.4c0 101 .2 97.6-5.8 103.9-1.5 1.6-4.5 3.9-6.7 5l-4 2.2H59.4l-4.9-2.6c-3.5-1.8-5.9-3.9-8-7.4l-3-4.8-.3-94.4L43 118h214v92.4z"/>
                          <path d="M67.9 142.9l-2.9 2.9v26.4c-.1 95 .1 121.1 1 122.9 2.3 4.2 6.3 5.1 21.1 4.7 13.6-.3 13.9-.3 16.6-3.1l2.8-2.7.3-73.6.2-73.6-3.4-3.4-3.4-3.4H70.8l-2.9 2.9zM96 220v70.1l-10.2-.3-10.3-.3-.3-69.8L75 150h21v70zm35.9-77.1l-2.9 2.9v73.9c.1 56.5.3 74.2 1.3 75.3 3.5 4.5 5.7 5 20 5h13.9l3.4-3.4 3.4-3.4v-72.6c0-78.9.1-76.8-5.5-79.4-1.6-.8-7.9-1.2-16.6-1.2h-14.1l-2.9 2.9zm28.6 77.1v69.5h-21l-.3-69.8-.2-69.8 10.7.3 10.8.3V220zm37.6-78.6c-5.2 2.9-5 .9-5.1 79v72.8l3.4 3.4 3.4 3.4h28.4l3.4-3.4 3.4-3.4v-73.3c0-68-.2-73.6-1.8-75.5-.9-1.2-2.7-2.7-3.9-3.3-3.2-1.6-28.1-1.4-31.2.3zm26.4 78.6v69.5h-21l-.3-68.5c-.1-37.7 0-69.1.3-69.8.3-.9 3.1-1.2 10.7-1l10.3.3V220z"/>
                        </g>
                      </svg>
                    </span>
                    <div class="card text-center p-0 mb-2">
                      <div class="card-header text-secondary" style="text-align:justify;padding-left:5px;">'.$archivo['titulo'].'</div>
                      <div class="card-body p-0">
                        <img src="/mycloud/gesman/files/'.$archivo['nombre'].'" class="imagen-ajustada" alt="">
                      </div>
                      <div class="card-footer text-secondary" style="text-align:justify;padding-left:5px;">'.$archivo['descripcion'].'</div>
                    </div>
                  </div>';
                }
              }
            }
          $html.='
          </div>';
          echo $html;
        ?>
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
            <button id="guardarActividad" class="btn btn-primary fw-bold pt-2 pb-2 col-12"onclick="FnModificarInformeEquipo();" >            
              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" widht="18px" height="23px" x="0px" y="0px" viewBox="0 0 611.923 611.923" xml:space="preserve" style='margin-right:10px'>
                <g fill="none" stroke="#FFFFFF" stroke-width="20">
                  <path d="M606.157,120.824L489.908,4.575c-2.46-2.46-6.612-4.152-10.764-4.152H434.32H175.988H40.672   C18.222,0.423,0,18.721,0,41.095v529.734c0,22.45,18.298,40.672,40.672,40.672h86.341h368.661h75.577   c22.45,0,40.672-18.299,40.672-40.672V131.665C611.077,128.359,609.463,124.207,606.157,120.824z M419.328,31.177v136.162   c0,0.846-0.846,0.846-0.846,0.846h-42.363V31.177H419.328z M344.596,31.177v137.008H192.595c-0.846,0-0.846-0.846-0.846-0.846   V31.177H344.596z M141.929,580.9V390.688c0-35.674,29.062-64.737,64.737-64.737h208.434c35.674,0,64.737,29.062,64.737,64.737   v190.135H141.929V580.9z M580.401,570.905c0,4.997-4.152,9.995-9.995,9.995h-59.816V390.688c0-52.281-43.209-95.49-95.49-95.49   H207.511c-52.281,0-95.49,43.209-95.49,95.49v190.135H40.595c-4.997,0-9.995-4.152-9.995-9.995V41.095   c0-4.997,4.152-9.995,9.995-9.995h120.401v136.162c0,17.453,14.147,31.523,31.523,31.523h225.886   c17.453,0,31.523-14.147,31.523-31.523V31.177h23.219l107.1,107.1L580.401,570.905L580.401,570.905z M422.634,490.33   c0,8.304-6.612,14.916-14.916,14.916H217.506c-8.304,0-14.916-6.612-14.916-14.916c0-8.303,6.612-14.916,14.916-14.916h189.289   C415.945,475.415,422.634,482.027,422.634,490.33z M422.634,410.678c0,8.303-6.612,14.916-14.916,14.916H217.506   c-8.304,0-14.916-6.612-14.916-14.916s6.612-14.916,14.916-14.916h189.289C415.945,394.84,422.634,401.529,422.634,410.678z"/>
                </g>
              </svg>
              GUARDAR
            </button>
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
            <button type="button" class="btn btn-primary fw-bold pt-2 pb-2 col-12" onclick="FnAgregarInformeArchivo(); return false;">
              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" widht="18px" height="23px" x="0px" y="0px" viewBox="0 0 611.923 611.923" xml:space="preserve" style='margin-right:10px'>
                <g fill="none" stroke="#FFFFFF" stroke-width="20">
                  <path d="M606.157,120.824L489.908,4.575c-2.46-2.46-6.612-4.152-10.764-4.152H434.32H175.988H40.672   C18.222,0.423,0,18.721,0,41.095v529.734c0,22.45,18.298,40.672,40.672,40.672h86.341h368.661h75.577   c22.45,0,40.672-18.299,40.672-40.672V131.665C611.077,128.359,609.463,124.207,606.157,120.824z M419.328,31.177v136.162   c0,0.846-0.846,0.846-0.846,0.846h-42.363V31.177H419.328z M344.596,31.177v137.008H192.595c-0.846,0-0.846-0.846-0.846-0.846   V31.177H344.596z M141.929,580.9V390.688c0-35.674,29.062-64.737,64.737-64.737h208.434c35.674,0,64.737,29.062,64.737,64.737   v190.135H141.929V580.9z M580.401,570.905c0,4.997-4.152,9.995-9.995,9.995h-59.816V390.688c0-52.281-43.209-95.49-95.49-95.49   H207.511c-52.281,0-95.49,43.209-95.49,95.49v190.135H40.595c-4.997,0-9.995-4.152-9.995-9.995V41.095   c0-4.997,4.152-9.995,9.995-9.995h120.401v136.162c0,17.453,14.147,31.523,31.523,31.523h225.886   c17.453,0,31.523-14.147,31.523-31.523V31.177h23.219l107.1,107.1L580.401,570.905L580.401,570.905z M422.634,490.33   c0,8.304-6.612,14.916-14.916,14.916H217.506c-8.304,0-14.916-6.612-14.916-14.916c0-8.303,6.612-14.916,14.916-14.916h189.289   C415.945,475.415,422.634,482.027,422.634,490.33z M422.634,410.678c0,8.303-6.612,14.916-14.916,14.916H217.506   c-8.304,0-14.916-6.612-14.916-14.916s6.612-14.916,14.916-14.916h189.289C415.945,394.84,422.634,401.529,422.634,410.678z"/>
                </g>
              </svg>  
              GUARDAR
            </button>
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