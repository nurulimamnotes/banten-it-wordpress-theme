<?php
/*
Style : Magz Style
*/
?>
<!DOCTYPE html>
<html lang="en">
<head>

<title><?php if (is_home()) { echo bloginfo('name');}
elseif (is_404()) { echo '404 Not Found';}
elseif (is_category()) { echo 'Category : '; wp_title('');}
elseif (is_search()) { echo 'Search Results';}
elseif ( is_day() || is_month() || is_year() ) { echo 'Archives'; wp_title('');}
else { echo wp_title('');} ?></title>

<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0;">

<link rel="pingback" href="http://www.nurulimam.com/xmlrpc.php" />
<?php wp_head(); ?>
<link rel="stylesheet" type="text/css" media="all" href="<?php echo get_template_directory_uri(); ?>/css/style/magz/reset.css" />
<link rel="stylesheet" type="text/css" media="all" href="<?php echo get_template_directory_uri(); ?>/css/style/magz/grid.css" />
<!--[if lt IE 9]><script src="http://www.nurulimam.com/wp-content/themes/h95vs4/js/html5.js" type="text/javascript"></script><![endif]-->
</head>
<body>

<article class="semua_16">

<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
<?php the_content(''); ?>

</article>

<?php endwhile; else : ?>
<?php endif; ?>

</body>
</html>