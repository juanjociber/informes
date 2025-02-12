
<?php
	session_start();
	require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/SesionData.php";
	require_once $_SERVER['DOCUMENT_ROOT']."/gesman/connection/ConnGesmanDb.php";
	require_once $_SERVER['DOCUMENT_ROOT'].'/informes/data/InformesData.php';

	$NOMBRE='UNKNOWN';
	$html5='';

	//$PATH_FILE = '/mycloud';
	$PATH_FILE = $_SERVER['DOCUMENT_ROOT'].'/mycloud';

    function construirArbol($registros, $padreId = 0) {
		$arbol = array();
		foreach ($registros as $registro) {
			if ($registro['ownid'] == $padreId) {
				$hijos = construirArbol($registros, $registro['id']);
				if (!empty($hijos)) {
					$registro['hijos'] = $hijos;
				}					
				$arbol[] = $registro;
			}
		}			
		return $arbol;
	}

    function FnGenerarInformeHtml($arbol, $imagenes, $nivel = 0, $indice ='1') {
		$html='<table width="100%" style="border: #b2b2b2 1px solid">';
		$contador=1;
		global $PATH_FILE;	

		foreach ($arbol as $key=>$nodo) {
			$indiceActual = $nivel==0?$contador++:$indice.'.'.($key+1);
			$html.='<tr><td colspan="4" style="border: red 1px solid;">'.$indiceActual.' - '.$nodo['actividad'].'</td></tr>';
			//$html.='<tr><td style="height:10px;" colspan="4"></td></tr>';
			$html.='
				<tr>
					<td style="padding:5px; text-align:center; font-weight:bold;">DIAGNOSTICO</td>
					<td style="padding:5px; text-align:center; font-weight:bold;">TRABAJOS</td>
				</tr>
				<tr>
					<td style="padding:5px;">'.$nodo['diagnostico'].'</td>
					<td style="padding:5px;">'.$nodo['trabajos'].'</td>
				</tr>
				<tr>
					<td colspan="4" style="padding:5px; font-weight:bold;">OBSERVACIONES</td>
				</tr>
				<tr>
					<td colspan="2" style="padding:5px;">'.$nodo['observaciones'].'</td>
				</tr>
				<tr><td style="height:10px;" colspan="4"></td></tr>';
			
			$imagen=array();
			if(isset($imagenes[$nodo['id']])){
				$html.='<tr><td colspan="4" style="padding-left:5px;"><table width="100%" style="border: 1px solid; color:red">';
				
                $i=1;
				foreach($imagenes[$nodo['id']] as $elemento){
					if($i==2){
                    $html.='
                            <td width="50%" style="border: 1px solid; text-align:center;">
                                <p style="margin:0px;">'.$elemento['titulo'].'</p>
                                <img src="'.$PATH_FILE.'/gesman/files/'.$elemento['nombre'].'" style="max-height:200px; width:auto;"/>
                                <p style="margin:0px;">'.$elemento['descripcion'].'</p>
                            </td>
                        </tr>';
                        $i=1;
                    }else{
                        $html.='
                        <tr>
                            <td width="50%" style="border: 1px solid; text-align:center;">
                                <p style="margin:0px;">'.$elemento['titulo'].'</p>
                                <img src="'.$PATH_FILE.'/gesman/files/'.$elemento['nombre'].'" style="max-height:200px; width:auto;"/>
                                <p style="margin:0px;">'.$elemento['descripcion'].'</p>
                            </td>';
                        $i+=1;
                    }
                }

                if($i==2){$html.='</tr>';}		
				$html.='
							</table>
						</td>
				</tr>';
			}

			if (!empty($nodo['hijos'])) {
				$html.='
				<tr>
					<td colspan="4" style="border: blue 1px solid">';
					$html.=FnGenerarInformeHtml($nodo['hijos'], $imagenes, $nivel+1, $indiceActual);
					$html.='
					</td>
				</tr>';
			}
		}
		$html.='</table>';
		return $html;
	}

	function FnGenerarInformeHtml2($arbol, $imagenes, $numero, $nivel = 0, $indice ='1') {
		$html='';
		$contador=1;
		global $PATH_FILE;	

		foreach ($arbol as $key=>$nodo) {
			$indiceActual = $nivel==0?$contador++:$indice.'.'.($key+1);
			$html.='
			<tr>
				<td colspan="2" style="font-weight:bold; background-color:#dcdcdc;">'.$numero.'.'.$indiceActual.' - '.htmlspecialchars(utf8_encode($nodo['actividad'])).'</td>
			</tr>';

			if(!empty($nodo['diagnostico'])){
				$html.='
				<tr>
					<td colspan="2" style="padding-left:5px; padding-bottom:0px; font-weight:bold;">Diagn&oacute;stico</td>
				</tr>
				<tr>
					<td colspan="2" style="padding-left:5px; padding-top:0px; padding-bottom:10px;">'.htmlspecialchars(utf8_encode($nodo['diagnostico'])).'</td>
				</tr>';
			}

			if(!empty($nodo['trabajos'])){
				$html.='
				<tr>
					<td colspan="2" style="padding-left:5px; padding-bottom:0px; font-weight:bold;">Trabajos</td>
				</tr>
				<tr>
					<td colspan="2" style="padding-left:5px; padding-top:0px; padding-bottom:10px;">'.htmlspecialchars(utf8_encode($nodo['trabajos'])).'</td>
				</tr>';
			}
			
			if(isset($imagenes[$nodo['id']])){

				/*foreach($imagenes[$nodo['id']] as $elemento){
					$html.='
					<tr>
						<td colspan="2" style="border: 1px solid; text-align:center;">
							<p style="margin:0px;">'.utf8_encode($elemento['titulo']).'</p>
							<img src="'.$_SERVER['DOCUMENT_ROOT'].'/mycloud/gesman/files/'.$elemento['nombre'].'" style="max-width:200px; max-height:300px;"/>
							<p style="margin:0px;">'.utf8_encode($elemento['descripcion']).'</p>
						</td>
					</tr>
					<tr>
						<td colspan="2" style="height:5px;"></td>
					</tr>';					
				}*/

				if(count($imagenes[$nodo['id']])==1){
					$html.='
					<tr>
						<td colspan="2" style="border: 1px solid; text-align:center;">
							<p style="margin:0px;">'.htmlspecialchars(utf8_encode($imagenes[$nodo['id']][0]['titulo'])).'</p>
                            <img src="'.$PATH_FILE.'/gesman/files/'.$imagenes[$nodo['id']][0]['nombre'].'" style="max-height:200px; max-width:300px;"/>
                            <p style="margin:0px;">'.htmlspecialchars(utf8_encode($imagenes[$nodo['id']][0]['descripcion'])).'</p>
						</td>
					</tr>';
				}else{
					$i=1;
					foreach($imagenes[$nodo['id']] as $elemento){
						if($i==2){
							$html.='
								<td width="50%" style="border: 1px solid; text-align:center;">
									<p style="margin:0px;">'.htmlspecialchars(utf8_encode($elemento['titulo'])).'</p>
									<img src="'.$PATH_FILE.'/gesman/files/'.$elemento['nombre'].'" style="max-height:200px; max-width:300px;"/>
									<p style="margin:0px;">'.htmlspecialchars(utf8_encode($elemento['descripcion'])).'</p>
								</td>
							</tr>';
							$i=1;
						}else{
							$html.='
							<tr>
								<td width="50%" style="border: 1px solid; text-align:center;">
									<p style="margin:0px;">'.htmlspecialchars(utf8_encode($elemento['titulo'])).'</p>
									<img src="'.$PATH_FILE.'/gesman/files/'.$elemento['nombre'].'" style="max-height:200px; max-width:300px;;"/>
									<p style="margin:0px;">'.htmlspecialchars(utf8_encode($elemento['descripcion'])).'</p>
								</td>';
							$i+=1;
						}
					}

					if($i==2){
						$html.='</tr>';
					}
				}
												
				$html.='
				<tr>
					<td colspan="2" style="height:5px;"></td>
				</tr>';
			}

			if(!empty($nodo['observaciones'])){
				$html.='
				<tr>
					<td colspan="2" style="padding-left:5px; padding-bottom:0px; font-weight:bold;">Observaciones</td>
				</tr>
				<tr>
					<td colspan="2" style="padding-left:5px; padding-top:0px; padding-bottom:10px;">'.htmlspecialchars(utf8_encode($nodo['observaciones'])).'</td>
				</tr>';
			}

			if (!empty($nodo['hijos'])) {
				$html.=FnGenerarInformeHtml2($nodo['hijos'], $imagenes, $numero, $nivel+1, $indiceActual);
			}
		}
		return $html;
	}

    $informe=array();

	try{
		$conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$informe=FnBuscarInforme($conmy, $_GET['id'], $_SESSION['gesman']['CliId']);

		$NOMBRE=$informe->Nombre;

        $actividades=array();
		$analisiss=array();
		$conclusiones=array();
		$recomendaciones=array();
		$antecedentes=array();

		$datos=FnBuscarInformeActividades($conmy, $informe->Id);

		foreach($datos as $dato){
			if($dato['tipo']=='act'){
				$actividades[]=array(
					'id'=>$dato['id'],
					'ownid'=>$dato['ownid'],
					'tipo'=>$dato['tipo'],
					'actividad'=>$dato['actividad'],
					'diagnostico'=>$dato['diagnostico'],
					'trabajos'=>$dato['trabajos'],
					'observaciones'=>$dato['observaciones'],
				);
			}else if($dato['tipo']=='con'){
				$conclusiones[]=array('actividad'=>$dato['actividad']);
			}else if($dato['tipo']=='ana'){
				$analisiss[]=array('actividad'=>$dato['actividad']);
			}else if($dato['tipo']=='rec'){
				$recomendaciones[]=array('actividad'=>$dato['actividad']);
			}else if($dato['tipo']=='ant'){
				$antecedentes[]=array('actividad'=>$dato['actividad']);
			}	
		}

        $arbol = construirArbol($actividades);

		$ids = array_map(function($elemento) {
			return $elemento['id'];
		}, $actividades);

		$cadenaIds = implode(',', $ids);
		$imagenes=array();

		if(!empty($cadenaIds)){
			$stmt3 = $conmy->prepare("select id, refid, nombre, titulo, descripcion from tblarchivos where refid IN(".$cadenaIds.") and tabla=:Tabla and tipo=:Tipo;");				
			$stmt3->execute(array(':Tabla'=>'INFD', ':Tipo'=>'IMG'));
			while($row3=$stmt3->fetch(PDO::FETCH_ASSOC)){
				$imagenes[$row3['refid']][]=array(
					'id'=>(int)$row3['id'],
					'nombre'=>$row3['nombre'],
					'titulo'=>$row3['titulo'],
					'descripcion'=>$row3['descripcion']
				);
			}
		}

		$ImgInforme=array();
		$stmt4 = $conmy->prepare("select nombre, titulo, descripcion from tblarchivos where refid=:RefId and tabla=:Tabla and tipo=:Tipo;");				
		$stmt4->execute(array('RefId'=>$informe->Id, ':Tabla'=>'INFE', ':Tipo'=>'IMG'));
		$ImgInforme = $stmt4->fetchAll(PDO::FETCH_ASSOC);

		$ImgAnexos=array();
		$stmt4 = $conmy->prepare("select nombre, titulo, descripcion from tblarchivos where refid=:RefId and tabla=:Tabla and tipo=:Tipo;");				
		$stmt4->execute(array('RefId'=>$informe->Id, ':Tabla'=>'INFA', ':Tipo'=>'IMG'));
		$ImgAnexos = $stmt4->fetchAll(PDO::FETCH_ASSOC);
	}catch(PDOException $ex){
		$conmy=null;
	};

	$html5='
	<!DOCTYPE html>
		<html lang="es">
			<head>
				<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
				<title>INFORME_'.$NOMBRE.'</title>
				<style>
					@page {
						margin: 0cm 0cm;
					}

					*{
						font-family: DejaVu Sans, sans-serif;
						font-size: 11px;
					}

					body {
						margin-top: 3cm;
						margin-left: 1cm;
						margin-right: 1cm;
						margin-bottom: 3.2cm;
					}

					header {
						position: fixed;
						top: 0.5cm;
						left: 1cm;
						right: 1cm;
						height: 2.3cm;
					}

					main{
						left:1cm;
						right:1cm;
					}

					footer {
						position: fixed;
						bottom:   0;
						left:     0px;
						height:   3.4cm;
						border-top:1px solid #B4B4B4;
					}

				</style>
			</head>';

	$html5.='
	<footer>
		<p style="text-align:center; padding:0px; margin:0px;">AV. Los Incas 4ta Cuadra S/N - Comas - Lima - Per&uacute; - Telf. (511) 7130629 Anexo 300</p>
		<p style="text-align:center; padding:0px; margin:0px;">e-mail: hola@gpemsac.com</p>
		<img src="'.$PATH_FILE."/logos/footer-gpem.jpg".'" width="100%"/>
	</footer>';
    
    $html5.='
    <header>
        <table style="border-spacing:0; width: 100%;">
			<tbody>
				<tr>
					<td rowspan="2" style="width: 25%;"><img src="'.$PATH_FILE."/logos/logo-gpem.png".'" style="height: 50px;"></td>
					<td style="text-align:center; font-weight: bold; font-size:14px; padding:3px; width:50%;">GESTION DE PROCESOS EFICIENTES DE MANTENIMIENTO S.A.C.</td>                     
					<td style="border-right:1px solid; border-top: 1px solid; border-left:1px solid; padding:5px; text-align:center; font-weight:bold; font-size:16px; width:25%;">INFORME</td>                     
				</tr>
				<tr>
					<td style="text-align:center;">Av. Los Incas S/N - Comas <br> Telf: 01-7130628</td>
					<td style="border-bottom: 1px solid; border-right: 1px solid; border-left:1px solid; padding:5px; text-align:center; font-weight:bold; font-size:16px;">'.$informe->Nombre.'</td>
				</tr>
			</tbody>
        </table>
    </header>';

	$html5.='
	<body>
		<main>';

	$NUMERO=1;
	//SECCION DATOS GENERALES
	$html5.='
	<table width="100%" style="border: #b2b2b2 1px solid; margin-bottom: 10px;">
		<tbody>
			<tr>
				<td colspan="4" style="font-weight:bold; font-size: 13px; background-color:#dcdcdc;">'.$NUMERO.'- DATOS GENERALES</td>
			</tr>
			<tr style="height:25px;">
				<td width="15%" style="padding-left:5px">Cliente</td>
				<td width="50%" style="padding-left:5px">: '.htmlspecialchars(utf8_encode($informe->CliNombre)).'</td>
				<td width="15%" style="padding-left:5px;">Fecha :</td>
				<td width="20%" style="padding-left:5px">: '.$informe->Fecha.'</td>
			</tr>
			<tr style="height:25px;">
				<td style="padding-left:5px;">Direcci&oacute;n</td>
				<td style="padding-left:5px">: '.htmlspecialchars(utf8_encode($informe->CliDireccion)).'</td>
				<td style="padding-left:5px;">OT</td>
				<td style="padding-left:5px">: '.$informe->OrdNombre.'</td>
			</tr>
			<tr style="height:25px;">
				<td style="padding-left:5px;">Contacto</td>
				<td style="padding-left:5px">: '.htmlspecialchars(utf8_encode($informe->CliContacto)).'</td>
				<td style="padding-left:5px;"></td>
				<td style="padding-left:5px"></td>
			</tr>';
		$html5.='
		</tbody>
	</table>';
	$NUMERO+=1;

	//SECCION INFORMACION DEL EQUIPO
	$html5.='
	<table width="100%" style="border: #b2b2b2 1px solid; margin-bottom: 10px;">
		<tbody>
			<tr>
				<td colspan="4" style="font-weight:bold; font-size: 13px; background-color:#dcdcdc;">'.$NUMERO.'- DATOS DEL EQUIPO</td>
			</tr>
			<tr style="height:25px;">
				<td style="padding-left:5px;" width="10%">Nombre</td>
				<td style="padding-left:5px;" width="40%">: '.htmlspecialchars(utf8_encode($informe->EquNombre)).'</td>
				<td style="padding-left:5px;">Marca</td>
				<td style="padding-left:5px;">: '.htmlspecialchars(utf8_encode($informe->EquMarca)).'</td>
			</tr>
			<tr style="height:25px;">
				<td style="padding-left:5px;">Modelo</td>
				<td style="padding-left:5px;">: '.htmlspecialchars(utf8_encode($informe->EquModelo)).'</td>
				<td style="padding-left:5px;">Serie</td>
				<td style="padding-left:5px;">: '.$informe->EquSerie.'</td>
			</tr>
			<tr style="height:25px;">
				<td style="padding-left:5px;">Kilometraje</td>
				<td style="padding-left:5px;">: '.$informe->EquKm.'</td>
				<td style="padding-left:5px;">H. Motor</td>
				<td style="padding-left:5px;">: '.$informe->EquHm.'</td>
			</tr>';
			if(!empty($informe->EquDatos)){
				$html5.='
				<tr style="height:25px;">
					<td style="padding-left:5px;" width="10%">Caracter&iacute;sticas</td>
					<td colspan="3" style="padding-left:5px;" width="90%">: '.htmlspecialchars(utf8_encode($informe->EquDatos)).'</td>
				</tr>';
			}
		$html5.='
		</tbody>
	</table>';

	//SECCION IMAGENES DEL EQUIPO
	if(count($ImgInforme)>0){
		$html5.='
		<table width="100%" style="margin-bottom: 10px;">
			<tbody>';
			foreach ($ImgInforme as $imagen) {
				$html5.='
				<tr>
					<td colspan="2" style="border: 1px solid; text-align:center; padding:5px;">
						<p style="margin:0px;">'.htmlspecialchars(utf8_encode($imagen['titulo'])).'</p>
						<img src="'.$_SERVER['DOCUMENT_ROOT'].'/mycloud/gesman/files/'.$imagen['nombre'].'" style="max-width:600px; max-height:700px;"/>
						<p style="margin:0px;">'.htmlspecialchars(utf8_encode($imagen['descripcion'])).'</p>
					</td>
				</tr>';
			}
			$html5.='
			</tbody>
		</table>';	
	}

	$html5.='
	<table width="100%" style="margin-bottom: 10px;">
		<tbody>';
		if(count($ImgInforme)==1){
			$html5.='
			<tr>
				<td style="border: 1px solid; text-align:center;">
					<p style="margin:0px;">'.htmlspecialchars(utf8_encode($ImgInforme[0]['titulo'])).'</p>
					<img src="'.$PATH_FILE.'/gesman/files/'.$ImgInforme[0]['nombre'].'" style="max-height:200px; max-width:300px;">
					<p style="margin:0px;">'.htmlspecialchars(utf8_encode($ImgInforme[0]['descripcion'])).'</p>
				</td>
			</tr>';
		}else if(count($ImgInforme)>1){
			$i=1;
			foreach ($ImgInforme as $imagen) {
				if($i==2){					
					$html5.='
						<td width="50%" style="border: 1px solid; text-align:center;">
							<p style="margin:0px;">'.htmlspecialchars(utf8_encode($imagen['titulo'])).'</p>
							<img src="'.$PATH_FILE.'/gesman/files/'.$imagen['nombre'].'" class="img-fluid" style="max-height:200px; max-width:300px;">
							<p style="margin:0px;">'.htmlspecialchars(utf8_encode($imagen['descripcion'])).'</p>
						</td>
					</tr>';
					$i=1;
				}else{
					$html5.='
					<tr>
						<td width="50%" style="border: 1px solid; text-align:center;">
							<p style="margin:0px;">'.htmlspecialchars(utf8_encode($imagen['titulo'])).'</p>
							<img src="'.$PATH_FILE.'/gesman/files/'.$imagen['nombre'].'" class="img-fluid" style="max-height:200px; max-width:300px;">
							<p style="margin:0px;">'.htmlspecialchars(utf8_encode($imagen['descripcion'])).'</p>
						</td>';
					$i+=1;
				}
			}

			if($i==2){
				$html.='</tr>';
			}
		}
		$html5.='
		</tbody>
	</table>';
	$NUMERO+=1;

	//SECCION SOLICITUD DEL CLIENTE
	$html5.='
	<table width="100%" style="border: #b2b2b2 1px solid; margin-bottom: 10px;">
		<tbody>
			<tr>
				<td style="font-weight:bold; font-size: 13px; background-color:#dcdcdc;">'.$NUMERO.'- SOLICITUD DEL CLIENTE</td>
			</tr>
			<tr>
				<td>'.htmlspecialchars(utf8_encode($informe->Actividad)).'</td>
			</tr>
		</tbody>
	</table>';
	$NUMERO+=1;

	//SECCION ANTECEDENTES
	if(count($antecedentes)>0){
		$html5.=
		'<div width="100%" style="border: #b2b2b2 1px solid; margin-bottom: 10px;">
			<p style="font-weight:bold; font-size: 13px; background-color:#dcdcdc; margin:0;">'.$NUMERO.'- '.utf8_encode('ANTECEDENTES').'</p>
			<ul style="margin-left: 5px; padding-left:5px;">';
			foreach ($antecedentes as $antecedente) {
				$html5.='<li style="padding-left:15px;">'.htmlspecialchars(utf8_encode($antecedente['actividad'])).'</li>';
			}
		$html5.='
			</ul>
		</div>';
		$NUMERO+=1;
	}

	//SECCION ACTIVIDADES
	$html5.='
	<table width="100%" style="border: #b2b2b2 1px solid; margin-bottom: 10px; border-collapse: separate">
		<tbody>
			<tr>
				<td colspan="2" style="font-weight:bold; font-size: 13px; background-color:#dcdcdc;">'.$NUMERO.'- ACTIVIDADES</td>
			</tr>';
			$html5.=FnGenerarInformeHtml2($arbol, $imagenes, $NUMERO);
		$html5.='
		</tbody>
	</table>';
	$NUMERO+=1;

	//SECCION ANALISIS
	if(count($analisiss)>0){
		$html5 .=
		'<div width="100%" style="border: #b2b2b2 1px solid; margin-bottom: 10px;">
			<p style="font-weight:bold; font-size: 13px; background-color:#dcdcdc; margin:0;">'.$NUMERO.'- '.utf8_encode('ANÁLISIS').'</p>
			<ul style="margin-left: 5px; padding-left:5px;">';
		  	foreach ($analisiss as $analisis) {
				$html5 .= '<li style="padding-left:15px; text-align:justify;">'.htmlspecialchars(utf8_encode($analisis['actividad'])).'</li>';
		  	}
		$html5 .=
    	'</ul>
		</div>';
		$NUMERO+=1;
	}

	//SECCION CONCLUSIONES
	if(count($conclusiones)>0){
		$html5.=
		'<div width="100%" style="border: #b2b2b2 1px solid; margin-bottom: 10px;">
			<p style="font-weight:bold; font-size: 13px; background-color:#dcdcdc; margin:0;">'.$NUMERO.'- '.utf8_encode('CONCLUSIONES').'</p>
			<ul style="margin-left: 5px; padding-left:5px;">';
			foreach ($conclusiones as $conclusion) {
				$html5.='<li style="padding-left:15px; text-align:justify;">'.htmlspecialchars(utf8_encode($conclusion['actividad'])).'</li>';
			}
		$html5.=
      '</ul>
		</div>';
		$NUMERO+=1;
	}

	//SECCION RECOMENDACIONES
	if(count($recomendaciones)>0){
		$html5.=
    '<div width="100%" style="border: #b2b2b2 1px solid; margin-bottom: 10px;">
			<p style="font-weight:bold; font-size: 13px; background-color:#dcdcdc; margin:0;">'.$NUMERO.'- '.utf8_encode('RECOMENDACIONES').'</p>
			<ul style="margin-left: 5px; padding-left:5px;">';
      foreach ($recomendaciones as $recomendacion) {
				$html5.='<li style="padding-left:15px;">'.htmlspecialchars(utf8_encode($recomendacion['actividad'])).'</li>';
			}
		$html5.='
			</ul>
		</div>';
		$NUMERO+=1;
	}

	//SECCION ANEXOS
	if(count($ImgAnexos)>0){
		$html5.='
		<table width="100%" style="border: #b2b2b2 1px solid; margin-bottom:10px;">
			<tbody>
				<tr>
					<td  style="font-weight:bold; font-size: 13px; background-color:#dcdcdc;">'.$NUMERO.'- ANEXOS</td>
				</tr>';
				foreach ($ImgAnexos as $anexo) {
					$html5.='
					<tr>
						<td style="border: 1px solid; text-align:center; padding:5px;">
							<p style="margin:0px; font-weight:bold; font-size:14px;">'.htmlspecialchars(utf8_encode($anexo['titulo'])).'</p>
							<img src="'.$PATH_FILE.'/gesman/files/'.$anexo['nombre'].'" style="max-width:600px; max-height:700px;">
							<p style="margin:0px;">'.htmlspecialchars(utf8_encode($anexo['descripcion'])).'</p>
						</td>
					</tr>';
				}
			$html5.='
			</tbody>
		</table>';
	}

	//SECCION FIRMAS
	$html5.='
	<table width="100%" style="margin-top:80px;">
		<tbody>
			<tr>
				<td width="35%"></td>
				<td width="35%"></td>
				<td width="30%" style="text-align:center;">
					<img src="'.$PATH_FILE.'/gesman/firmas/'.$informe->SupId.'.jpeg" style="max-width:180px; max-height:180px;">
				</td>
			</tr>
			<tr>
				<td width="35%"></td>
				<td width="35%"></td>
				<td width="30%" style="border-top:1px solid; text-align:center;"><p style="margin:0px; font-weight:bold;">'.htmlspecialchars(utf8_encode($informe->Supervisor)).'</p><p style="margin:0px;">SUPERVISOR RESPONSABLE</p></td>
			</tr>
		</tbody>
	</table>';

	$html5.='
			</main>
		</body>
	</html>';

	//echo $html5;



    // require_once $_SERVER['DOCUMENT_ROOT']."/mycloud/library/dompdf_0-8-3/autoload.inc.php";
	require_once $_SERVER['DOCUMENT_ROOT']."/mycloud/library/dompdf_1_0_2/autoload.inc.php";
	use Dompdf\Dompdf;
	$dompdf = new DOMPDF();
	$dompdf->load_html(utf8_decode($html5));
	// $pdf->set_option('enable_html5_parser', TRUE);
	//$pdf->set_paper("letter", "portrait");
	$dompdf->setPaper('A4', 'portrait');//Definimos el tamaño y orientación del papel que queremos.
	$dompdf->render();	// Renderiza el HTML a PDF
	$dompdf->stream('INFORME_'.$NOMBRE.'.pdf', array('Attachment'=>1));// Envía el PDF al navegador

?>