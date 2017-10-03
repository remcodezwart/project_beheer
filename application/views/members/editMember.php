<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="content-wrapper">
    <section class="content">
    	<div class="editmember">
		    <div class="container">
		    	<div class="row">
		    		<div class="col-md-12">
					  	<?php echo form_open('members/editMemberAction'); ?>
							<label for="title">OV-Nummer</label>
								<div class="input-group">
									<input type="number" class="form-control" name="ovnumber" value="<?=$member['0']->ovnumber ?>" required/>
								</div>
							<label for="text">Voornaam</label>
								<div class="input-group">
									<input type="input" name="name" class="form-control" value="<?=$member['0']->name ?>" required/>
								</div>
							<label for="text">Tussenvoegsel</label>
								<div class="input-group">
									<input type="input" name="insertion" class="form-control" value="<?=$member['0']->insertion ?>" />
								</div>
							<label for="text">Achternaam</label>
								<div class="input-group">
									<input type="input" name="lastname" class="form-control" value="<?=$member['0']->lastname ?>" required/>
								</div>

							<div class="form-group">
								<label>Active</label><br>
								<label><input type="radio" id="active" name="active" value="1"
								 <?php if ($member['0']->active == 1) echo "checked" ?> /> Ja
								</label>
								<label><input type="radio" id="active" name="active" value="0"
								 <?php if ($member['0']->active == 0) echo "checked" ?> /> Nee
								</label>
							</div>
							<input type="hidden" name="slug" value="<?=$member['0']->slug ?>">
							<button type="submit" class="btn btn-blue">Student Aanpassen</button>
						</form>

					</div>
				</div>
			</div>
		</div>
    </section>
</div>
