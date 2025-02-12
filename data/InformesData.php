<?php
  function FnAgregarInforme($conmy, $informe) {
    try {
        $stmt = $conmy->prepare("CALL spman_agregarinforme(:_ordid, :_equid, :_cliid, :_supid, :_fecha, :_ordnombre, :_clinombre, :_clidireccion, :_clicontacto, :_supnombre, :_equnombre, 
        :_equmarca, :_equmodelo, :_equserie, :_equdatos, :_equkm, :_equhm, :_actividad, :_usuario, @_id)");
        $stmt->bindParam(':_ordid', $informe['ordid'], PDO::PARAM_INT);
        $stmt->bindParam(':_equid', $informe['equid'], PDO::PARAM_INT);
        $stmt->bindParam(':_cliid', $informe['cliid'], PDO::PARAM_INT);
        $stmt->bindParam(':_supid', $informe['supid'], PDO::PARAM_INT);
        $stmt->bindParam(':_fecha', $informe['fecha'], PDO::PARAM_STR);
        $stmt->bindParam(':_ordnombre', $informe['ordnombre'], PDO::PARAM_STR);
        $stmt->bindParam(':_clinombre', $informe['clinombre'], PDO::PARAM_STR);
        $stmt->bindParam(':_clidireccion', $informe['clidireccion'], PDO::PARAM_STR);
        $stmt->bindParam(':_clicontacto', $informe['clicontacto'], PDO::PARAM_STR);
        $stmt->bindParam(':_supnombre', $informe['supnombre'], PDO::PARAM_STR);
        $stmt->bindParam(':_equnombre', $informe['equnombre'], PDO::PARAM_STR);
        $stmt->bindParam(':_equmarca', $informe['equmarca'], PDO::PARAM_STR);
        $stmt->bindParam(':_equmodelo', $informe['equmodelo'], PDO::PARAM_STR);
        $stmt->bindParam(':_equserie', $informe['equserie'], PDO::PARAM_STR);
        $stmt->bindParam(':_equdatos', $informe['equdatos'], PDO::PARAM_STR);
        $stmt->bindParam(':_equkm', $informe['equkm'], PDO::PARAM_INT);
        $stmt->bindParam(':_equhm', $informe['equhm'], PDO::PARAM_INT);
        $stmt->bindParam(':_actividad', $informe['actividad'], PDO::PARAM_STR);
        $stmt->bindParam(':_usuario', $informe['usuario'], PDO::PARAM_STR);
        $stmt->execute();
        $stmt = $conmy->query("SELECT @_id as id");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $id = $row['id'];
        return $id;            
    } catch (PDOException $ex) {
        throw new Exception($ex->getMessage());
    }
}

  function FnAgregarInformeActividades($conmy, $actividades) {
    try {
        $stmt=$conmy->prepare("insert into tbldetalleinforme(infid, ownid, orden, tipo, actividad, diagnostico, trabajos, observaciones, creacion, actualizacion) values(:InfId, :OwnId, :Orden, :Tipo, :Actividad, :Diagnostico, :Trabajos, :Observaciones, :Creacion, :Actualizacion);");
        $stmt2=$conmy->prepare("insert into tblarchivos(refid, tabla, nombre, titulo, descripcion, tipo, creacion) values(:RefId, :Tabla, :Nombre, :Titulo, :Descripcion, :Tipo, :Creacion);");
        foreach ($actividades as $key=>$valor) {
            $stmt->execute(array(
                ':InfId'=>$valor['infid'],
                ':OwnId'=>$valor['ownid'],
                ':Orden'=>$valor['orden'],
                ':Tipo'=>$valor['acttipo'],
                ':Actividad'=>$valor['actnombre'],
                ':Diagnostico'=>$valor['diagnostico'],
                ':Trabajos'=>$valor['trabajos'],
                ':Observaciones'=>$valor['observaciones'],
                ':Creacion'=>$valor['usuario'],
                ':Actualizacion'=>$valor['usuario']
            ));
            $ID=$conmy->lastInsertId();

            if(!empty($valor['arcnombre'])){
                $stmt2->execute(array(
                    ':RefId'=>$ID,
                    ':Tabla'=>$valor['arctabla'],
                    ':Nombre'=>$valor['arcnombre'],
                    ':Titulo'=>$valor['actnombre'],
                    ':Descripcion'=>$valor['trabajos'],
                    ':Tipo'=>$valor['arctipo'],
                    ':Creacion'=>$valor['usuario']
                ));
            }             
        }         
    } catch (PDOException $ex) {
        throw new Exception($ex->getMessage());
    }
  }

  /** */
  function FnModificarInformeActividad($conmy, $actividad) {
    try {
      $res = false;
      $stmt = $conmy->prepare("UPDATE tblinforme SET actividad = :Actividad, actualizacion=:Actualizacion WHERE id = :Id");
      $params = array(':Actividad' => $actividad->actividad,':Actualizacion'=>$actividad->usuario,':Id' => $actividad->id);
      
      if ($stmt->execute($params)) {
        $res = true;
      }
      return $res;
    } catch (PDOException $ex) {
      throw new Exception($ex->getMessage());
    }
  }

  /** */
  function FnBuscarInforme($conmy, $id, $cliid) {
    try {
      $stmt = $conmy->prepare("SELECT id, ordid, equid, cliid, supid, numero, nombre, fecha, ord_nombre, cli_nombre, cli_contacto, cli_direccion, supervisor, equ_nombre, equ_marca, equ_modelo, equ_serie, equ_datos, equ_referencia, equ_km, equ_hm, actividad, estado FROM tblinforme WHERE id = :Id AND cliid = :Cliid");
      $stmt->execute(array(':Id' => $id, ':Cliid' => $cliid));
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      if ($row) {
        $informe = new stdClass();
        $informe->Id = $row['id'];
        $informe->OrdId = $row['ordid'];
        $informe->EquId = $row['equid'];
        $informe->CliId = $row['cliid'];
        $informe->SupId = $row['supid'];
        $informe->Numero = $row['numero'];
        $informe->Nombre = $row['nombre'];
        $informe->Fecha = $row['fecha'];
        $informe->OrdNombre = $row['ord_nombre'];
        $informe->CliNombre = $row['cli_nombre'];
        $informe->CliContacto = $row['cli_contacto'];
        $informe->CliDireccion = $row['cli_direccion'];
        $informe->Supervisor = $row['supervisor'];
        $informe->EquNombre = $row['equ_nombre'];
        $informe->EquMarca = $row['equ_marca'];
        $informe->EquModelo = $row['equ_modelo'];
        $informe->EquSerie = $row['equ_serie'];
        $informe->EquDatos = $row['equ_datos'];
        $informe->EquReferencia = $row['equ_referencia'];
        $informe->EquKm = $row['equ_km'];
        $informe->EquHm = $row['equ_hm'];
        $informe->Actividad = $row['actividad'];
        $informe->Estado = $row['estado'];
        return $informe;
      } 
    } catch (PDOException $ex) {
      throw new Exception($ex->getMessage());
    } catch (Exception $ex) {
      throw new Exception($ex->getMessage());
    }
  }

  function FnBuscarInformes($conmy, $informe) {
    try {
      $informes=array('data'=>array(), 'pag'=>0);
      $query = "";
      if(!empty($informe->Nombre)){
          $query = " and nombre like '%".$informe->Nombre."%'";
      }else{
          if(!empty($informe->Equipo)){$query .=" and equid=".$informe->Equipo;}
          $query.=" and fecha between '".$informe->FechaInicial."' and '".$informe->FechaFinal."'";
      }
      $query.=" limit ".$informe->Pagina.", 15";

      $stmt = $conmy->prepare("select id, cliid, nombre, fecha, cli_nombre, actividad, estado from tblinforme where cliid=:CliId".$query.";");
      $stmt->bindParam(':CliId', $informe->CliId, PDO::PARAM_INT);
      $stmt->execute();

      $n=$stmt->rowCount();
      if($n>0){
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
          $informes['data'][]=array(
            'id'=>(int)$row['id'],
            'cliid'=>(int)$row['cliid'],
            'nombre'=>$row['nombre'],
            'fecha'=>$row['fecha'],
            'clinombre'=>$row['cli_nombre'],
            'actividad'=>$row['actividad'],
            'estado'=>(int)$row['estado']
          );
        }
        $informes['pag']=$n;
      }
      return $informes;
    } catch (PDOException $e) {
      throw new Exception($e->getMessage());
    }
  }

  /** */
  function FnModificarInforme($conmy, $informe) {
    try {
      $res = false;
      $stmt = $conmy->prepare("UPDATE tblinforme SET supid = :SupId, fecha = :Fecha, cli_contacto=:CliContacto, cli_direccion = :Clidireccion, supervisor = :Supervisor, actualizacion = :Actualizacion WHERE id=:Id");
      $params = array(
        ':SupId' => $informe->supid, 
        ':Fecha' => $informe->fecha,
        ':CliContacto' => $informe->clicontacto,
        ':Clidireccion' => $informe->clidireccion,
        ':Supervisor' => $informe->supervisor,
        ':Actualizacion' => $informe->actualizacion,
        ':Id' => $informe->id,
      );
      if ($stmt->execute($params)) {
        $res=true;
      }
      return $res;
    } catch (PDOException $e) {
      throw new Exception($e->getMessage());
    }
  }

  /** */
  function FnModificarInformeEquipo($conmy, $informe) {
    try {
      $res = false;
      $stmt = $conmy->prepare("UPDATE tblinforme SET equid=:Equid, equ_nombre = :EquNombre, equ_marca = :EquMarca, equ_modelo = :EquModelo, equ_serie = :EquSerie, equ_datos = :EquDatos, equ_referencia =:EquReferencia, equ_km = :EquKm, equ_hm = :EquHm, actualizacion = :Actualizacion WHERE id =:Id");
      $params = array(
        ':Equid' => $informe->equid,
        ':EquNombre' => $informe->equnombre,
        ':EquMarca' => $informe->equmarca,
        ':EquModelo' => $informe->equmodelo,
        ':EquSerie' => $informe->equserie,
        ':EquDatos' => $informe->equdatos,
        ':EquReferencia' => $informe->equreferencia,
        ':EquKm' => $informe->equkm,
        ':EquHm' =>$informe->equhm,
        ':Actualizacion' => $informe->actualizacion,
        ':Id' => $informe->id);
      if ($stmt->execute($params)) {
        $res=true;
      }
      return $res;
    } catch (PDOException $ex) {
      throw new Exception($ex->getMessage());
    }
  }

  /**
   * TABLA : tbldetalleinforme
   */
  function FnRegistrarInformeActividad($conmy, $actividad) {
    try {
      $res = false;
      $stmtMaxOrden = $conmy->prepare("SELECT MAX(orden) AS max_orden FROM tbldetalleinforme WHERE infid = :InfId");
      $stmtMaxOrden->execute(array(':InfId' => $actividad->infid));
      $resultado = $stmtMaxOrden->fetch(PDO::FETCH_ASSOC);
      $nuevoOrden = 1; 
      if ($resultado && $resultado['max_orden'] !== null) {
        $nuevoOrden = $resultado['max_orden'] + 1;
      }
      $stmt = $conmy->prepare("INSERT INTO tbldetalleinforme (infid, ownid, orden, tipo, actividad, diagnostico, trabajos, observaciones, creacion, actualizacion) 
                                VALUES (:InfId, :OwnId, :Orden, :Tipo, :Actividad, :Diagnostico, :Trabajos, :Observaciones, :Creacion, :Actualizacion);");
      $params = array(
        ':InfId' => $actividad->infid,
        ':OwnId' => $actividad->ownid,
        ':Orden' => $nuevoOrden,
        ':Tipo' => $actividad->tipo,
        ':Actividad' => $actividad->actividad,
        ':Diagnostico' => $actividad->diagnostico,
        ':Trabajos' => $actividad->trabajos,
        ':Observaciones' => $actividad->observaciones,
        ':Creacion' => $actividad->usuario,
        ':Actualizacion' => $actividad->usuario
      );
      if ($stmt->execute($params)) {
        $res = true;
      }
      return $res;
    } catch (PDOException $ex) {
        throw new Exception($ex->getMessage());
    }
  }

  function FnRegistrarInformeActividades($conmy, $actividad) {
    try {
      $res = false;
      $stmt = $conmy->prepare("INSERT INTO tbldetalleinforme (infid, ownid, orden, actividad, diagnostico, trabajos, observaciones, tipo, creacion, actualizacion) VALUES (:InfId, :OwnId, :Orden, :Actividad, :Diagnostico, :Trabajos, :Observaciones, :Tipo,:Creacion, :Actualizacion);");
      $params = array(':InfId' => $actividad->infid,':OwnId' => $actividad->ownid, ':Orden' => $actividad->orden, ':Actividad' => $actividad->actividad,':Diagnostico' => $actividad->diagnostico,':Trabajos' => $actividad->trabajos,':Observaciones' => $actividad->observaciones,':Tipo' => $actividad->tipo, ':Creacion' => $actividad->usuario,':Actualizacion' => $actividad->usuario);
      if ($stmt->execute($params)) {
        $res = true;
      }
      return $res;
    } catch (PDOException $e) {
      throw new Exception($e->getMessage());
    } 
  }

  function FnBuscarInformeActividad($conmy, $id) {
    try {
      $stmt = $conmy->prepare("SELECT id, infid, ownid, tipo, actividad, diagnostico, trabajos, observaciones, estado FROM tbldetalleinforme WHERE id = :Id;");
      $stmt->execute(array(':Id' => $id));
      $actividad = new stdClass();
      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $actividad->id = $row['id'];
        $actividad->infid = $row['infid'];
        $actividad->ownid = $row['ownid'];
        $actividad->tipo = $row['tipo'];
        $actividad->actividad = $row['actividad'];
        $actividad->diagnostico = $row['diagnostico'];
        $actividad->trabajos = $row['trabajos'];
        $actividad->observaciones = $row['observaciones'];
        $actividad->estado = $row['estado'];
      }
      return $actividad;
    } catch (PDOException $e) {
      throw new Exception($e->getMessage());
    }
  }

  function FnBuscarInformeActividades($conmy, $infid) {
    try {
        $stmt = $conmy->prepare("SELECT id, ownid, orden, tipo, actividad, diagnostico, trabajos, observaciones, estado FROM tbldetalleinforme WHERE infid = :Infid order by orden");
        $stmt->execute(array(':Infid' => $infid));
        $datos = array(); 
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
          $datos[] = $row; 
        }
        return $datos; 
    } catch (PDOException $ex) {
      throw new Exception($ex->getMessage());
    }
      catch (Exception $ex) {
      throw new Exception($ex->getMessage());
    }
  }

  function FnModificarInformeActividades($conmy, $actividad) {
    try {
      $stmt = $conmy->prepare("UPDATE tbldetalleinforme SET actividad=:Actividad, diagnostico=:Diagnostico, trabajos=:Trabajos, observaciones=:Observaciones, actualizacion=:Actualizacion WHERE id=:Id;");
      $params = array(':Actividad'=>$actividad->actividad, ':Diagnostico'=>$actividad->diagnostico, ':Trabajos'=>$actividad->trabajos, ':Observaciones'=>$actividad->observaciones, ':Actualizacion'=>$actividad->usuario, ':Id'=>$actividad->id);
      $result = $stmt->execute($params);
      if($stmt->rowCount()==0){
        throw new Exception('Cambios no realizados.');
      }
      return $result;
    } catch (PDOException $e) {
      throw new Exception($e->getMessage());
    }
  }

  function FnEliminarInformeActividades($conmy, $id) {
    try {
      $stmt = $conmy->prepare("DELETE FROM tbldetalleinforme WHERE id = :Id");
      $params = array(':Id' => $id);
      $result = $stmt->execute($params);
      if($stmt->rowCount()==0){
        throw new Exception('Cambios no realizados.');
      }
      return $result;
    } catch (PDOException $e) {
      throw new Exception($e->getMessage());
    }
  }
?>