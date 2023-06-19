<?php

$title = get_field( 'title' );
$desc  = get_field( 'description' );

?>

<div>
	<h1 class="tstm-h1"><?php echo esc_html( $title ); ?></h1>
	<div><?php echo $desc; ?></div>
</div>
