<?php

    /** @var $message \Fetch\Message */

	foreach ($messages as $message) {

	echo"<pre>";
    print_r($message);
    echo "</pre>";//exit();

	}

	exit();
?>


<html>
<head><title>Ejemplo de tabla sencilla</title></head>
<body>
 
<h1>Listado de cursos</h1>
 
<table width="1200px" border="2px"> 
<?php
	
	foreach ($messages as $key => $message) {
		# code...
		
		$result = $message->getOverview(); 

		// echo "<pre>";
		// print_r($result);
		// echo "</pre>";exit();
 ?>

<tr>
  <td><?php echo $result->from ?> </td>
  <td>
  <?php 

  echo isset($result->subject) ? $result->subject : "(sin asunto)";
  echo isset($message->getMessageBody()) ? $message->getMessageBody() : "";

  ?>
  </td>
  <td><?php echo $result->date ?> </td>
  <td>Eliminar</td>
</tr>

<?php 
}
?>
 
</table>
 
</body>
</html>