<div class="pt30 pb30">
      <div class="row">
        <div class="columns medium-12">
          <h2><?php echo $title; ?></h2>
    <?php

    foreach ($projects as $project){
      echo $project->rating.": ".$project->platform->name."-".$project->title."<br />";
    }
    
    ?>
        </div>
      </div>
</div>