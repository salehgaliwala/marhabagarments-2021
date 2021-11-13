<?php
/**
 * Template Name: Page (Default)
 * Description: Page template with Sidebar on the left side.
 *
 */
$the_user = get_field('allowed_users');
if($the_user == NULL)
{

}
else if( in_array('0',$the_user) || empty( $the_user ) ) 
{
   // nothing 
} 
else 
{
    if(in_array(get_current_user_id(),$the_user) ) 
	{
		  // ok
	}
      
    else 
    { // NOTHING TO SEE, GO TO FRONT PAGE
        wp_redirect('/');
        header("Status: 302");
        exit;
    }
}
get_header();

the_post();

?>
<div class="row">

	<div class="col-md-12 order-md-2 col-sm-12">
		<div id="post-<?php the_ID(); ?>" <?php post_class( 'content' ); ?>>
			<h1 class="entry-title"><?php the_title(); ?></h1>
			<?php
				the_content();							
			

				wp_link_pages(
					array(
						'before' => '<div class="page-links">' . __( 'Pages:', 'marhaba' ),
						'after'  => '</div>',
					)
				);
				edit_post_link( esc_html__( 'Edit', 'marhaba' ), '<span class="edit-link">', '</span>' );
			?>
		</div><!-- /#post-<?php the_ID(); ?> -->
		
	</div><!-- /.col -->
	
</div><!-- /.row -->
<?php
get_footer();
