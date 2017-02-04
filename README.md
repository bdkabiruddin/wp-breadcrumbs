# wp-breadcrumbs

How to use

<?php
if(function_exists('wp_breadcrumbs')){
	$html = '';
	$breadcrumbs = wp_breadcrumbs();
	$html .= '<ol class="breadcrumb">';
	foreach ($breadcrumbs as $key => $breadcrumb) {
		$html .= '<li class="breadcrumb-item">'; if(!empty($breadcrumb['link'])){ 
			$html .= '<a href="'.$breadcrumb['link'].'">';
		}
		$html .= $breadcrumb['title']; if(!empty($breadcrumb['link'])){
			$html .= '</a>';
		}
		$html .= '</li> ';
	}
	$html .= '</ol>';
	echo $html;
}
?>
