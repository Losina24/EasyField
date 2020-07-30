<?php
	// Conexión a la base de datos

	$idSonda1 = $_GET['idsonda'];

	include ("../api/includes/conexion.php");
?>
<!DOCTYPE html>
<html lang="es" >

<head>
<link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon">
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<link href="https://fonts.googleapis.com/css?family=Montserrat:300,400,600|Open+Sans" rel="stylesheet">
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css">
<title>Mediciones de la Sonda <?php print_r($idSonda1)?></title>
<!-- Estilos -->
<link href="css/mediciones.css" rel="stylesheet">
<link href="css/all.min.css" rel="stylesheet">
<link href="fonts/fonts.css" rel="stylesheet">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
</head>

<body>
	<div class="cuerpo-pagina">

		<!-- HEADER -->
		<header class="encabezado">
			<a href="index.php" class="enlace-imagen"><img class="logotipo-encabezado" src="images/Logotype.png" alt="Logotipo de la empresa"></a>
			<a class="boton-cerrar-sesion"href="javascript: fetch ('../api/v1.0/sesion',{method:'delete'}).then( function(respuesta) { if(respuesta.status== 200) location .href= '..'; })" >Cerrar Sesión</a>

		</header>
		<!-- CONTENIDOS -->
		<section class="seccion-principal">

			<!-- Menu de navegación de cada sonda -->
			<nav class="menu-sonda">
				<a href="usuario.php" class="volver">< Volver</a>

						<h1 class="titulo-mediciones" id="titulo-Sonda">Sonda <?php print_r($idSonda1); ?></h1>

						<div class="iconos">

							<!-- Menú comparativa de sondas -->
							<div class="dropdown">

								<button onclick="myFunction2()" class="dropbtn">Comparar</button>
								<div id="myDropdown2" class="dropdown-content">
										<button id="bSonda" class="boton-reinicio" onclick="tipoGrafica()"><img src="../app/images/reinicio.png" alt="reinicio"></button>
										<!--Tabla con las sondas a comparar-->
										<table id="table">
							<!--PHP con el que selecciono las sondas de la parcela en la que estamos-->
							<?php
										   include ("../api/includes/conexion.php");

										   //Selecciono las posiciones que tiene esas parcelas
										   $sql="SELECT id_posicion FROM posicion WHERE idSondas = '$idSonda1'";
										   $result=mysqli_query($conexion, $sql);

											while($row = mysqli_fetch_assoc($result)){

												//Selecciono las sondas que tiene la parcela
												$rowf = $row['id_posicion'];
												$sqlQuery = "SELECT idSondas FROM posicion WHERE id_posicion = $rowf";
												$res=mysqli_query($conexion, $sqlQuery);
												while($mostrar=mysqli_fetch_array($res)){

													if($mostrar['idSondas'] != $idSonda1){

										?>
							<!--Muestro las sondas obtenidas en las celdas de la tabla-->
							<tr class="lista-sondas">

								<td class="elementos-lista">

									<button class="boton-lista" onclick="tipoGraficaComparar(<?php print_r($mostrar['idSondas']);?>)">

										<?php print_r("Sonda "); print_r($mostrar['idSondas'])?>

									</button>

								</td>

							</tr>
							<?php
													}
												}
											}
												 ?>
						</table>
								</div>
							</div>

							<div class="dropdown">

							  <button onclick="myFunction()" class="dropbtn">Calendario</button>
							  <div id="myDropdown" class="dropdown-content">
								<a href="#" id="bD">Diaria</a>
								<a href="#" id="bS">Semanal</a>
								<a href="#" id="bM">Mensual</a>
								<a href="#" id="bA">Anual</a>
							  </div>

							</div>
						</div>
			</nav>

			<!-- GRÁFICAS -->
			<!-- Humedad -->
			<article class="graficas-superior">

				<div class="grafica" id="humedad">
					<h2 class="titulo-grafica"><i class="far fa-tint icono-med"></i> Humedad</h2>
					<div class="contenedor-grafica">
						<canvas id="graphCanvasH"></canvas>
					</div>
				</div>

				<!-- Salinidad -->
				<div class="grafica" id="salinidad">
					<h2 class="titulo-grafica"><i class="far fa-mountains icono-med"></i> Salinidad</h2>
					<div class="contenedor-grafica">
						<canvas id="graphCanvasS"></canvas>
					</div>
				</div>
			</article>
			<article class="graficas-inferior">
				<!-- Temperatura -->
				<div class="grafica" id="temperatura">
					<h2 class="titulo-grafica"><i class="far fa-thermometer-half icono-med"></i> Temperatura</h2>
					<div class="contenedor-grafica">
						<canvas id="graphCanvasT"></canvas>
					</div>
				</div>

				<!-- Luminosidad -->
				<div class="grafica" id="luminosidad">
					<h2 class="titulo-grafica"><i class="far fa-sun icono-med"></i> Luminosidad</h2>
					<div class="contenedor-grafica">
						<canvas id="graphCanvasL"></canvas>
					</div>
				</div>
			</article>
		</section>

		<!--Menú desplegable-->
		<div class="overlay" id="overlay">
			<!--Contenido del menú-->
			<div class="popup" id="popup">
				<!--Botón cerrar el menú-->
				<div class="form">
					<a href="#" id="btn-cerrar-popup" class="btn-cerrar-popup"><i class="fas fa-times"></i></a>
				</div>
			</div>
		</div>

		<!-- SCRIPTS -->
		<script src="js/jquery.min.js"></script> <!-- jQuery -->
		<script src="js/iconos.js"></script> <!-- Este script contiene los iconos que se están usando en la página -->
		<script src="js/session.js"></script> <!-- Este script hace que si el usuario no este logueado lo mande a la pagina de login automaticamente-->
		<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.bundle.min.js"></script>
		<script>

		/* When the user clicks on the button,
		toggle between hiding and showing the dropdown content */
		function myFunction() {
		  document.getElementById("myDropdown").classList.toggle("show");
		}

		// Close the dropdown menu if the user clicks outside of it
		window.onclick = function(event) {
		  if (!event.target.matches('.dropbtn')) {
			var dropdowns = document.getElementsByClassName("dropdown-content");
			var i;
			for (i = 0; i < dropdowns.length; i++) {
			  var openDropdown = dropdowns[i];
			  if (openDropdown.classList.contains('show2')) {
				openDropdown.classList.remove('show2');
			  }
			}
		  }
		}

		function myFunction2() {
		  document.getElementById("myDropdown2").classList.toggle("show2");
		}

		</script>
		<script src="js/ScriptGrafica.js"></script> <!-- JS con las funciones encargadas de la construcción de las estadísticas -->
		<script>

			iniciarEstadisticas();

			// queLabel() --> R
			//Muestra la id de la sonda en el label de sus estadísticas
			function queLabel(){

				return <?php print_r($idSonda1); ?>
			}

			// R, [JSON], R --> iniciarestadisticas() --> RecibirDatosSonda(R, R,[JSON] ,R)
			//                                        --> tipoGrafica(R)
			//Inicia la construcción de las estadísticas dependiendo de si se ha decidido comparar o no
			function iniciarEstadisticas(tipo, mediciones2, labelSonda2){

				//Si existe mediciones2, se construirán las estadísticas comparables
				if(mediciones2){

					RecibirDatosSonda(tipo, <?php print_r($idSonda1); ?>, mediciones2, labelSonda2);
				}else{

					tipoGrafica(<?php print_r($idSonda1); ?>);
				}
			}

		</script> <!-- JS encargado de las llamadas a las funciones encargadas de la construcción de las estadísticas -->
		<script>
			var btnAbrirPopup = document.getElementById("btn-abrir-popup"),
				overlay = document.getElementById("overlay"),
				popup = document.getElementById("popup"),
				btnCerrarPopup = document.getElementById("btn-cerrar-popup");

			btnAbrirPopup.on('click', function() {
				overlay.classList.add('active');
				popup.classList.add('active');
			});

			btnCerrarPopup.on('click', function() {
				overlay.classList.remove('active');
				popup.classList.remove('active');
			});

		</script> <!-- animaciones-->
</body>

</html>
