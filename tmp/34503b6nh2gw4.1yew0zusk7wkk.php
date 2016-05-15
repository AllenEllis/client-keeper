<?php echo $this->render('header.html',$this->mime,get_defined_vars(),0); ?>
<h2>Projects for <?php echo $client['client_full']; ?>:</h2>

<?php echo $this->raw($client_projects); ?>

<?php echo $this->render('footer.html',$this->mime,get_defined_vars(),0); ?>