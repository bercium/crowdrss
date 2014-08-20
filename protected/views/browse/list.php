<div class="pb30">
    <div class="row">
      <div class="columns">
        <h2><?php echo $title; ?></h2>
        <p>List is generated from new projects from past 7 days</p>
      </div>
    </div>
    <?php 
    $i = 1;
    foreach ($projects as $project){
    ?>
  
    <a href="" target="_blank" style="color:inherit;">
    <div class="row panel <?php if ($i < 4) echo "callout"; ?>" style="padding: 1rem">
      <div class="columns small-1">
        <h4 style="margin:0;">
          <?php echo $i; ?>
          
          <?php 
          /*
            $rand = rand(1,3);
            if ($rand == 1){
              ?><i class="fa fa-caret-up right" style="color:#43ac6a"></i><?php
            }
            if ($rand == 2){
              ?><i class="fa fa-caret-down right"  style="color:#f04124"></i><?php
            }
            if ($rand == 3){
              ?><i class="fa fa-minus right" style="color:#007095"></i><?php
            }
           */
          ?>
          
        </h4>
      </div>
      <div class="columns small-11 medium-8">
        <i class="fa fa-external-link right show-for-small"></i>
        <?php
          echo "<strong>".$project->title."</strong>";
          if ($project->creator) echo "<i> by ".$project->creator."</i>";
        ?>
        <br />
        <div style="padding-top:5px;">
          <small>
          <?php
            if ($allPlatforms) echo "<strong>".$project->platform->name."</strong> - ";
            if (isset($project->origCategory)) echo $project->origCategory->name;
          ?>
          </small>
        </div>
      </div>
      <div class="columns show-for-medium-up medium-3">
        <i class="fa fa-external-link right"></i>
        <?php 
        //
        echo "Goal: ".$project->goal."</strong>"; ?>
        <br />
        <div style="padding-top:5px;">
          <small>
          <?php
            echo "Ends on ".$project->end;
          ?>
          </small>
        </div>
        
      </div>
      
    </div>
    </a>
  
   <?php 
     $i++;
     } ?>
  
</div>