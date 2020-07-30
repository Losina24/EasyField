//R --> procesarDatos() --> RecibirDatosSonda(R, R)
//Llamada a la función RecibirDatosSonda dependiendo de la opción seleccionada
function tipoGrafica(idSonda) {

	//Si no se pulsa nada, la gráfica mostrada siempre será la horaria. Es decir: 1
	RecibirDatosSonda(1, idSonda);

	//Aquí están las distintas opciones de gráfica dependiendo del botón pulsado
	//Llamadas a la función datosTabla()
	document.getElementById("bD").onclick = function () {
		RecibirDatosSonda(1, idSonda); //Diaria
	}
	document.getElementById("bS").onclick = function () {
		RecibirDatosSonda(2, idSonda); //Semanal
	}
	document.getElementById("bM").onclick = function () {
		RecibirDatosSonda(3, idSonda); //Mensual
	}
	document.getElementById("bA").onclick = function () {
		RecibirDatosSonda(4, idSonda); //Anual
	}
}

//R --> procesarDatos() --> RecibirDatosSondaComparar(R, R)
//Llamada a la función RecibirDatosSondaComparar dependiendo de la opción seleccionada
//Solo se activará si el modo de gráficas es el de comparar
function tipoGraficaComparar(idSonda){

	//Si no se pulsa nada, la gráfica mostrada siempre será la horaria. Es decir: 1
	RecibirDatosSondaComparar(1, idSonda);

	//Aquí están las distintas opciones de gráfica dependiendo del botón pulsado
	//Llamadas a la función datosTabla()
	document.getElementById("bD").onclick = function () {
		RecibirDatosSondaComparar(1, idSonda); //Diaria
	}
	document.getElementById("bS").onclick = function () {
		RecibirDatosSondaComparar(2, idSonda); //Semanal
	}
	document.getElementById("bM").onclick = function () {
		RecibirDatosSondaComparar(3, idSonda); //Mensual
	}
	document.getElementById("bA").onclick = function () {
		RecibirDatosSondaComparar(4, idSonda); //Anual
	}
	document.getElementById("bSonda").onclick = function () {
		iniciarEstadisticas();
	}
}

//R, R, [JSON], R --> procesarDatosSonda() -->procesarDatos([JSON], [JSON], R, R)
//Ajax que recibe un json de un php (get-medicionesPruebas.php) mediante el idSonda(R)
//   recibido. Luego llama a procesarDatos
function RecibirDatosSonda(tipo, idSonda, mediciones2, labelSonda2){

	$.ajax({
		type:'POST',
		url:'../api/v1.0/modelos/get-mediciones.php',
		dataType: "json",
		data:{idSonda: idSonda},
		success:function(data){

			var mediciones1 = data;
			procesarDatos(mediciones1, mediciones2, tipo, labelSonda2);
		}
	});
}

//R, R, [JSON], R --> procesarDatosSondaComparar() -->procesarDatos([JSON], [JSON], R, R)
//Ajax que recibe un json de un php (get-medicionesPruebas.php) mediante el idSonda(R)
//   recibido. Luego llama a iniciarEstadisticas (se encuentra en el mediciones.php)
function RecibirDatosSondaComparar(tipo, idSonda) {

	$.ajax({
		type:'POST',
		url:'../api/v1.0/modelos/get-mediciones.php',
		dataType: "json",
		data:{idSonda: idSonda},
		success:function(data){

			var mediciones2 = data;
			iniciarEstadisticas(tipo, mediciones2, idSonda);
		}
	});
}

