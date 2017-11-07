<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="content-wrapper">
    <section class="content">
    	<div class="col-md-12">
     		<?=RenderBreadCrum()?>
    	</div>
    	<div class="col-md-12">
    		<?=feedback();?>
    	</div>
    	<div class="addtag">
		    <div class="container">
		    	<div class="row">
		    		<div class="col-md-10">
					  	<?php echo form_open('tags/addTagAction'); ?>
							<label for="title">Naam</label>
								<div class="input-group">
									<input type="text" class="form-control" name="name" required/>
									<div class="input-group-addon">
										<i class="fa fa-info" data-toggle="tooltip"  aria-hidden="true" title="Alleen de nummers 0-9 toegestaan"></i>
									</div>
								</div>
								<label for="text">Beschrijving</label>
								<div class="input-group">
									<textarea maxlength="500" name="description" class="form-control" rows="5" required></textarea>
									<div class="input-group-addon">
										<i class="fa fa-info" data-toggle="tooltip"  aria-hidden="true" title="Alleen de letters a-z .0-9 en / - _ zijn toegestaan(niet hoofdlettergevoelig)"></i>
									</div>
								</div>
							<button type="submit" class="btn btn-blue">Tag Toevoegen</button>
						</form>

					</div>
				</div>
			</div>
		</div>
    </section>
</div>