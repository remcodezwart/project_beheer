<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
?>
	<div class="content-wrapper">
        <section class="content-header ">
          <div class="col-md-12">
            <?=RenderBreadCrum()?>
          </div>
          <div class="col-md-12">
            <?=feedback();?>
          </div>
          <h1>
            Gebruikers
            <small>Overzicht	</small>
          </h1>
        </section>

		
		<section class="content">
          <div class="row">
          	<div class="col-xs-12">
              <div class="box box-success">
                <div class="box-header">
                  <h3 class="box-title">Gebruikers</h3>
                  <button class="btn btn-success pull-right" onClick="javascript: window.location = '/Users/newUser';">Nieuw</button>
                </div>
                <div class="box-body">

                  <?php if ($deleted) { ?>
                          <div class="callout callout-success" onClick="javascript: jQuery('.callout').slideUp();" >
                            <h4>Verwijderd</h4>
                            <p>De gebruiker is met succes verwijderd</p>
                          </div>
                  <?php } ?>
                    <table id="users" class="table userTable table-bordered table-hover dataTable">
                      <thead>
                        <tr>
                          <th width="50" class="hidden-xs"></th>
                          <th>Naam</th>
                          <th width="117">&nbsp;</th>
                        </tr>
                      </thead>
                      <tbody>  
                      <?php foreach($users as $user) {?>
                        <tr>
                          <td><img src="<?=!empty($user->profile_image) ? $user->profile_image : '/custom/images/users/default.png'?>" width="45" class="img-circle" alt="<?=$user->name?>"></td>
                          <td><?=$user->name?></td>
                          <td>
                             <!-- edit / delete buttons -->

                             <a href="/Users/editUser/<?=$user->id?>" class="btn btn-success"><i class="fa fa-edit"></i></a>
                             <a href="/Users/deleteUser/<?=$user->id?>" class="btn btn-danger removeBtn"><i class="fa fa-trash"></i></a>
                          </td>
                        </tr>
                      <?php }?>
                      </tbody>
                    </table>
                </div>
              </div>
            </div>

          </div>
    </section>
  </div>

    <script type="text/javascript">
    jQuery(document).ready(function(){
      jQuery(".removeBtn").click(function(){
          showPopup('Verwijderen', 'Weet u zeker dat u deze gebruiker wilt verwijderen?', 'Ja', 'Nee', '', '', jQuery(this).attr('href'));
          return false;
      });
  });
    </script>