<?php 
if ($project->removed == 0){
?>
 <script async src="http://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>

<div <?php /*/ ?> class=""style="background-image:url(<?php echo $project->image; ?>); background-size:cover;" <?php /*/?> class="parallax-window" data-parallax="scroll" data-speed="0.4" data-image-src="<?php echo $project->image; ?>" <?php //*/ ?>>
    
    <div class="hide-for-small pt50"></div>
    
    <div class="row ">
       <div class="columns pt15 pb15" style="background-color: rgba(255,255,255,0.7)"> 

    <div class="row show-for-small">
        <div class="columns">
            <div class="pt20"></div>
           
			<script>
				 if (!window.matchMedia("screen and (min-width: 40.063em)").matches){
					 console.log(window.matchMedia("screen and (min-width: 40.063em)"));
						 document.write (
							 '<ins class="adsbygoogle" style="display:block" data-ad-client="ca-pub-0534207295672567" data-ad-slot="6740352234" data-ad-format="auto"></ins>'
							);

						 
				 }
			 </script>
        </div>
    </div> 
    
    <div class="row">
          <div class="columns medium-4">
              <?php if ($project->rating){ ?>
              <span class="fa-stack fa-5x fa-lg" style="color:#0088bb; right:-20px; top:-50px; margin:0; padding:0; position: absolute;" >
                <i class="fa fa-certificate fa-stack-1x" ></i>
                <i style="font-size: 60%; " class="fa-stack-1x fa-inverse">
                  <span style="font-style: normal;" data-tooltip data-options="disable_for_touch:true" class="tip-right radius" title="Our rating for this project based on content quality, social reach and campaign progress">
                    <?php echo round($project->rating); ?>
                  </span>
                </i>
              </span>
              <?php } ?>
                <a href="<?php echo Yii::app()->createUrl("feed/rl",array("l"=>$project->link)); ?>" trk="link_preview_<?php echo $project->id; ?>" target="_blank">
                  <img src="<?php echo $project->image; ?>" style="width:100%">
                </a>
              <div class="show-for-small pb20"></div>
          </div>
          <div class="columns medium-8">
                <a href="<?php echo Yii::app()->createUrl("feed/rl",array("l"=>$project->link)); ?>" trk="link_preview_<?php echo $project->id; ?>" target="_blank">
                    <h1 class="small"><?php echo $project->title; ?></h1>
                </a>
                <small><?php echo "in ".$project->origCategory->name." on ".date("D, d M Y H:i:s e",strtotime($project->time_added)); ?></small>
                <p style="margin-top: 10px;" class="mb20">
                <?php echo $project->description; ?>
                <br />
                <?php 
                  echo "<br /><strong>".$project->platform->name."</strong> - ".$project->origCategory->name." ";//." <br />";
                  if (!empty($project->creator)) echo "<br />Creator: <i>".$project->creator."</i> ";
                  //if (!empty($project->location)) $desc.= " \nCreator of project: ".$project->location;
                  if (!empty($project->goal)) echo "<br />Project goal: <strong>".$project->goal."</strong>";
                  if (!empty($project->type_of_funding)){
                    if ($project->type_of_funding == 0) echo " <i>fixed funding</i>";
                    else echo " <i>flexible funding</i>";
                  }

                   //"<br />Rating: ".round($project->rating);
                ?>
                </p>
                <a href="<?php echo Yii::app()->createUrl("feed/rl",array("l"=>$project->link)); ?>" target="_blank" trk="link_preview_<?php echo $project->id; ?>">View on <?php echo $project->platform->name; ?> <i class="fa fa-external-link"></i></a>
          </div>

    </div>
		   
	<?php if ((($ad_type = rand(0, 1)) == 0) || (count($similar) != 3)){ echo $ad_type; ?>
    <div class="row hide-for-small">
      <div class="columns pt30 text-center">
          <script>
				 if (window.matchMedia("screen and (min-width: 40.063em)").matches){
					  console.log(window.matchMedia("screen and (min-width: 40.063em)"));
						 document.write (
							 '<ins class="adsbygoogle" style="display:block" data-ad-client="ca-pub-0534207295672567" data-ad-slot="6740352234" data-ad-format="auto"></ins>'
							);

						 
				 }
			</script>
      </div>
    </div>		   
	<?php } ?>
           
    <?php if (count($similar) == 3){ ?>
    <div class="row">
      <div class="columns pt30 text-center">
          <hr>
            <h3 class="pt20">Similar campaigns</h3>  
      </div>
    </div>
           
    <div class="row pt20 pb10 hide-for-small text-center">
        <div class="columns medium-4">
            <?php   if (!empty($similar[0]->internal_link)){
                $internal_link = Yii::app()->createUrl("view/index", array("name" => $similar[0]->internal_link));
            }else{
                if (strpos($project->title, "/") === false)
                    $internal_link = htmlspecialchars(str_replace(" ", "+", (Yii::app()->createUrl("view/index", array("name" => $similar[0]->title)))));
                else
                    $internal_link = htmlspecialchars(str_replace(" ", "+", (Yii::app()->createUrl("view/index") . "?name=" . $similar[0]->title)));
            }
            ?>
            
            <a href="<?php echo $internal_link; ?>" trk="link_previewinternal_<?php echo $similar[0]->id; ?>">
                <img src="<?php echo $similar[0]->image; ?>" style="height: 250px; width: auto;">
            </a>
            <div class="pt20">
            <strong><em><?php echo $similar[0]->title ?></em></strong>
            </div>
        </div>
        <div class="columns medium-4">
			<?php if ($ad_type == 1){ ?>
			<script>
				 if (window.matchMedia("screen and (min-width: 40.063em)").matches){
					  console.log(window.matchMedia("screen and (min-width: 40.063em)"));
						 document.write (
							 '<ins class="adsbygoogle" style="display:blockstyle="display:inline-block;width:300px;height:250px"  data-ad-client="ca-pub-0534207295672567" data-ad-slot="3635121837"></ins>'
							);
				 }
			</script>
			
            <div class="pt20">
            <strong><em>Google</em></strong>
            </div>
			<?php }else{ ?>
			<?php   if (!empty($similar[1]->internal_link)){
                $internal_link = Yii::app()->createUrl("view/index", array("name" => $similar[1]->internal_link));
            }else{
                if (strpos($project->title, "/") === false)
                    $internal_link = htmlspecialchars(str_replace(" ", "+", (Yii::app()->createUrl("view/index", array("name" => $similar[1]->title)))));
                else
                    $internal_link = htmlspecialchars(str_replace(" ", "+", (Yii::app()->createUrl("view/index") . "?name=" . $similar[1]->title)));
            }
            ?>
            
            <a href="<?php echo $internal_link; ?>" trk="link_previewinternal_<?php echo $similar[1]->id; ?>">
                <img src="<?php echo $similar[1]->image; ?>" style="height: 250px; width: auto;">
            </a>
            <div class="pt20">
            <strong><em><?php echo $similar[1]->title ?></em></strong>
            </div>
			<?php } ?>
        </div>
        <div class="columns medium-4">
            <?php if (!empty($similar[2]->internal_link)){
                $internal_link = Yii::app()->createUrl("view/index", array("name" => $similar[2]->internal_link));
            }else{
                if (strpos($project->title, "/") === false)
                    $internal_link = htmlspecialchars(str_replace(" ", "+", (Yii::app()->createUrl("view/index", array("name" => $similar[2]->title)))));
                else
                    $internal_link = htmlspecialchars(str_replace(" ", "+", (Yii::app()->createUrl("view/index") . "?name=" . $similar[2]->title)));
            }
            ?>
            
            <a href="<?php echo $internal_link; ?>" trk="link_previewinternal_<?php echo $similar[2]->id; ?>">
                <img src="<?php echo $similar[2]->image; ?>" style="height: 250px; width: auto;">
            </a>
            <div class="pt20">
            <strong><em><?php echo $similar[2]->title ?></em></strong>
            </div>
        </div>
    </div>
    <?php } ?>          
    
    <div class="row">
      <div class="columns pt30 text-center">

        <a href="/" trk="button_view_index">
            <input type="button" class="button success medium radius" value="Customize your projects" style=" font-weight: bold;">
        </a>
           <div class="show-for-small pt30"></div>
      </div>
    </div>
       
        </div>
    </div>
       
   <div class="hide-for-small pt60"></div>
</div>
<?php }else{ ?>
<div class="pt30 pb30">
      <div class="row">
        <div class="columns medium-12">
            <h4>This project has been removed.</h4>
        </div>
      </div>
    </div>
<?php } ?>