//[jSon], [jSon], R --> procesarDatos() --> tipologiaGrafica([jSon], [X], [X], [X], [X], [X], R), Tiks(R), generarGrafica([X], [X], [X], [jSon], X)
//Procesar los datos de los jSon recibidos
function procesarDatos(mediciones1, mediciones2, tipo, labelSonda2) {

	var labelSondaComp;

	if(labelSonda2) labelSondaComp = labelSonda2;;

	//Arrays con datos del jSon
	var medidaT = [];
	var medidaS = [];
	var medidaH = [];
	var medidaL = [];
	var fecha = [];

	//Arrays con datos del jSon para la comparativa
	var medidaT2 = [];
	var medidaS2 = [];
	var medidaH2 = [];
	var medidaL2 = [];
	var fecha2 = [];

    //Si se quiere comparar mediciones (si existen datos procesados de una segunda sonda)...
	if (mediciones2) {

		//Llamada a tipologiaGrafica()
		tipologiaGrafica(mediciones1, medidaT, medidaS, medidaH, medidaL, fecha, tipo);
		tipologiaGrafica(mediciones2, medidaT2, medidaS2, medidaH2, medidaL2, fecha2, tipo);

    //Si no se va a comparar...
	} else {

		tipologiaGrafica(mediciones1, medidaT, medidaS, medidaH, medidaL, fecha, tipo);
	}

    //Dependiendo del tipo, se mostrarán más o menos datos en la gráfica
	//Llamada a Tiks()
	var cantidad_tiks = Tiks(tipo);

	//Opciones que son comunes de cada tabla
	var opciones = {

		//Quitar leyenda
		legend: {

			position: 'bottom',
			fillStyle: "rgb(255, 0, 0)",
			labels: {

				defaultFontStyle: 'normal',
				defaultFontSize: '2',
			}
		},

		scales: {

			yAxes: [{

				gridLines: {

					//eliminar las lineas verticales de fondo
					color: "rgb(232, 232, 232)"
				},

				ticks: {

					fontColor: "rgba(0,0,0,0.5)",
					fontStyle: "bold",
					//El grafico empezara siempre a cero
					beginAtZero: true,
					//El maximo sera siempre 100
					max: 100,
					maxTicksLimit: 10,
					fontSize: '10'
				}
					  }],

			xAxes: [{

				gridLines: {

					//cambiar color de las lineas horizontales del fondo
					display: false
				},
				ticks: {
					padding: 5,
					fontColor: "rgba(0,0,0,0.5)",
					maxTicksLimit: cantidad_tiks,
					minTicksLimit: cantidad_tiks,
					fontStyle: "bold",
					fontSize: '11'
				}
				}]
		}
	};

    //Llamadas a generarGraficas:

	//Gráfica humedad
	var ctx = document.getElementById('graphCanvasH').getContext('2d');

	generarGrafica(medidaH, medidaH2, fecha, opciones, ctx, labelSondaComp);


	//Gráfica luminosidad
	var ctx = document.getElementById('graphCanvasL').getContext('2d');

	generarGrafica(medidaL, medidaL2, fecha, opciones, ctx, labelSondaComp);

	//Gráfica salinidad
	var ctx = document.getElementById('graphCanvasS').getContext('2d');

	generarGrafica(medidaS, medidaS2, fecha, opciones, ctx, labelSondaComp);

	//Gráfica temperatura
	var ctx = document.getElementById('graphCanvasT').getContext('2d');

	generarGrafica(medidaT, medidaT2, fecha, opciones, ctx, labelSondaComp);

}

//[X], [X], [X], [jSon], X --> generarGrafica --> pavaraGraf([X], X, X)
//Generación de las gráficas mediante los daotos recibidos
function generarGrafica(medida1, medida2, fecha, opciones, ctx, labelSondaComp) {

  //Creación de dos tipos de gradiantes distintos (rojo, azul)

	//Rojo
	var gradianteRojo = ctx.createLinearGradient(0, 0, 0, 275);
	gradianteRojo.addColorStop(0, "#F45B49");
	gradianteRojo.addColorStop(1, "#F0F0F0");

	//Azul
	var gradianteAzul = ctx.createLinearGradient(0, 0, 0, 275);
	gradianteAzul.addColorStop(0, "#49E1F4");
	gradianteAzul.addColorStop(1, "#F0F0F0");

	//Dependiendo de las medidas la gráfica tendrá un color u otro
	//Llamada a pavaraGraf
	var estado1 = pavaraGraf(medida1, gradianteAzul, gradianteRojo);
	var estado2 = pavaraGraf(medida2, gradianteAzul, gradianteRojo);

	var bordeColor;

	if(estado1 == gradianteRojo){

		bordeColor = "#F45B49";
	} else {

		bordeColor = "#49E1F4";
	}

	var label = queLabel();

	//Si medida2 existe, hay comparativa: Se crean dos tipos de mediciones
	if (medida2 != 0) {

		var char = {

			labels: fecha,
			datasets: [{

					label: 'Sonda ' +label+ ' (%)',

					pointRadius: 0,
					pointHitRadius: 5,
					data: medida1,
					backgroundColor: "rgba(0, 0, 0, 0)",
					borderColor: "#49E1F4",
					borderWidth: 2.5,
					lineTension: 0.1,

				},

				{

					label: 'Sonda ' +labelSondaComp+ ' (%)',
					pointRadius: 0,
					pointHitRadius: 5,
					data: medida2,
					backgroundColor: "rgba(0, 0, 0, 0)",
					borderColor: "rgba(165, 105, 189)",
					borderWidth: 2.5,
					lineTension: 0.1,

				}
			]
		};
    //Si no, solo una
	} else {

		var char = {

			labels: fecha,
			datasets: [{

				label: 'Sonda ' +label+ ' (%)',
				hoverBackgroundColor: estado1,
				pointRadius: 0,
				pointHitRadius: 5,
				data: medida1,
				backgroundColor: estado1,
				borderColor: bordeColor,
				borderWidth: 1,
				lineTension: 0.1,

				}]
		};
	}

	//Si ya hay gráficas creadas a la hora de crear una, estas se autoeliminan
	if (ctx.grafica) {

		ctx.grafica.clear();
		ctx.grafica.destroy();
	}

	//Creamos la gráfica con los datos creados anteriormente
	ctx.grafica = new Chart(ctx, {
		type: 'line',
		data: char,
		options: opciones
	});
}

