<?php 
if ($project->removed == 0){
?>
 <script async src="http://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>

<div <?php /*/ ?> class=""style="background-image:url(<?php echo $project->image; ?>); background-size:cover;" <?php /*/?> class="parallax-window" data-parallax="scroll" data-speed="0.4" data-image-src="<?php echo $project->image; ?>" <?php //*/ ?>>
    
    <div class="hide-for-small pt50"></div>
    
    <div class="row ">
       <div class="columns pt15 pb15" style="background-color: rgba(255,255,255,0.7)" itemscope itemtype="http://schema.org/Product">
            <?php if ($redirect){ ?>
                <h2 class="text-center mb40">Redirecting you in <span class="countdown">5</span>s</h2>
            <?php } ?>
           
            <div class="row show-for-small">
                <div class="columns">
                    <div class="pt20"></div>

                    <script>
                         if (!window.matchMedia("screen and (min-width: 40.063em)").matches){
                                 document.write (
                                     '<ins class="adsbygoogle" style="display:block" data-ad-client="ca-pub-0534207295672567" data-ad-slot="6740352234" data-ad-format="auto"></ins>'
                                    );
                                (adsbygoogle = window.adsbygoogle || []).push({}); 
                         }
                     </script>
                </div>
            </div> 

            <div class="row">
                  <div class="columns medium-4">
                      <?php if ($project->rating){ ?>
                      <span class="fa-stack fa-5x fa-lg" style="color:#0088bb; right:-20px; top:-50px; margin:0; padding:0; position: absolute;" itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
                        <i class="fa fa-certificate fa-stack-1x" ></i>
                        <i style="font-size: 60%; " class="fa-stack-1x fa-inverse">
                          <span style="font-style: normal;" data-tooltip data-options="disable_for_touch:true" class="tip-right radius" title="Our rating for this project based on content quality, social reach and campaign progress" itemprop="ratingValue">
                            <?php echo round($project->rating); ?>
                          </span>
						  <span itemprop="reviewCount" style="display: none;"><?php if (isset($project->creator_backed)) echo $project->creator_backed+1; else echo "1"; ?></span>
						  <span itemprop="bestRating" style="display: none;">10</span>
						  <span itemprop="worstRating" style="display: none;">0</span>
                        </i>
                      </span>
                      <?php } ?>
                        <a href="<?php echo Yii::app()->createUrl("feed/rl",array("l"=>$project->link)); ?>" trk="link_preview_<?php echo $project->id; ?>" target="_blank">
                          <img itemprop="image" src="<?php echo $project->image; ?>" alt='<?php echo $project->title; ?>' style="width:100%">
                        </a>
                      <div class="show-for-small pb20"></div>
                  </div>
                  <div class="columns medium-8">
                        <?php if (!Yii::app()->user->isGuest){ ?><a href="<?php echo Yii::app()->createUrl("view/index",array("remove"=>$project->id,"name"=>$project->internal_link)); ?>">Remove</a> <?php }?>
                        <a href="<?php echo Yii::app()->createUrl("feed/rl",array("l"=>$project->link)); ?>" trk="link_preview_<?php echo $project->id; ?>" target="_blank">
                            <h1 class="small" itemprop="name"><?php echo $project->title; ?></h1>
                        </a>
                        <small><?php echo "in ".$project->origCategory->name." on ".date("D, d M Y H:i:s e",strtotime($project->time_added)); ?></small>
                        <p style="margin-top: 10px;" class="mb20" itemprop="description">
                        <?php echo $project->description; ?>
                        <br />
                        <?php 
                          echo "<br /><strong>".$project->platform->name."</strong> - <span itemprop=\"category\">".$project->origCategory->name."</span> ";//." <br />";
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

            <?php if ((($ad_type = rand(0, 0)) == 0) || (count($similar) != 3)){ ?>
            <div class="row hide-for-small">
              <div class="columns pt30 text-center">
                  <script>

                         if (window.matchMedia("screen and (min-width: 40.063em)").matches){

                                 document.write (
                                     '<ins class="adsbygoogle" style="display:block" data-ad-client="ca-pub-0534207295672567" data-ad-slot="6740352234" data-ad-format="auto"></ins>'
                                    );
                                (adsbygoogle = window.adsbygoogle || []).push({});

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
                    <?php
                    if (!empty($similar[0]->internal_link)){
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
                                 document.write (
                                     '<ins class="adsbygoogle" style="display:blockstyle="display:inline-block;width:300px;height:250px"  data-ad-client="ca-pub-0534207295672567" data-ad-slot="3635121837"></ins>'
                                    );
                                (adsbygoogle = window.adsbygoogle || []).push({});
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

                 <?php if ($rating_detail){
                        echo "<div class='text-left pb-20'><pre>";
                        echo htmlentities(print_r($rating_detail,true));
                        echo"</pre></div>";
                       } ?>


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
 
 <script type="application/ld+json">
{
  "@context": "http://schema.org",
  "@type": "BreadcrumbList",
  "itemListElement": [{
    "@type": "ListItem",
    "position": 1,
    "item": {
      "@id": "https://example.com/topDaily/<?php echo $project->platform->name; ?>",
      "name": "<?php echo $project->platform->name; ?>"
    }
  },{
    "@type": "ListItem",
    "position": 2,
    "item": {
      "@id": "https://example.com/topDaily/<?php echo $project->platform->name; ?>/<?php echo $project->origCategory->name; ?>/<?php echo $project->origCategory->category->name; ?>",
      "name": "<?php echo $project->origCategory->category->name; ?>"
    }
  },{
    "@type": "ListItem",
    "position": 3,
    "item": {
      "@id": "https://example.com/topDaily/<?php echo $project->platform->name; ?>/<?php echo $project->origCategory->name; ?>",
      "name": "<?php echo $project->origCategory->name; ?>"
    }
  }]
}
<?php /* ?>
{
  "@context": "http://schema.org/",
  "@type": "Product",
  "name": "<?php echo $project->title; ?>",
  "image": "<?php echo Yii::app()->createAbsoluteUrl("feed/image", array("data" => $project->id)); ?>",
  "description": "<?php echo $project->description; ?>",
  "mpn": "<?php echo $project->id; ?>",
  "brand": {
    "@type": "Thing",
    "name": "<?php echo $project->creator; ?>"
  }
 <?php if ($project->rating){ ?>,
  "aggregateRating": {
    "@type": "AggregateRating",
    "ratingValue": "<?php echo round($project->rating,1); ?>",
    "reviewCount": "<?php if (isset($project->creator_backed)) echo $project->creator_backed+1; else echo "1"; ?>"
  }
 <?php } ?>
}
<?php */ ?>
</script>

 
<?php }else{ ?>
<div class="pt30 pb30">
      <div class="row">
        <div class="columns medium-12">
            <h4>This project has been removed.</h4>
        </div>
      </div>
    </div>
<?php } ?>