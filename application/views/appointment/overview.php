<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="content-wrapper">
  <section class="content-header">
    <div class="breadcrumbs-bar">
        <div class="col-md-12">
          <?=RenderBreadCrum()?>
        </div>
    </div>
    <div class="col-md-12">
        <?=feedback();?>
      </div>
      <div class="toggle-bar">
        <div class="col-md-12 text-right">
          <i class="fa fa-arrows-alt" title="Klik om te wisselen tussen fullscreen" aria-hidden="true" id="fullscreen"></i>
        </div>
    </div>
      <div class="col-md-12">
        <table class="table table-striped table-center">
          <thead>
            <tr>
              <th>Projectnaam</th>
              <th>Klant</th>
              <th>Docent</th>
              <th>Leden</th>
              <th>Eerst volgende code revieuw</th>
              <th>Eerst volgende iteratie bespreking</th>
              <th class="fullscreen-hide">Opmerking</th>
            </tr>
          </thead>
          <tbody>

          <?php
            foreach ($projects as $project) { 
          ?>
            <tr class="red">
              <td><?=$project->name ?></td>
              <td><?=$project->client ?></td>
              <td><?=$project->teacher ?></td>
              <td><?php foreach ($project->members as $member) { 
            echo $member->name." ".$member->insertion." ".$member->lastname." "; } ?></td>
              <td><?=$project->code_date ." ". $project->code_start ?></td>
              <td><?=$project->iteration_date ." ". $project->iteration_start ?></td>
              <td class="fullscreen-hide"><a href="/appointment/addAppointment/<?=$project->slug ?>"><i class="fa fa-pencil-square" aria-hidden="true" style="color:white;"></i>
</a></td>
            </tr>

            <?php
                }
            ?>

            <!--<tr class="orange">
              <td>componenten beheer</td>
              <td>de jong</td>
              <td>de jong</td>
              <td>student 3, student 4</td>
              <td>maandag 11 uur</td>
              <td>vrijdag 13 uur</td>
              <td>erg lange laadtijd</td>
            </tr>
            <tr class="green">
              <td>speur tocht</td>
              <td>ruijter</td>
              <td>ruijter</td>
              <td>student 5, student 6</td>
              <td>25-10-2017 13 uur</td>
              <td>donderdag 11 uur</td>
              <td>security workshop onvoldoende</td>
            </tr>-->
          </tbody>
        </table>

      </div>
    </div>
  </section>
</div>