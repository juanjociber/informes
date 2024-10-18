<?php 
  function FnBuscarSupervisores($comy) {
    try {
      $stmt = $comy->prepare("SELECT idsupervisor, idcliente, supervisor FROM cli_supervisores WHERE idcliente = 1");
      $stmt->execute(); 
      $supervisores = $stmt->fetchAll(PDO::FETCH_ASSOC);
      return $supervisores;
    } catch (PDOException $e) {
      throw new Exception($e->getMessage());
    }
  }
    
  function FnBuscarContacto($comy, $id) {
    try {
      $stmt = $comy->prepare("SELECT idsupervisor, idcliente, supervisor FROM cli_supervisores WHERE idcliente=:Id");
      $stmt->execute(array(':Id'=>$id));
      $supervisores = $stmt->fetchAll(PDO::FETCH_ASSOC);
      return $supervisores;
    } catch (PDOException $e) {
      throw new Exception($e->getMessage());
    }
  }
?>