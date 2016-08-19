<?php
/*
Plugin Name: Plugin Detalles
Plugin URI: http://www.open-link.net
Description: PLugin para mostrar detalles de los archivos de transparencia
*/
/* Start Adding Functions Below this Line */
if (!defined('ABSPATH')) {
    	exit;
	}
//Cargamos nuestros script y css
	function load_script_openlink() {			
		wp_enqueue_script( 'colorbox', plugins_url('/js/jquery.colorbox-min.js', __FILE__ ),array('jquery'),null,true );
	}		
	add_action( 'wp_enqueue_scripts', 'load_script_openlink', 5 );
	function load_css_openlink() {		
		wp_register_style( 'colorbox', plugins_url( '/css/colorbox.css',__FILE__), array(), 20160728, 'all' );
		wp_enqueue_style( 'colorbox' );
	}	
	add_action( 'wp_enqueue_scripts', 'load_css_openlink' );
	
function get_detail_openlink ($atts) {
	//$idpost = $atts['id'];
	extract( shortcode_atts(  array(      
        'id' => '',        
    ), $atts ) );
	//$attachment = get_post($idpost);
	$attachment = get_post($id);
	echo "<br />";
	var_dump( $attachment->ID);
	//$funcionario_responsable = get_post_meta($attachment->ID, 'be_funcionario_responsable', true);
	$datos = get_post_meta($attachment->ID);	
	$num_visitas = get_post_meta( get_the_ID(), 'count_view', true );

	if (isset($datos['be_depto_name'][0]))
		$depto_responsable = $datos['be_depto_name'][0];
	else
		$depto_responsable = "";
	if (isset($datos['be_funcionario_responsable'][0]))
		$func_responsable = $datos['be_funcionario_responsable'][0];
	else
		$func_responsable = "";
	if (isset($datos['transparencia_responsable'][0]))
		$resp_subir_info = $datos['transparencia_responsable'][0];
	else
		$resp_subir_info = "";
	return '<a href="#tableDetalle'.$id.'" class="detalles'.$id.'"><button type="button">
	  			Detalles
			</button></a>
			<div id="myModalDetalles"style="display:none">
				<table class="table" id="tableDetalle'.$id.'">
					<caption>Detalles de Archivo</caption>
					<tr><td>Nombre </td><td>'.get_the_title($id).'</tr>
					<tr><td>Funcionario Resposable</td><td>'.$func_responsable.'</tr>
					<tr><td>Dirección Resposable</td><td>'.$depto_responsable.'</tr>
					<tr><td>Resposable de subir la información</td><td>'.$resp_subir_info.'</tr>
					<tr><td>Tipo de Archivo</td><td>'.$attachment->post_mime_type.'</tr>
					<tr><td>Tamaño de Archivo</td><td>'.size_format(filesize( get_attached_file($attachment->ID))).'</tr>
					<tr><td>Fecha de Creación</td><td>'.get_the_date().'</tr>
					<tr><td>Fecha de Última Modificación</td><td>'.$attachment->post_modified.'</tr>
					<tr><td>Número de vistas</td><td>'.$num_visitas.'</tr>								
				</table>
			</div>
			<script type="text/javascript">
				jQuery(document).ready(function($) {
					var $detail = $("#tableDetalle'.$id.'");
					$(".detalles'.$id.'").colorbox({
						inline:true, 
						href:$detail,
						overlayClose: false, 
						close:"Cerrar",
						maxWidth: "100%",
						maxHeight: "80%"
					});
				})			
			</script>';
}


/* Agregamos metabox a upload form */
function be_attachment_field_transparencia( $form_fields, $post ) {
	$form_fields['be-depto-name'] = array(
		'label' => 'Área Responsable',
		'input' => 'text',
		'value' => get_post_meta( $post->ID, 'be_depto_name', true ),
		'helps' => 'Departamento responsable de la información',
	);

	$form_fields['be-funcionario-responsable'] = array(
		'label' => 'Funcionario Responsable',
		'input' => 'text',
		'value' => get_post_meta( $post->ID, 'be_funcionario_responsable', true ),
		'helps' => 'Funcionario responsable de proporcionar la información',
	);
	$form_fields['transparencia_responsable'] = array(
		'label' => 'Responsable de subir la información',
		'input' => 'text',
		'value' => get_post_meta( $post->ID, 'transparencia_responsable', true ),
		'helps' => 'Funcionario responsable de información',
	);

	return $form_fields;
}

add_filter( 'attachment_fields_to_edit', 'be_attachment_field_transparencia', 10, 3 );


function be_attachment_field_credit_save( $post, $attachment ) {
	if( isset( $attachment['be-depto-name'] ) )
		update_post_meta( $post['ID'], 'be_depto_name', $attachment['be-depto-name'] );
	if( isset( $attachment['be-funcionario-responsable'] ) )
		update_post_meta( $post['ID'], 'be_funcionario_responsable', $attachment['be-funcionario-responsable'] );
	if( isset( $attachment['transparencia_responsable'] ) )
		update_post_meta( $post['ID'], 'transparencia_responsable', $attachment['transparencia_responsable'] ) ;
	return $post;
}

add_filter( 'attachment_fields_to_save', 'be_attachment_field_credit_save', 10, 3 );

add_shortcode('transparencia', 'get_detail_openlink');

?>