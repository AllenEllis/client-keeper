<?php echo $this->render('header.html',$this->mime,get_defined_vars(),0); ?>
<!-- *****************************************************************************************************************
 BLUE WRAP
 ***************************************************************************************************************** -->
<div id="blue">
    <div class="container">
        <div class="row">
            <h3><a href="<?php echo $client['client_url']; ?>"><?php echo $client['client_full']; ?></a> <span style="font-weight:lighter">| <?php echo $project['project_full']; ?></span></h3>
        </div><!-- /row -->
    </div> <!-- /container -->
</div><!-- /blue -->


<!-- *****************************************************************************************************************
	 TITLE & CONTENT
	 ***************************************************************************************************************** -->

<div class="container mt">
    <div class="row">
        <div class="col-lg-10 col-lg-offset-1 centered">
            <div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
                <!-- Indicators -->
                <ol class="carousel-indicators">
                    <li data-target="#carousel-example-generic" data-slide-to="0" class="active"></li>
                    <li data-target="#carousel-example-generic" data-slide-to="1"></li>
                    <li data-target="#carousel-example-generic" data-slide-to="2"></li>
                </ol>

               
            <?php echo $video_embed; ?>
        </div>

        <div class="col-lg-5 col-lg-offset-1">
            <div class="spacing"></div>
            <h4><?php echo $project['project_full']; ?></h4>
            <p><?php echo $project['description']; ?></p>
              </div>

        <div class="col-lg-4 col-lg-offset-1">
            <div class="spacing"></div>
            <h4>Download</h4>
            <div class="hline"></div>
            <?php echo $project['download_links']; ?>
            <p>&nbsp;</p>
            <h4>All Versions</h4>
            <div class="hline"></div>
            <?php echo $this->raw($version_summaries); ?>
            <p>&nbsp;</p>
        </div>

    </div><! --/row -->
</div><! --/container -->





<?php echo $this->render('footer.html',$this->mime,get_defined_vars(),0); ?>