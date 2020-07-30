<?php

// INCLUDE PHPMAILER //
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require (realpath(dirname(__FILE__)).'/../../includes/PHPMailer/src/Exception.php');
require (realpath(dirname(__FILE__)).'/../../includes/PHPMailer/src/PHPMailer.php');
require (realpath(dirname(__FILE__)).'/../../includes/PHPMailer/src/SMTP.php');

// CONEXIÓN A LA BASE DE DATOS //

$bbdd_server = 'localhost';
$bbdd_user = 'root';
$bbdd_password = '';
$bbdd = 'ycansam_easyfield';

$conexion = mysqli_connect($bbdd_server,$bbdd_user,$bbdd_password,$bbdd);
mysqli_query($conexion,'SET NAMES utf8.1');


// SENTENCIAS SQL //

$diezMinutos = [];
$usuario = [];

$query = mysqli_query($conexion, "SELECT * FROM mediciones WHERE fecha >= NOW() - INTERVAL 20 MINUTE");
while($row = mysqli_fetch_assoc($query)){
    $diezMinutos[] = $row;
}

for($i = 0; $i < count($diezMinutos); $i++){
    $query_posicion = mysqli_query($conexion, 'SELECT id_posicion, idSondas FROM posicion WHERE id = '.$diezMinutos[$i]["idPosicion"].'');
    $idPosicion = mysqli_fetch_assoc($query_posicion);
    print_r($idPosicion);
    $idParcelas = mysqli_fetch_assoc(mysqli_query($conexion, 'SELECT id_parcelas, Nombre FROM parcelas WHERE idPosicion = '.$idPosicion["id_posicion"].''));
    $coordenadas = mysqli_fetch_assoc(mysqli_query($conexion, 'SELECT * FROM sondas WHERE id_sondas = '.$idPosicion["idSondas"].''));
    $idUsuarios = [];
    while($idUsuario = mysqli_fetch_assoc(mysqli_query($conexion, 'SELECT idUsuarios FROM asignacionusparc WHERE idParcelas = '.$idParcelas["id_parcelas"].''))){
        array_push($idUsuarios, $idUsuario);
    }

    print_r($idPosicion["idSondas"]);
    $correo = "";

    if($diezMinutos[$i]['medida_salinidad'] < 20.00 or $diezMinutos[$i]['medida_salinidad'] > 80.00){
        $correo .= "La salinidad de la plantacion '".$idParcelas['Nombre']."' en las coordenadas (".$coordenadas['latitud_sonda'].", ".$coordenadas['longitud_sonda'].") es del: ".$diezMinutos[$i]['medida_salinidad']."%. <br><br>";
    }

    if($diezMinutos[$i]['medida_temperatura'] < 3.00 or $diezMinutos[$i]['medida_temperatura'] > 30.00){
        $correo .= "La temperatura de la plantacion '".$idParcelas['Nombre']."' en las coordenadas (".$coordenadas['latitud_sonda'].", ".$coordenadas['longitud_sonda'].") es de: ".$diezMinutos[$i]['medida_temperatura']."ºC. <br><br>";
    }

    if($diezMinutos[$i]['medida_humedad'] < 20.00 or $diezMinutos[$i]['medida_humedad'] > 80.00){
        $correo .= "La humedad de la plantacion '".$idParcelas['Nombre']."' en las coordenadas (".$coordenadas['latitud_sonda'].", ".$coordenadas['longitud_sonda'].") es del: ".$diezMinutos[$i]['medida_temperatura']."%. <br><br>";
    }

    if($correo != ""){

        for ($x=0; $x < count($idUsuarios); $x++) {
            $myIdUser = $idUsuarios[$x]['idUsuarios'];
            $myConsulta  = "INSERT INTO notificaciones (usuario, mensaje) VALUES ($myIdUser, $correo)";
            mysqli_query($conexion, $myConsulta);

            $usuarioQuery = mysqli_query($conexion, 'SELECT * FROM usuarios WHERE id_user = '.$idUsuarios[$x]["idUsuarios"].'');
            while ($fila = mysqli_fetch_assoc($usuarioQuery)){
                $usuario[] = $fila;
            }

            // PHPMailer //
            $mail = new PHPMailer(true);

            $nick = $usuario[$x]['nombre_user'];
            $email = $usuario[$x]['mail'];

            try {
                //Server settings
                $mail->SMTPDebug = 0;                                       // Enable verbose debug output
                $mail->isSMTP();                                            // Set mailer to use SMTP
                $mail->Host       = 'smtp.gmail.com';                       // Specify main and backup SMTP servers
                $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
                $mail->Username   = 'easyfield.upv@gmail.com';              // SMTP username
                $mail->Password   = 'EasyField_upv';                        // SMTP password
                $mail->SMTPSecure = 'tls';                                  // Enable TLS encryption, `ssl` also accepted
                $mail->Port       = 587;                                    // TCP port to connect to

                //Recipients
                $mail->setFrom('easyfield.upv@gmail.com', 'EasyField | UPV');
                $mail->addAddress($email, $nick);                     // Add a recipient

                // Content
                $mail->isHTML(true);                                  // Set email format to HTML
                $mail->Subject = '[ALERTA] Su plantacion esta en peligro!';
                $mail->Body = '
                <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
                <html style="width:100%;font-family:\'open sans\', \'helvetica neue\', helvetica, arial, sans-serif;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%;padding:0;Margin:0;">
                 <head>
                  <meta charset="UTF-8">
                  <meta content="width=device-width, initial-scale=1" name="viewport">
                  <meta name="x-apple-disable-message-reformatting">
                  <meta http-equiv="X-UA-Compatible" content="IE=edge">
                  <meta content="telephone=no" name="format-detection">
                  <title>ALERTA | EasyField</title>
                  <!--[if (mso 16)]>
                    <style type="text/css">
                    a {text-decoration: none;}
                    </style>
                    <![endif]-->
                  <!--[if gte mso 9]><style>sup { font-size: 100% !important; }</style><![endif]-->
                  <!--[if !mso]><!-- -->
                  <link href="https://fonts.googleapis.com/css?family=Lato:400,400i,700,700i" rel="stylesheet">
                  <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,400i,700,700i" rel="stylesheet">
                  <!--<![endif]-->
                  <style type="text/css">
                @media only screen and (max-width:600px) {p, ul li, ol li, a { font-size:16px!important; line-height:150%!important } h1 { font-size:32px!important; text-align:left; line-height:120%!important } h2 { font-size:26px!important; text-align:left; line-height:120%!important } h3 { font-size:20px!important; text-align:left; line-height:120%!important } h1 a { font-size:36px!important; text-align:left } h2 a { font-size:30px!important; text-align:left } h3 a { font-size:18px!important; text-align:left } .es-menu td a { font-size:16px!important } .es-header-body p, .es-header-body ul li, .es-header-body ol li, .es-header-body a { font-size:16px!important } .es-footer-body p, .es-footer-body ul li, .es-footer-body ol li, .es-footer-body a { font-size:16px!important } .es-infoblock p, .es-infoblock ul li, .es-infoblock ol li, .es-infoblock a { font-size:12px!important } *[class="gmail-fix"] { display:none!important } .es-m-txt-c, .es-m-txt-c h1, .es-m-txt-c h2, .es-m-txt-c h3 { text-align:center!important } .es-m-txt-r, .es-m-txt-r h1, .es-m-txt-r h2, .es-m-txt-r h3 { text-align:right!important } .es-m-txt-l, .es-m-txt-l h1, .es-m-txt-l h2, .es-m-txt-l h3 { text-align:left!important } .es-m-txt-r img, .es-m-txt-c img, .es-m-txt-l img { display:inline!important } .es-button-border { display:inline-block!important } a.es-button { font-size:16px!important; display:inline-block!important; border-width:15px 30px 15px 30px!important } .es-btn-fw { border-width:10px 0px!important; text-align:center!important } .es-adaptive table, .es-btn-fw, .es-btn-fw-brdr, .es-left, .es-right { width:100%!important } .es-content table, .es-header table, .es-footer table, .es-content, .es-footer, .es-header { width:100%!important; max-width:600px!important } .es-adapt-td { display:block!important; width:100%!important } .adapt-img { width:100%!important; height:auto!important } .es-m-p0 { padding:0px!important } .es-m-p0r { padding-right:0px!important } .es-m-p0l { padding-left:0px!important } .es-m-p0t { padding-top:0px!important } .es-m-p0b { padding-bottom:0!important } .es-m-p20b { padding-bottom:20px!important } .es-mobile-hidden, .es-hidden { display:none!important } .es-desk-hidden { display:table-row!important; width:auto!important; overflow:visible!important; float:none!important; max-height:inherit!important; line-height:inherit!important } .es-desk-menu-hidden { display:table-cell!important } table.es-table-not-adapt, .esd-block-html table { width:auto!important } table.es-social { display:inline-block!important } table.es-social td { display:inline-block!important } }
                #outlook a {
                    padding:0;
                }
                .ExternalClass {
                    width:100%;
                }
                .ExternalClass,
                .ExternalClass p,
                .ExternalClass span,
                .ExternalClass font,
                .ExternalClass td,
                .ExternalClass div {
                    line-height:100%;
                }
                .es-button {
                    mso-style-priority:100!important;
                    text-decoration:none!important;
                }
                a[x-apple-data-detectors] {
                    color:inherit!important;
                    text-decoration:none!important;
                    font-size:inherit!important;
                    font-family:inherit!important;
                    font-weight:inherit!important;
                    line-height:inherit!important;
                }
                .es-desk-hidden {
                    display:none;
                    float:left;
                    overflow:hidden;
                    width:0;
                    max-height:0;
                    line-height:0;
                    mso-hide:all;
                }
                </style>
                 </head>
                 <body style="width:100%;font-family:\'open sans\', \'helvetica neue\', helvetica, arial, sans-serif;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%;padding:0;Margin:0;">
                  <div class="es-wrapper-color" style="background-color:#EEEEEE;">
                   <!--[if gte mso 9]>
                            <v:background xmlns:v="urn:schemas-microsoft-com:vml" fill="t">
                                <v:fill type="tile" color="#eeeeee"></v:fill>
                            </v:background>
                        <![endif]-->
                   <table class="es-wrapper" width="100%" cellspacing="0" cellpadding="0" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;padding:0;Margin:0;width:100%;height:100%;background-repeat:repeat;background-position:center top;">
                     <tr style="border-collapse:collapse;">
                      <td valign="top" style="padding:0;Margin:0;">
                       <table cellpadding="0" cellspacing="0" class="es-content" align="center" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;table-layout:fixed !important;width:100%;">
                         <tr style="border-collapse:collapse;">
                          <td align="center" style="padding:0;Margin:0;">
                           <table class="es-content-body" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:transparent;" width="600" cellspacing="0" cellpadding="0" align="center">
                             <tr style="border-collapse:collapse;">
                              <td align="left" style="Margin:0;padding-left:10px;padding-right:10px;padding-top:15px;padding-bottom:15px;">
                               <!--[if mso]><table width="580" cellpadding="0" cellspacing="0"><tr><td width="282" valign="top"><![endif]-->
                               <table class="es-left" cellspacing="0" cellpadding="0" align="left" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:left;">
                                 <tr style="border-collapse:collapse;">
                                  <td width="282" align="left" style="padding:0;Margin:0;">
                                   <table width="100%" cellspacing="0" cellpadding="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;">
                                     <tr style="border-collapse:collapse;">
                                     </tr>
                                   </table></td>
                                 </tr>
                               </table>
                               <!--[if mso]></td><td width="20"></td><td width="278" valign="top"><![endif]-->
                               <table class="es-right" cellspacing="0" cellpadding="0" align="right" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:right;">
                                 <tr style="border-collapse:collapse;">
                                  <td width="278" align="left" style="padding:0;Margin:0;">
                                   <table width="100%" cellspacing="0" cellpadding="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;">
                                     <tr style="border-collapse:collapse;">
                                     </tr>
                                   </table></td>
                                 </tr>
                               </table>
                               <!--[if mso]></td></tr></table><![endif]--></td>
                             </tr>
                           </table></td>
                         </tr>
                       </table>
                       <table class="es-content" cellspacing="0" cellpadding="0" align="center" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;table-layout:fixed !important;width:100%;">
                         <tr style="border-collapse:collapse;"></tr>
                         <tr style="border-collapse:collapse;">
                          <td align="center" style="padding:0;Margin:0;">
                           <table class="es-header-body" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#000000;" width="600" cellspacing="0" cellpadding="0" bgcolor="#000" align="center">
                             <tr style="border-collapse:collapse;">
                              <td align="left" style="Margin:0;padding-top:35px;padding-left:35px;padding-right:35px;padding-bottom:40px;">
                               <table width="100%" cellspacing="0" cellpadding="0" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;">
                                 <tr style="border-collapse:collapse;">
                                  <td width="530" valign="top" align="center" style="padding:0;Margin:0;">
                                   <table width="100%" cellspacing="0" cellpadding="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;">
                                     <tr style="border-collapse:collapse;">
                                      <td class="es-m-txt-c" align="center" style="padding:0;Margin:0;"><h3 style="Margin:0;line-height:38px;mso-line-height-rule:exactly;font-family:lato, \'helvetica neue\', helvetica, arial, sans-serif;font-size:38px;font-style:normal;font-weight:bold;color:#FFFFFF;">EasyField</h3></td>
                                     </tr>
                                   </table></td>
                                 </tr>
                               </table></td>
                             </tr>
                           </table></td>
                         </tr>
                       </table>
                       <table class="es-content" cellspacing="0" cellpadding="0" align="center" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;table-layout:fixed !important;width:100%;">
                         <tr style="border-collapse:collapse;">
                          <td align="center" style="padding:0;Margin:0;">
                           <table class="es-content-body" width="600" cellspacing="0" cellpadding="0" bgcolor="#ffffff" align="center" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#FFFFFF;">
                             <tr style="border-collapse:collapse;">
                              <td align="left" style="Margin:0;padding-bottom:25px;padding-top:35px;padding-left:35px;padding-right:35px;">
                               <table width="100%" cellspacing="0" cellpadding="0" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;">
                                 <tr style="border-collapse:collapse;">
                                  <td width="530" valign="top" align="center" style="padding:0;Margin:0;">
                                   <table width="100%" cellspacing="0" cellpadding="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;">
                                     <tr style="border-collapse:collapse;">
                                      <td align="left" style="padding:0;Margin:0;padding-bottom:5px;padding-top:20px;"><h3 style="Margin:0;line-height:22px;mso-line-height-rule:exactly;font-family:\'open sans\', \'helvetica neue\', helvetica, arial, sans-serif;font-size:18px;font-style:normal;font-weight:bold;color:#333333;">Alerta, su plantacion puede estar en peligro:<br></h3></td>
                                     </tr>
                                     <tr style="border-collapse:collapse;">
                                      <td align="left" style="padding:0;Margin:0;padding-bottom:10px;padding-top:15px;"><p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-size:16px;font-family:\'open sans\', \'helvetica neue\', helvetica, arial, sans-serif;line-height:24px;color:#777777;">'.$correo.'</p></td>
                                     </tr>
                                     <tr style="border-collapse:collapse;">
                                      <td align="left" style="padding:0;Margin:0;padding-top:40px;"><h3 style="Margin:0;line-height:22px;mso-line-height-rule:exactly;font-family:\'open sans\', \'helvetica neue\', helvetica, arial, sans-serif;font-size:18px;font-style:normal;font-weight:bold;color:#333333;">EasyField</h3></td>
                                     </tr>
                                     <tr style="border-collapse:collapse;">
                                      <td align="left" style="padding:0;Margin:0;"><p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-size:16px;font-family:\'open sans\', \'helvetica neue\', helvetica, arial, sans-serif;line-height:24px;color:#777777;">Programa automatico de notificaciones</p></td>
                                     </tr>
                                   </table></td>
                                 </tr>
                               </table></td>
                             </tr>
                           </table></td>
                         </tr>
                       </table>
                       <table class="es-content" cellspacing="0" cellpadding="0" align="center" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;table-layout:fixed !important;width:100%;">
                         <tr style="border-collapse:collapse;">
                          <td align="center" style="padding:0;Margin:0;">
                           <table class="es-content-body" width="600" cellspacing="0" cellpadding="0" bgcolor="#ffffff" align="center" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#FFFFFF;">
                             <tr style="border-collapse:collapse;">
                              <td align="left" style="padding:0;Margin:0;padding-top:15px;padding-left:35px;padding-right:35px;">
                               <table width="100%" cellspacing="0" cellpadding="0" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;">
                                 <tr style="border-collapse:collapse;">
                                  <td width="530" valign="top" align="center" style="padding:0;Margin:0;">
                                   <table width="100%" cellspacing="0" cellpadding="0" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;">
                                     <tr style="border-collapse:collapse;">
                                      <td align="center" style="padding:0;Margin:0;display:none;"></td>
                                     </tr>
                                   </table></td>
                                 </tr>
                               </table></td>
                             </tr>
                           </table></td>
                         </tr>
                       </table>
                       <table cellpadding="0" cellspacing="0" class="es-content" align="center" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;table-layout:fixed !important;width:100%;">
                         <tr style="border-collapse:collapse;">
                          <td align="center" style="padding:0;Margin:0;">
                           <table class="es-content-body" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#2DEDF4;" width="600" cellspacing="0" cellpadding="0" bgcolor="#2dedf4" align="center">
                             <tr style="border-collapse:collapse;">
                              <td align="left" style="padding:0;Margin:0;">
                               <table width="100%" cellspacing="0" cellpadding="0" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;">
                                 <tr style="border-collapse:collapse;">
                                  <td width="600" valign="top" align="center" style="padding:0;Margin:0;">
                                   <table width="100%" cellspacing="0" cellpadding="0" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;">
                                     <tr style="border-collapse:collapse;">
                                      <td align="center" style="padding:0;Margin:0;display:none;"></td>
                                     </tr>
                                   </table></td>
                                 </tr>
                               </table></td>
                             </tr>
                           </table></td>
                         </tr>
                       </table>
                       <table cellpadding="0" cellspacing="0" class="es-footer" align="center" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;table-layout:fixed !important;width:100%;background-color:transparent;background-repeat:repeat;background-position:center top;">
                         <tr style="border-collapse:collapse;">
                          <td align="center" style="padding:0;Margin:0;">
                           <table class="es-footer-body" width="600" cellspacing="0" cellpadding="0" align="center" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#FFFFFF;">
                             <tr style="border-collapse:collapse;">
                              <td align="left" bgcolor="#000000" style="Margin:0;padding-top:35px;padding-left:35px;padding-right:35px;padding-bottom:40px;background-color:#000000;">
                               <table width="100%" cellspacing="0" cellpadding="0" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;">
                                 <tr style="border-collapse:collapse;">
                                  <td width="530" valign="top" align="center" style="padding:0;Margin:0;">
                                   <table width="100%" cellspacing="0" cellpadding="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;">
                                     <tr style="border-collapse:collapse;">
                                      <td align="center" style="padding:0;Margin:0;padding-bottom:35px;"><p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-size:14px;font-family:\'open sans\', \'helvetica neue\', helvetica, arial, sans-serif;line-height:21px;color:#EFEFEF;"><b>UNIVERSITAT POLITECNICA DE VALENCIA</b></p><p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-size:14px;font-family:\'open sans\', \'helvetica neue\', helvetica, arial, sans-serif;line-height:21px;color:#EFEFEF;"><b>Escuela Politecnica Superior de Gandia</b></p></td>
                                     </tr>
                                   </table></td>
                                 </tr>
                               </table></td>
                             </tr>
                           </table></td>
                         </tr>
                       </table>
                       <table class="es-content" cellspacing="0" cellpadding="0" align="center" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;table-layout:fixed !important;width:100%;">
                         <tr style="border-collapse:collapse;">
                          <td align="center" style="padding:0;Margin:0;">
                           <table class="es-content-body" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:transparent;" width="600" cellspacing="0" cellpadding="0" align="center">
                             <tr style="border-collapse:collapse;">
                              <td align="left" style="Margin:0;padding-left:20px;padding-right:20px;padding-top:30px;padding-bottom:30px;">
                               <table width="100%" cellspacing="0" cellpadding="0" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;">
                                 <tr style="border-collapse:collapse;">
                                 </tr>
                               </table></td>
                             </tr>
                           </table></td>
                         </tr>
                       </table></td>
                     </tr>
                   </table>
                  </div>
                 </body>
                </html>
                ';

                $mail->send();
            } catch (Exception $e) {
                echo "Error, el mensaje no pudo ser enviado: {$mail->ErrorInfo}";
            }
        }
    }

}


?>
