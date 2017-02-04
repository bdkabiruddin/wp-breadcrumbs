# wp-breadcrumbs

How to use
<pre>
&lt;?php
if(function_exists('wp_breadcrumbs')){
$html = '';
$breadcrumbs = wp_breadcrumbs();
$html .= '&lt;ol class="breadcrumb"&gt;';
foreach ($breadcrumbs as $key =&gt; $breadcrumb) {
$html .= '&lt;li class="breadcrumb-item"&gt;'; if(!empty($breadcrumb['link'])){
$html .= '&lt;a href="'.$breadcrumb['link'].'"&gt;';
}
$html .= $breadcrumb['title']; if(!empty($breadcrumb['link'])){
$html .= '&lt;/a&gt;';
}
$html .= '&lt;/li&gt; ';
}
$html .= '&lt;/ol&gt;';
echo $html;
}
?&gt;
</pre>
