<?php
$this->pageTitle = 'How does your project stack to others';


?>

<div class="pb30">
  <div class="row">
    <div class="columns medium-12">
      <h3>How your project stacks to others</h3>
      <form method="post" id="checkproject_form" action="<?php echo Yii::app()->createUrl('site/owners'); ?>" data-abide>
        <p>
          Check how your project stacks against other projects listed on our site.
        </p>
        
        <div class="row">
          <div class="large-8 columns">
            <div class="row collapse">
              <div class="small-10 columns">
                <input type="text" name="link" value="" placeholder="Link to your campaign" required>
              </div>
              <div class="small-2 columns">
                <button trk="button_form_checkProject" type="submit" name="subscribe" class="button radius success postfix">Compare</button>
              </div>
            </div>
          </div>
        </div>        
        
      </form>
      
    </div>
  </div>
</div>

<div class="pt30 outro pb30">
  <div class="row">
    <div class="columns medium-12">
      <h2 class="white">Get noticed</h2>
    </div>
  </div>
</div>