//[X], X, X --> pavaraGraf() --> X
//Dependiendo de las medidas recibidas, la función devuelve un tipo de gradiante u otro
function pavaraGraf(cadena, azul, rojo) {

	var mallor = 0;
	var menor = 100;

	for (var i = 0; i < cadena.length; i++) {

		if (cadena[i] > mallor) {

			mallor = cadena[i];
		} else if (cadena[i] < menor) {

			menor = cadena[i];
		}
	}

	//Si el mallor número almacendao es mallor que 75...
	if (mallor > 75) {

		//Devuelve un gradiante rojo
		return rojo;

	//Si no, si el dato más pequeño es inferior a 20...
	} else if (menor < 20) {

		//También se devuelve rojo
		return rojo;
	}
    //Si no, se devuelve azul
	return azul;
}

//[jSon], [X], [X], [X], [X], [X], R --> tipologiaGrafica()
//Almacena de forma ordenada en las array recibidas los datos almacenados en el jSon (también recibido). depndiendo del tipo.
function tipologiaGrafica(mediciones, medidaT, medidaS, medidaH, medidaL, fecha, tipo) {

 //Dependiendo del tipo recibido se almacenarán de una forma u otra

	if (tipo == 1) {

		//Dependiendo de la hora del día de la última medición, se mostrará más o menos datos
		var hasta = 0;

		if(mediciones[mediciones.length -1].fecha[11] != 0){

			hasta = mediciones[mediciones.length -1].fecha[11] + mediciones[mediciones.length -1].fecha[12];
			hasta++;
		} else {

			hasta = mediciones[mediciones.length -1].fecha[12];
			hasta++;
		}

		for (var i = 0; i < hasta; i++) {

			medidaT.push(mediciones[i].medida_temperatura);
			medidaS.push(mediciones[i].medida_salinidad);
			medidaH.push(mediciones[i].medida_humedad);
			medidaL.push(mediciones[i].medida_luminosidad);
			//Solo se mostrará las horas y los minutos
			fecha.push(mediciones[i].fecha[10] + mediciones[i].fecha[11] + mediciones[i].fecha[12] + mediciones[i].fecha[13] + mediciones[i].fecha[14] + mediciones[i].fecha[15]);
		}

	}

	if (tipo == 2) {

		var mediaT = 0;
		var mediaS = 0;
		var mediaH = 0;
		var mediaL = 0;
		var posicion = 1;
		var hasta = 0;

		if(mediciones[mediciones.length -1].fecha[11] != 0){

			hasta = mediciones[mediciones.length -1].fecha[11] + mediciones[mediciones.length -1].fecha[12];
			hasta++;

		} else {

			hasta = mediciones[mediciones.length -1].fecha[12];
			hasta++;
		}

		for (var i = 0; i < hasta; i++) {

			mediaT = parseFloat(mediciones[i].medida_temperatura) + parseFloat(mediaT);
			mediaS = parseFloat(mediciones[i].medida_salinidad) + parseFloat(mediaS);
			mediaH = parseFloat(mediciones[i].medida_humedad) + parseFloat(mediaH);
			mediaL = parseFloat(mediciones[i].medida_luminosidad) + parseFloat(mediaL);
		}

			mediaT = mediaT / hasta;
			medidaT.push(mediaT);
			mediaS = mediaS / hasta;
			medidaS.push(mediaS);
			mediaH = mediaH / hasta;
			medidaH.push(mediaH);
			mediaL = mediaL / hasta;
			medidaL.push(mediaL);
			fecha.push(mediciones[posicion -1].fecha[0] + mediciones[posicion -1].fecha[1] + mediciones[posicion -1].fecha[2] + mediciones[posicion -1].fecha[3] + mediciones[posicion -1].fecha[4] + mediciones[posicion -1].fecha[5] + mediciones[posicion -1].fecha[6] + mediciones[posicion -1].fecha[7] + mediciones[posicion -1].fecha[8] + mediciones[posicion -1].fecha[9]);



		for (var i = 1; i < 7; i++) {

			for (var j = 0; j < 24; j++) {

				mediaT = parseFloat(mediciones[j + posicion].medida_temperatura) + parseFloat(mediaT);
				mediaS = parseFloat(mediciones[j + posicion].medida_salinidad) + parseFloat(mediaS);
				mediaH = parseFloat(mediciones[j + posicion].medida_humedad) + parseFloat(mediaH);
				mediaL = parseFloat(mediciones[j + posicion].medida_luminosidad) + parseFloat(mediaL);
			}

			//Aquí se realizarán las medias (cada 24 son un día)
			mediaT = mediaT / 24;
			medidaT.push(mediaT);
			mediaS = mediaS / 24;
			medidaS.push(mediaS);
			mediaH = mediaH / 24;
			medidaH.push(mediaH);
			mediaL = mediaL / 24;
			medidaL.push(mediaL);
			fecha.push(mediciones[posicion].fecha[0] + mediciones[posicion -1].fecha[1] + mediciones[posicion -1].fecha[2] + mediciones[posicion -1].fecha[3] + mediciones[posicion -1].fecha[4] + mediciones[posicion -1].fecha[5] + mediciones[posicion -1].fecha[6] + mediciones[posicion -1].fecha[7] + mediciones[posicion -1].fecha[8] + mediciones[posicion -1].fecha[9]);
			posicion = posicion + 25;
		}
	}

	if (tipo == 3) {

		var mediaT = 0;
		var mediaS = 0;
		var mediaH = 0;
		var mediaL = 0;
		var mediaT2 = 0;
		var mediaS2 = 0;
		var mediaH2 = 0;
		var mediaL2 = 0;
		var posicion = 0;
		var posicion2 = 1;

		for (var i = 0; i < 4; i++) {

			for (var x = 0; x < 7; x++) {

				for (var j = 0; j < 24; j++) {

					mediaT = parseFloat(mediciones[j + posicion].medida_temperatura) + parseFloat(mediaT);
					mediaS = parseFloat(mediciones[j + posicion].medida_salinidad) + parseFloat(mediaS);
					mediaH = parseFloat(mediciones[j + posicion].medida_humedad) + parseFloat(mediaH);
					mediaL = parseFloat(mediciones[j + posicion].medida_luminosidad) + parseFloat(mediaL);
				}

				mediaT = mediaT / 24;
				mediaT2 = mediaT + mediaT2;
				mediaS = mediaS / 24;
				mediaS2 = mediaS + mediaS2;
				mediaH = mediaH / 24;
				mediaH2 = mediaH + mediaH2;
				mediaL = mediaL / 24;
				mediaL2 = mediaL + mediaL2;
				posicion = posicion + 24;
			}

			var mediaT2 = mediaT2 / 7;
			medidaT.push(mediaT2);
			var mediaS2 = mediaS2 / 7;
			medidaS.push(mediaS2);
			var mediaH2 = mediaH2 / 7;
			medidaH.push(mediaH2);
			var mediaL2 = mediaL2 / 7;
			medidaL.push(mediaL2);
			fecha.push(mediciones[posicion2].fecha[8] + mediciones[posicion2].fecha[9] + "-" + mediciones[posicion2 + 169].fecha[8] + mediciones[posicion2 + 169].fecha[9]);
			posicion2 = posicion2 + 166;
		}
	}

	if (tipo == 4) {

		var mediaT = 0;
		var mediaS = 0;
		var mediaH = 0;
		var mediaL = 0;
		var mediaT2 = 0;
		var mediaS2 = 0;
		var mediaH2 = 0;
		var mediaL2 = 0;
		var mediaT3 = 0;
		var mediaS3 = 0;
		var mediaH3 = 0;
		var mediaL3 = 0;
		var posicion = 0;
		var posicion2 = 0;
		for (var i = 0; i < 12; i++) {

			for (var z = 0; z < 4; z++) {

				for (var x = 0; x < 7; x++) {

					for (var j = 0; j < 24; j++) {

						mediaT = parseFloat(mediciones[j + posicion].medida_temperatura) + parseFloat(mediaT);
						mediaS = parseFloat(mediciones[j + posicion].medida_salinidad) + parseFloat(mediaS);
						mediaH = parseFloat(mediciones[j + posicion].medida_humedad) + parseFloat(mediaH);
						mediaL = parseFloat(mediciones[j + posicion].medida_luminosidad) + parseFloat(mediaL);
					}

					mediaT = mediaT / 24;
					mediaT2 = mediaT + mediaT2;
					mediaS = mediaS / 24;
					mediaS2 = mediaS + mediaS2;
					mediaH = mediaH / 24;
					mediaH2 = mediaH + mediaH2;
					mediaL = mediaL / 24;
					mediaL2 = mediaL + mediaL2;
					posicion = posicion + 24;
				}

				var mediaT2 = mediaT2 / 7;
				mediaT3 = mediaT2 + mediaT3;
				var mediaS2 = mediaS2 / 7;
				mediaS3 = mediaS2 + mediaS3;
				var mediaH2 = mediaH2 / 7;
				mediaH3 = mediaH2 + mediaH3;
				var mediaL2 = mediaL2 / 7;
				mediaL3 = mediaL2 + mediaL3;


			}

			var mediaT3 = mediaT3 / 4;
			medidaT.push(mediaT2);
			var mediaS3 = mediaS3 / 4;
			medidaS.push(mediaS2);
			var mediaH3 = mediaH3 / 4;
			medidaH.push(mediaH2);
			var mediaL3 = mediaL3 / 4;
			medidaL.push(mediaL2);

			if ((mediciones[posicion2].fecha[5] + mediciones[posicion2].fecha[6]) == 01) fecha.push("Enero");
			if ((mediciones[posicion2].fecha[5] + mediciones[posicion2].fecha[6]) == 02) fecha.push("Febrero");
			if ((mediciones[posicion2].fecha[5] + mediciones[posicion2].fecha[6]) == 03) fecha.push("Marzo");
			if ((mediciones[posicion2].fecha[5] + mediciones[posicion2].fecha[6]) == 04) fecha.push("Abril");
			if ((mediciones[posicion2].fecha[5] + mediciones[posicion2].fecha[6]) == 05) fecha.push("Mayo");
			if ((mediciones[posicion2].fecha[5] + mediciones[posicion2].fecha[6]) == 06) fecha.push("Junio");
			if ((mediciones[posicion2].fecha[5] + mediciones[posicion2].fecha[6]) == 07) fecha.push("Julio");
			if ((mediciones[posicion2].fecha[5] + mediciones[posicion2].fecha[6]) == 08) fecha.push("Agosto");
			if ((mediciones[posicion2].fecha[5] + mediciones[posicion2].fecha[6]) == 09) fecha.push("Septiembre");
			if ((mediciones[posicion2].fecha[5] + mediciones[posicion2].fecha[6]) == 10) fecha.push("Octubre");
			if ((mediciones[posicion2].fecha[5] + mediciones[posicion2].fecha[6]) == 11) fecha.push("Noviembre");
			if ((mediciones[posicion2].fecha[5] + mediciones[posicion2].fecha[6]) == 12) fecha.push("Diciembre");

			posicion2 = posicion2 + 672;

		}
	}
}

// R --> Tiks() --> R
//Dependiendo del número recibido, la función devolverá uno u otro
function Tiks(tipo) {

	if (tipo == 1) return 8;
	if (tipo == 2) return 7;
	if (tipo == 3) return 4;
	if (tipo == 4) return 12;
}
