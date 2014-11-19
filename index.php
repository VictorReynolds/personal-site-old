<?php
	include("inc/html_header.php");
?>

<div id="wrapper_main">
	<header id="title_contents"><h1> Victor Reynolds </h1></header>

	<div id="body_contents">
		<p>I&apos;m a sophomore Computer Science student at the University of South Carolina, also minoring in Islamic World Cultures with a focus on the Arabic Language.</p>
		<p>This website is currently under construction; however, you can find an up-to-date copy of my résumé by following the link on the left.</p>
		<p>.هذه جملة عربية</p>
		
		<img class="img-body-center" src="images/home/splash_image.jpg">


	</div>
	
	<footer><p>&copy; Victor Reynolds <?php echo date("Y");?></footer>
</div>

<script type="text/javascript">
	$(function() {
	 
	 $('#nav_container > li:not(.active) > a').stop().animate({'marginLeft':'-85px'},1000);
	 $('li ul').hide();
	 
	 $('#nav_container > li:not(.active)').hover(
	  function () {
	   $('ul', $(this)).attr('style', '');
	   $(' > a',$(this)).stop().animate({'marginLeft':'-2px'},200);
	   $('ul', $(this)).stop().fadeIn({ duration: 400, queue: false }).css('display', 'none').slideDown();
	  },
	  function () {
	   $(' > a',$(this)).stop().animate({'marginLeft':'-85px'},200);
	   $('ul', $(this)).stop().fadeOut({ duration: 400, queue: false }).hide("slide", { direction: "left" }, 1000);
	  }
	 );
	});
</script>
</body>

</html>