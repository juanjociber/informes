<?php
  /**
   * TABLA : tblinformes
   */
  function FnAgregarInforme($conmy, $informe) {
    try {
        $stmt = $conmy->prepare("CALL spman_agregarinforme(:_ordid, :_equid, :_cliid, :_fecha, :_ordnombre, :_clinombre, :_clicontacto, :_clidireccion, :_supervisor, :_equcodigo, :_equnombre, :_equmarca, :_equmodelo, :_equserie, :_equdatos, :_equkm, :_equhm, :_actividad, :_usuario, @_id)");
        $stmt->bindParam(':_ordid', $informe['ordid'], PDO::PARAM_INT);
        $stmt->bindParam(':_equid', $informe['equid'], PDO::PARAM_INT);
        $stmt->bindParam(':_cliid', $informe['cliid'], PDO::PARAM_INT);
        $stmt->bindParam(':_fecha', $informe['fecha'], PDO::PARAM_STR);
        $stmt->bindParam(':_ordnombre', $informe['ordnombre'], PDO::PARAM_STR);
        $stmt->bindParam(':_clinombre', $informe['clinombre'], PDO::PARAM_STR);
        $stmt->bindParam(':_clicontacto', $informe['clicontacto'], PDO::PARAM_STR);
        $stmt->bindParam(':_clidireccion', $informe['clidireccion'], PDO::PARAM_STR);
        $stmt->bindParam(':_supervisor', $informe['supervisor'], PDO::PARAM_STR);
        $stmt->bindParam(':_equcodigo', $informe['equcodigo'], PDO::PARAM_STR);
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

  function FnModificarInformeActividad($conmy, $actividad) {
    try {
      $stmt = $conmy->prepare("UPDATE tblinforme SET actividad = :Actividad, actualizacion=:Actualizacion WHERE id = :Id");
      $params = array(':Actividad' => $actividad->actividad,':Actualizacion'=>$actividad->usuario,':Id' => $actividad->id);
      $result = $stmt->execute($params);
      if ($stmt->rowCount() == 0) {
        throw new Exception('Cambios no realizados.');
      }
      return $result;
    } catch (PDOException $ex) {
      throw new Exception($ex->getMessage());
    }
  }

  function FnBuscarInforme($conmy, $id, $cliid) {
    try {
      $stmt = $conmy->prepare("SELECT id, ordid, equid, cliid, numero, nombre, fecha, ord_nombre, cli_nombre, cli_contacto, cli_direccion, supervisor, equ_codigo, equ_nombre, equ_marca, equ_modelo, equ_serie, equ_datos, equ_km, equ_hm, actividad, estado FROM tblinforme WHERE id = :Id AND cliid = :Cliid");
      $stmt->execute(array(':Id' => $id, ':Cliid' => $cliid));
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      if ($row) {
        $informe = new stdClass();
        $informe->Id = $row['id'];
        $informe->OrdId = $row['ordid'];
        $informe->EquId = $row['equid'];
        $informe->CliId = $row['cliid'];
        $informe->Numero = $row['numero'];
        $informe->Nombre = $row['nombre'];
        $informe->Fecha = $row['fecha'];
        $informe->OrdNombre = $row['ord_nombre'];
        $informe->CliNombre = $row['cli_nombre'];
        $informe->CliContacto = $row['cli_contacto'];
        $informe->CliDireccion = $row['cli_direccion'];
        $informe->Supervisor = $row['supervisor'];
        $informe->EquCodigo = $row['equ_codigo'];
        $informe->EquNombre = $row['equ_nombre'];
        $informe->EquMarca = $row['equ_marca'];
        $informe->EquModelo = $row['equ_modelo'];
        $informe->EquSerie = $row['equ_serie'];
        $informe->EquDatos = $row['equ_datos'];
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
      $query.=" limit ".$informe->Pagina.", 2";

      $stmt = $conmy->prepare("select id, nombre, fecha, cli_nombre, actividad, estado from tblinforme where cliid=:CliId".$query.";");
      $stmt->bindParam(':CliId', $informe->CliId, PDO::PARAM_INT);
      $stmt->execute();

      $n=$stmt->rowCount();
      if($n>0){
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
          $informes['data'][]=array(
            'id'=>(int)$row['id'],
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
      throw new Exception($e->getMessage().$msg);
    }
  }

  function FnModificarInforme($conmy, $informe) {
    try {
      $stmt = $conmy->prepare("UPDATE tblinforme SET fecha = :Fecha, cli_contacto=:CliContacto, cli_direccion = :Clidireccion, supervisor = :Supervisor, actualizacion = :Actualizacion WHERE id=:Id");
      $params = array(
        ':Fecha' => $informe->fecha,
        ':CliContacto' => $informe->clicontacto,
        ':Clidireccion' => $informe->clidireccion,
        ':Supervisor' => $informe->supervisor,
        ':Actualizacion' => $informe->actualizacion,
        ':Id' => $informe->id,
      );
      $result = $stmt->execute($params);
      if ($stmt->rowCount() == 0) {
        throw new Exception('Cambios no realizados.');
      }
      return $result;
    } catch (PDOException $e) {
      throw new Exception($e->getMessage());
    }
  }

  function FnModificarInformeEquipo($conmy, $informe) {
    try {
      $stmt = $conmy->prepare("UPDATE tblinforme SET equ_nombre = :EquNombre, equ_marca = :EquMarca, equ_modelo = :EquModelo, equ_serie = :EquSerie, equ_datos = :EquDatos, equ_km = :EquKm, equ_hm = :EquHm, actualizacion = :Actualizacion WHERE id =:Id");
      $params = array(
        ':EquNombre' => $informe->equnombre,
        ':EquMarca' => $informe->equmarca,
        ':EquModelo' => $informe->equmodelo,
        ':EquSerie' => $informe->equserie,
        ':EquDatos' => $informe->equdatos,
        ':EquKm' => $informe->equkm,
        ':EquHm' =>$informe->equhm,
        ':Actualizacion' => $informe->actualizacion,
        ':Id' => $informe->id);
      $result = $stmt->execute($params);
      if ($stmt->rowCount() == 0) {
        throw new Exception('Cambios no realizados.');
      }
      return $result;
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
      $stmt = $conmy->prepare("INSERT INTO tbldetalleinforme (infid, ownid, actividad, diagnostico, trabajos, observaciones, tipo, creacion, actualizacion) VALUES (:InfId, :OwnId, :Actividad, :Diagnostico, :Trabajos, :Observaciones, :Tipo,:Creacion, :Actualizacion);");
      $params = array(':InfId' => $actividad->infid,':OwnId' => $actividad->ownid,':Actividad' => $actividad->actividad,':Diagnostico' => $actividad->diagnostico,':Trabajos' => $actividad->trabajos,':Observaciones' => $actividad->observaciones,':Tipo' => $actividad->tipo, ':Creacion' => $actividad->usuario,':Actualizacion' => $actividad->usuario);
      if ($stmt->execute($params)) {
          $res = true;
      }
      return $res;
    } catch (PDOException $e) {
      throw new Exception($e->getMessage());
    }
  }

  function FnRegistrarInformeActividades($conmy, $actividad) {
    try {
      $res = false;
      $stmt = $conmy->prepare("INSERT INTO tbldetalleinforme (infid, ownid, actividad, diagnostico, trabajos, observaciones, tipo, creacion, actualizacion) VALUES (:InfId, :OwnId, :Actividad, :Diagnostico, :Trabajos, :Observaciones, :Tipo,:Creacion, :Actualizacion);");
      $params = array(':InfId' => $actividad->infid,':OwnId' => $actividad->ownid,':Actividad' => $actividad->actividad,':Diagnostico' => $actividad->diagnostico,':Trabajos' => $actividad->trabajos,':Observaciones' => $actividad->observaciones,':Tipo' => $actividad->tipo, ':Creacion' => $actividad->usuario,':Actualizacion' => $actividad->usuario);
      if ($stmt->execute($params)) {
          $res = true;
      }
      return $res;
    } catch (PDOException $e) {
      throw new Exception($e->getMessage());
    }
  }

  function FnBuscarInformeActividades1($conmy, $id) {
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

  function FnBuscarInformeActividades2($conmy, $infid) {
    try {
      $stmt = $conmy->prepare("SELECT id, ownid, tipo, actividad, diagnostico, trabajos, observaciones, estado FROM tbldetalleinforme WHERE infid = :Infid;");
      $stmt->execute(array(':Infid' => $infid));
      $actividades = $stmt ->fetchAll(PDO::FETCH_ASSOC);;
      return $actividades;
    } catch (PDOException $ex) {
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