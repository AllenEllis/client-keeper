<?php echo $this->render('app/views/header.html',$this->mime,get_defined_vars(),0); ?>
<h2>Projects for <?php echo $client_full; ?>:</h2>

<?php echo $this->raw($client_projects); ?>

<?php echo $this->render('app/views/footer.html',$this->mime,get_defined_vars(),0); ?>