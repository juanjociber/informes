<?php
  /**
   * TABLA : tblinformes
   */
  function FnRegistrarInforme($conmy, $orden, $cliente, $equipo, $fecha, $actividad, $usuario) {
    try {
      $stmt = $conmy->prepare("CALL spman_agregarinforme(:_ordid, :_equid, :_cliid, :_fecha, :_ordnombre, :_clinombre, :_clicontacto, :_clidireccion, :_supervisor, :_equcodigo, :_equnombre, :_equmarca, :_equmodelo, :_equserie, :_equdatos, :_equkm, :_equhm, :_actividad, :_usuario, @_id)");
      $stmt->bindParam(':_ordid', $orden->id, PDO::PARAM_INT);
      $stmt->bindParam(':_equid', $equipo->id, PDO::PARAM_INT);
      $stmt->bindParam(':_cliid', $cliente->id, PDO::PARAM_INT);
      $stmt->bindParam(':_fecha', $fecha, PDO::PARAM_STR);
      $stmt->bindParam(':_ordnombre', $orden->nombre, PDO::PARAM_STR);
      $stmt->bindParam(':_clinombre', $cliente->nombre, PDO::PARAM_STR);
      $stmt->bindParam(':_clicontacto', $orden->contacto, PDO::PARAM_STR);
      $stmt->bindParam(':_clidireccion', $equipo->cli_direccion, PDO::PARAM_STR);
      $stmt->bindParam(':_supervisor', $orden->supervisor, PDO::PARAM_STR);
      $stmt->bindParam(':_equcodigo', $equipo->codigo, PDO::PARAM_STR);
      $stmt->bindParam(':_equnombre', $equipo->nombre, PDO::PARAM_STR);
      $stmt->bindParam(':_equmarca', $equipo->marca, PDO::PARAM_STR);
      $stmt->bindParam(':_equmodelo', $equipo->modelo, PDO::PARAM_STR);
      $stmt->bindParam(':_equserie', $equipo->serie, PDO::PARAM_STR);
      $stmt->bindParam(':_equdatos', $equipo->caracteristicas, PDO::PARAM_STR);
      $stmt->bindParam(':_equkm', $orden->km, PDO::PARAM_INT);
      $stmt->bindParam(':_equhm', $orden->hm, PDO::PARAM_INT);
      $stmt->bindParam(':_actividad', $actividad, PDO::PARAM_STR);
      $stmt->bindParam(':_usuario', $usuario, PDO::PARAM_STR);
      $stmt->execute();

      $stmt = $conmy->query("SELECT @_id as id");
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      $id = $row['id'];

      return $id;
    } catch (PDOException $e) {
      throw new Exception($e->getMessage());
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
      $res=false;
      $stmt = $conmy->prepare("update tblinforme set fecha=:Fecha, cli_contacto=:CliContacto, cli_direccion=:Cli_direccion, supervisor=:Supervisor, actualizacion=:Actualizacion;");
      $params = array(':Fecha'=>$informe->fecha, ':CliContacto'=>$informe->clicontacto, ':Cli_direccion'=>$informe->cli_direccion, ':Supervisor'=>$informe->supervisor, ':Actualizacion'=>$informe->actualizacion);
      if($stmt->execute($params)){
          $res=true;
      }
      return $res;
    } catch (PDOException $e) {
      throw new Exception($e->getMessage());
    }
  }

  function FnModificarInformeDatosGenerales($conmy, $informe) {
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

  function FnModificarInformeDatosEquipo($conmy, $informe) {
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

  /**
   * TABLA : tbldetalleinforme
   */
  function FnBuscarDetalleInformeActividad($conmy, $id) {
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

  function FnBuscarDetalleInformeActividades($conmy, $infid) {
    try {
      $stmt = $conmy->prepare("SELECT id, ownid, tipo, actividad, diagnostico, trabajos, observaciones, estado FROM tbldetalleinforme WHERE infid = :Infid;");
      $stmt->execute(array(':Infid' => $infid));
      $actividades = $stmt ->fetchAll(PDO::FETCH_ASSOC);;
      return $actividades;
    } catch (PDOException $ex) {
      throw new Exception($ex->getMessage());
    }
  }

  function FnRegistrarDetalleInformeTipoActividad($conmy, $actividad) {
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

  function FnModificarDetalleInformeActividad($conmy, $actividad) {
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

  function FnEliminarDetalleInformeActividad($conmy, $id) {
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

  /**
   * TABLA : tblarchivos
   */
  function FnBuscarArchivoTituloDescripcion($conmy, $id) {
    try {
      $stmt = $conmy->prepare("SELECT id, titulo, descripcion, nombre FROM tblarchivos WHERE id = :Id");
      $stmt->execute(array(':Id' => $id));
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
      if ($row) {
        $archivo = new stdClass();
        $archivo->id = $row['id'];
        $archivo->titulo = $row['titulo'];
        $archivo->descripcion = $row['descripcion'];
        $archivo->nombre = $row['nombre'];
        return $archivo;
      }
      return null;
    } catch (PDOException $e) {
      throw new Exception($e->getMessage());
    }
  }


  function FnBuscarArchivos($conmy, $id) {
    try {
      $stmt = $conmy->prepare("SELECT id, refid, tabla, nombre, descripcion, tipo, titulo FROM tblarchivos WHERE refid=:Id");
      $stmt->execute(array(':Id' => $id));
      $archivos = $stmt ->fetchAll(PDO::FETCH_ASSOC);
      return $archivos;
    } catch (PDOException $ex) {
      throw new Exception($ex->getMessage());
    }
  }

  function FnRegistrarArchivo($conmy, $imagen) {
    try {
      $stmt = $conmy->prepare("INSERT INTO tblarchivos (refid, tabla, nombre, titulo, descripcion, tipo) VALUES (:RefId, :Tabla, :Nombre, :Titulo, :Descripcion, :Tipo)");
      $params = array(
        ':RefId' => $imagen->refid,
        ':Tabla' => $imagen->tabla,
        ':Nombre' => $imagen->nombre,
        ':Titulo' => $imagen->titulo,
        ':Descripcion' => $imagen->descripcion,
        ':Tipo' => $imagen->tipo
      );
      $stmt->execute($params);
      return $stmt;
    } catch (PDOException $ex) {
      throw new Exception($ex->getMessage());
    }
  }

  function FnModificarArchivoImagenTituloDescripcion($conmy, $archivo) {
    try {
      $query = "UPDATE tblarchivos SET descripcion = :Descripcion, titulo = :Titulo";
      if (!empty($archivo->nombre)) {
        $query.=", nombre = :Nombre";
      }
      $query.=" WHERE id = :Id";
      $stmt = $conmy->prepare($query);
      $params = array(
        ':Descripcion' => $archivo->Descripcion,
        ':Titulo' => $archivo->Titulo,
        ':Id' => $archivo->Id,
      );
      // AGREGAR NUEVO NOMBRE
      if (!empty($archivo->nombre)) {
        $params[':Nombre'] = $archivo->nombre;
      }
      // EJECUTAR CONSULTA
      $result = $stmt->execute($params);
      if ($stmt->rowCount() == 0) {
        throw new Exception('Cambios no realizados.');
      }
      return $result;
    } catch (PDOException $e) {
      throw new Exception($e->getMessage());
    }
  }

  function FnModificarArchivoAnexoTituloDescripcion($conmy, $archivo) {
    try {
      $query = "UPDATE tblarchivos SET descripcion = :Descripcion, titulo = :Titulo";
      if (!empty($archivo->nombre)) {
        $query.=", nombre = :Nombre";
      }
      $query.=" WHERE id = :Id";
      $stmt = $conmy->prepare($query);
      $params = array(
        ':Descripcion' => $archivo->Descripcion,
        ':Titulo' => $archivo->Titulo,
        ':Id' => $archivo->Id,
      );
      // AGREGAR NUEVO NOMBRE
      if (!empty($archivo->nombre)) {
        $params[':Nombre'] = $archivo->nombre;
      }
      // EJECUTAR CONSULTA
      $result = $stmt->execute($params);
      if ($stmt->rowCount() == 0) {
        throw new Exception('Cambios no realizados.');
      }
      return $result;
    } catch (PDOException $e) {
      throw new Exception($e->getMessage());
    }
  }

  function FnEliminarArchivo($conmy, $id) {
    try {
      $res = false;
      $stmt = $conmy->prepare("DELETE FROM tblarchivos WHERE id =:Id");
      $params = array(':Id' => $id);
      if ($stmt->execute($params)) {
          $res = true;
      }
      return $res;
    } catch (PDOException $e) {
      throw new Exception($e->getMessage());
    }
  }
?>