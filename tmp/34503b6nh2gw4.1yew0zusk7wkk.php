<?php echo $this->render('header.html',$this->mime,get_defined_vars(),0); ?>
<!-- *****************************************************************************************************************
 BLUE WRAP
 ***************************************************************************************************************** -->
<div id="blue">
    <div class="container">
        <div class="row">
            <h3><?php echo $client['client_full']; ?> <span style="font-weight:lighter">| All Projects</span></h3>
        </div><!-- /row -->
    </div> <!-- /container -->
</div><!-- /blue -->

<?php echo $this->raw($client_projects); ?>

<?php echo $this->render('footer.html',$this->mime,get_defined_vars(),0); ?>